<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Physical cash register / POS terminal / cash drawer at a location
        Schema::create('cash_registers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->restrictOnDelete();
            $t->string('code', 30);
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'code']);
            $t->index('location_id');
        });

        // A cashier opens a shift on one register (check "only one open shift per register" in code)
        Schema::create('cash_shifts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('cash_register_id')->constrained('cash_registers')->restrictOnDelete();
            $t->foreignId('cashier_id')->constrained('users')->restrictOnDelete();  // signed in with users.pos_pin
            $t->decimal('opening_cash', 12, 2)->default(0);
            $t->decimal('expected_cash', 12, 2)->nullable();   // opening + cash sales (void sales excluded)
            $t->decimal('closing_cash', 12, 2)->nullable();
            $t->decimal('difference', 12, 2)->nullable();
            $t->string('status', 20)->default('open');         // open | closed
            $t->text('note')->nullable();
            $t->timestamp('opened_at')->useCurrent();
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();

            $t->index(['cash_register_id', 'status']);
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            // stock is taken from this location; branch and register are reached via location / cash_shift
            $t->foreignId('location_id')->constrained('stock_locations')->restrictOnDelete();
            $t->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $t->foreignId('cash_shift_id')->nullable()->constrained('cash_shifts')->nullOnDelete();

            $t->string('invoice_no');
            $t->string('currency', 3)->default('USD');
            $t->decimal('exchange_rate', 14, 4)->default(1);   // sale currency -> client base currency

            $t->decimal('subtotal', 12, 2)->default(0);

            // order-level discount
            $t->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $t->string('discount_source', 20)->nullable();     // manual | promotion | customer | group
            $t->string('discount_type', 10)->nullable();       // percent | fixed
            $t->decimal('discount_value', 12, 2)->default(0);
            $t->decimal('discount_amount', 12, 2)->default(0);

            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);           // in sale currency
            $t->decimal('total_base', 14, 2)->default(0);      // total * exchange_rate (customer totals / reports)
            $t->decimal('paid_amount', 12, 2)->default(0);
            $t->decimal('change_amount', 12, 2)->default(0);

            $t->string('payment_status', 20)->default('unpaid'); // unpaid | partial | paid
            // hold | completed | void
            // void = cancelled: every sale_item goes back to stock (stock_movements type 'sale_void'),
            //        customer totals are reduced, and the sale is ignored in shift/cash reports
            $t->string('status', 20)->default('completed');
            $t->text('note')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();   // cashier
            $t->timestamp('sold_at')->useCurrent();

            // void info
            $t->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('void_reason')->nullable();
            $t->timestamp('voided_at')->nullable();
            $t->timestamps();

            $t->unique(['client_id', 'invoice_no']);
            $t->index(['client_id', 'status', 'sold_at']);
            $t->index(['location_id', 'sold_at']);
            $t->index(['customer_id', 'sold_at']);
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignUuid('sale_id')->constrained('sales')->cascadeOnDelete();
            $t->foreignUuid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $t->foreignUuid('variant_id')->nullable()->constrained('variants')->nullOnDelete();

            // snapshots, so old invoices don't change when a product is edited
            $t->string('product_name_en');
            $t->string('product_name_kh')->nullable();
            $t->string('variant_name_en')->nullable();
            $t->string('variant_name_kh')->nullable();
            $t->string('sku')->nullable();

            $t->integer('quantity');
            $t->decimal('unit_price', 12, 2);
            $t->decimal('unit_cost', 12, 2)->default(0);

            // line-level discount
            $t->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete();
            $t->string('discount_type', 10)->nullable();       // percent | fixed
            $t->decimal('discount_value', 12, 2)->default(0);
            $t->decimal('discount_amount', 12, 2)->default(0);

            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('line_total', 12, 2);
            $t->timestamps();

            $t->index('sale_id');
        });

        // A sale can be split across several payment types
        Schema::create('sale_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignUuid('sale_id')->constrained('sales')->cascadeOnDelete();
            $t->foreignId('payment_method_id')->constrained('payment_methods')->restrictOnDelete();
            $t->decimal('amount', 12, 2);                      // amount in paid currency
            $t->string('currency', 3)->default('USD');
            $t->decimal('exchange_rate', 14, 4)->default(1);
            $t->decimal('amount_base', 12, 2);                 // converted to sale currency
            $t->string('reference_no')->nullable();            // bank / KHQR transaction id
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('paid_at')->useCurrent();
            $t->timestamps();

            $t->index('sale_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_shifts');
        Schema::dropIfExists('cash_registers');
    }
};

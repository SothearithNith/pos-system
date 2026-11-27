<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('code', 30)->nullable();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('contact_person')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->string('address')->nullable();
            $t->string('tin_no')->nullable();
            $t->text('note')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'code']);
            $t->index(['client_id', 'phone']);
        });

        // Purchase order / goods receipt: buying stock from a supplier into one location
        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->restrictOnDelete(); // where goods arrive
            $t->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();

            $t->string('purchase_no');
            $t->string('supplier_invoice_no')->nullable();     // number on the supplier's own invoice
            $t->string('currency', 3)->default('USD');
            $t->decimal('exchange_rate', 14, 4)->default(1);   // purchase currency -> client base currency

            $t->decimal('subtotal', 12, 2)->default(0);
            $t->decimal('discount_amount', 12, 2)->default(0);
            $t->decimal('tax_amount', 12, 2)->default(0);
            $t->decimal('total', 12, 2)->default(0);
            $t->decimal('total_base', 14, 2)->default(0);
            $t->decimal('paid_amount', 12, 2)->default(0);

            $t->string('payment_status', 20)->default('unpaid'); // unpaid | partial | paid
            $t->string('status', 20)->default('draft');          // draft | ordered | partial | received | cancelled
            $t->text('note')->nullable();

            $t->timestamp('ordered_at')->nullable();
            $t->timestamp('received_at')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->unique(['client_id', 'purchase_no']);
            $t->index(['client_id', 'status']);
            $t->index(['supplier_id', 'created_at']);
        });

        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $t->foreignUuid('variant_id')->constrained('variants')->restrictOnDelete();
            $t->integer('quantity');                           // ordered
            $t->integer('received_qty')->default(0);           // allows partial receiving
            $t->decimal('unit_cost', 12, 2);
            $t->decimal('discount_amount', 12, 2)->default(0);
            $t->decimal('line_total', 12, 2);
            $t->timestamps();

            $t->unique(['purchase_id', 'variant_id']);
        });

        // Money paid to the supplier (can be several payments per purchase)
        Schema::create('purchase_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $t->foreignId('payment_method_id')->constrained('payment_methods')->restrictOnDelete();
            $t->decimal('amount', 12, 2);
            $t->string('currency', 3)->default('USD');
            $t->decimal('exchange_rate', 14, 4)->default(1);
            $t->decimal('amount_base', 12, 2);
            $t->string('reference_no')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('paid_at')->useCurrent();
            $t->timestamps();

            $t->index('purchase_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
    }
};

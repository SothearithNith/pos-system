<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('code', 30);                    // cash, khqr, aba, card, bank_transfer, credit
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('type', 30)->default('cash');   // cash | bank | qr | card | wallet | credit | other
            $t->boolean('is_cash')->default(false);    // counts toward cash drawer
            $t->boolean('is_default')->default(false);
            $t->integer('sort_order')->default(0);
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'code']);
        });

        // Membership levels: Regular, Silver, Gold, VIP ... each with an automatic discount
        Schema::create('customer_groups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('discount_type', 10)->nullable();            // percent | fixed
            $t->decimal('discount_value', 12, 2)->default(0);
            $t->decimal('min_total_spent', 14, 2)->default(0);      // auto-upgrade threshold (base currency)
            $t->boolean('is_default')->default(false);
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'name_en']);
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('customer_group_id')->nullable()->constrained('customer_groups')->nullOnDelete();

            $t->string('code', 30)->nullable();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->string('address')->nullable();
            $t->date('birthday')->nullable();
            $t->text('note')->nullable();

            // personal discount (overrides group discount when set)
            $t->string('discount_type', 10)->nullable();            // percent | fixed
            $t->decimal('discount_value', 12, 2)->default(0);

            // spending summary, base currency, net of returns.
            // Updated when a sale / void / exchange is saved; can always be rebuilt from sales + exchanges.
            $t->decimal('total_spent', 14, 2)->default(0);
            $t->unsignedInteger('total_orders')->default(0);
            $t->decimal('total_discount', 14, 2)->default(0);       // how much discount they received
            $t->timestamp('first_purchase_at')->nullable();
            $t->timestamp('last_purchase_at')->nullable();

            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'code']);
            $t->unique(['client_id', 'phone']);
            $t->index(['client_id', 'total_spent']);
        });

        // Promotions / coupons
        Schema::create('discounts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('code', 30)->nullable();                     // coupon code, NULL = automatic
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('type', 10);                                 // percent | fixed
            $t->decimal('value', 12, 2);
            $t->string('scope', 20)->default('order');              // order | product | category
            $t->foreignId('customer_group_id')->nullable()->constrained('customer_groups')->nullOnDelete(); // only this group
            $t->decimal('min_order_amount', 12, 2)->default(0);
            $t->decimal('max_discount_amount', 12, 2)->nullable();  // cap for percent discounts
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('ends_at')->nullable();
            $t->unsignedInteger('usage_limit')->nullable();
            $t->unsignedInteger('used_count')->default(0);
            $t->boolean('status')->default(true);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();

            $t->unique(['client_id', 'code']);
            $t->index(['client_id', 'status', 'starts_at', 'ends_at']);
        });

        Schema::create('discount_products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $t->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $t->unique(['discount_id', 'product_id']);
        });

        Schema::create('discount_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('discount_id')->constrained('discounts')->cascadeOnDelete();
            $t->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $t->unique(['discount_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_categories');
        Schema::dropIfExists('discount_products');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('payment_methods');
    }
};

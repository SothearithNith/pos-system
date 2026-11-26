<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'parent_id', 'name_en']);
        });

        Schema::create('brands', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('logo')->nullable();
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'name_en']);
        });

        Schema::create('products', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->text('description')->nullable();
            $t->string('image_url')->nullable();
            $t->decimal('base_price', 12, 2)->default(0);
            $t->boolean('status')->default(true);
            $t->timestamps();

            // unique per client (was globally unique before)
            $t->unique(['client_id', 'name_en']);
        });

        Schema::create('colors', function (Blueprint $t) {
            $t->id();
            $t->string('name_en')->unique();
            $t->string('name_kh')->nullable();
            $t->string('code', 20)->nullable();          // hex, e.g. #FF0000
            $t->boolean('status')->default(true);
            $t->timestamps();
        });

        Schema::create('sizes', function (Blueprint $t) {
            $t->id();
            $t->string('name_en')->unique();
            $t->string('name_kh')->nullable();
            $t->boolean('status')->default(true);
            $t->timestamps();
        });

        Schema::create('variants', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $t->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $t->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();

            $t->string('sku')->unique();
            $t->string('barcode')->nullable()->index();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->decimal('price', 12, 2);
            $t->decimal('cost_price', 12, 2)->default(0);   // default purchase cost
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['product_id', 'color_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};

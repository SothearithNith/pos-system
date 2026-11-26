<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $t) {
            $t->id();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('tin_no')->nullable()->unique();
            $t->string('contact')->nullable();
            $t->string('address')->nullable();
            $t->string('logo')->nullable();
            $t->string('base_currency', 3)->default('USD');   // currency used for reports / customer totals
            $t->boolean('status')->default(true);
            $t->unsignedBigInteger('created_by')->nullable();  // platform admin (users table is created later)
            $t->timestamps();
        });

        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('abbreviation', 20);
            $t->string('address')->nullable();
            $t->boolean('status')->default(true);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();

            // unique per client, not globally
            $t->unique(['client_id', 'name_en']);
            $t->unique(['client_id', 'abbreviation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
        Schema::dropIfExists('clients');
    }
};

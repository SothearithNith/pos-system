<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();

            // NULL = platform admin, not tied to any client
            $t->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();

            // FK to stock_locations is added in the stock migration (avoids circular dependency)
            $t->unsignedBigInteger('default_location_id')->nullable();

            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('username')->unique();
            $t->string('password');
            $t->string('pos_pin')->nullable();          // hashed PIN (Hash::make) used to sign in to the cash register
            $t->boolean('is_active')->default(true);

            // API tokens are stored by Laravel Sanctum (personal_access_tokens table)

            $t->rememberToken();
            $t->timestamps();

            $t->index('client_id');
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};

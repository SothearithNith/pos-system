<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Where stock physically lives. branch_id is NULL for clients without branches.
        Schema::create('stock_locations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('code', 30);
            $t->boolean('is_default')->default(false);
            $t->boolean('status')->default(true);
            $t->timestamps();

            $t->unique(['client_id', 'code']);
        });

        // users were created earlier, so the FK is added now
        Schema::table('users', function (Blueprint $t) {
            $t->foreign('default_location_id')->references('id')->on('stock_locations')->nullOnDelete();
        });

        // Extra locations a user may sell from / manage stock in
        Schema::create('user_locations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->cascadeOnDelete();
            $t->timestamps();

            $t->unique(['user_id', 'location_id']);
        });

        // Current quantity per location + variant
        Schema::create('stocks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->cascadeOnDelete();
            $t->foreignUuid('variant_id')->constrained('variants')->cascadeOnDelete();
            $t->integer('quantity')->default(0);
            $t->integer('min_quantity')->default(0);          // low-stock alert level
            $t->decimal('avg_cost', 12, 2)->default(0);
            $t->timestamps();

            $t->unique(['location_id', 'variant_id']);
            $t->index('client_id');
        });

        // Ledger: every change to stock is a row (quantity signed: + in, - out)
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->restrictOnDelete();
            $t->foreignUuid('variant_id')->constrained('variants')->restrictOnDelete();
            // opening | purchase | sale | sale_void | adjustment
            // transfer_in | transfer_out | stock_check
            $t->string('type', 30);
            $t->integer('quantity');
            $t->integer('balance_after');
            $t->decimal('unit_cost', 12, 2)->nullable();
            $t->string('reference_type', 50)->nullable();     // sales, stock_checks, stock_transfers
            $t->string('reference_id', 36)->nullable();
            $t->string('note')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->index(['location_id', 'variant_id']);
            $t->index(['reference_type', 'reference_id']);
            $t->index(['client_id', 'type']);
        });

        Schema::create('stock_checks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('stock_locations')->cascadeOnDelete();
            $t->string('check_no');
            $t->string('status', 20)->default('draft');       // draft | counting | completed | cancelled
            $t->text('note')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();

            $t->unique(['client_id', 'check_no']);
        });

        Schema::create('stock_check_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stock_check_id')->constrained('stock_checks')->cascadeOnDelete();
            $t->foreignUuid('variant_id')->constrained('variants')->cascadeOnDelete();
            $t->integer('system_qty')->default(0);            // snapshot when check started
            $t->integer('counted_qty')->nullable();
            $t->integer('difference_qty')->default(0);        // counted - system
            $t->string('note')->nullable();
            $t->timestamps();

            $t->unique(['stock_check_id', 'variant_id']);
        });

        Schema::create('stock_transfers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $t->string('transfer_no');
            $t->foreignId('from_location_id')->constrained('stock_locations')->cascadeOnDelete();
            $t->foreignId('to_location_id')->constrained('stock_locations')->cascadeOnDelete();
            $t->string('status', 20)->default('pending');     // pending | in_transit | received | cancelled
            $t->text('note')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('received_at')->nullable();
            $t->timestamps();

            $t->unique(['client_id', 'transfer_no']);
        });

        Schema::create('stock_transfer_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $t->foreignUuid('variant_id')->constrained('variants')->cascadeOnDelete();
            $t->integer('quantity');
            $t->timestamps();

            $t->unique(['stock_transfer_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_check_items');
        Schema::dropIfExists('stock_checks');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('user_locations');

        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['default_location_id']);
        });
        Schema::dropIfExists('stock_locations');
    }
};

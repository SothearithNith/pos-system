
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sidebar menu. Each menu item can require one permission (Spatie), e.g. "products.view".
        Schema::create('menus', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $t->string('slug')->unique();
            $t->string('name_en');
            $t->string('name_kh')->nullable();
            $t->string('icon')->nullable();                     // bootstrap icon class
            $t->string('route_name')->nullable();               // NULL for a group that only holds children
            $t->string('permission_name')->nullable();          // used to create the Spatie permission
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};

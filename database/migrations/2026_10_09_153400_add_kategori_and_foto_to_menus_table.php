<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'kategori')) {
                $table->string('kategori')->default('makanan')->after('nama_menu');
            }
            if (!Schema::hasColumn('menus', 'foto_menu')) {
                $table->string('foto_menu')->nullable()->after('stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'kategori')) {
                $table->dropColumn('kategori');
            }
            if (Schema::hasColumn('menus', 'foto_menu')) {
                $table->dropColumn('foto_menu');
            }
        });
    }
};

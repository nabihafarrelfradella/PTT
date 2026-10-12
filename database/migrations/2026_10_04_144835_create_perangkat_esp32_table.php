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
        if (!Schema::hasTable('perangkat_esp32')) {
            Schema::create('perangkat_esp32', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('merchant_id')->nullable()->constrained('kantins')->onDelete('set null');
                $table->string('device_id')->unique();
                $table->string('nama_perangkat');
                $table->string('lokasi');
                $table->string('status')->default('online');
                $table->timestamp('last_ping')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'perangkat_id')) {
                    $table->foreignId('perangkat_id')->nullable()->after('kantin_id')->constrained('perangkat_esp32')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'perangkat_id')) {
                    $table->dropForeign(['perangkat_id']);
                    $table->dropColumn('perangkat_id');
                }
            });
        }

        Schema::dropIfExists('perangkat_esp32');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('debit_accounts')) {
            Schema::table('debit_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('debit_accounts', 'card_token')) {
                    $table->string('card_token')->nullable()->after('card_token_masked');
                }
            });
        }

        if (Schema::hasTable('biometrics')) {
            Schema::table('biometrics', function (Blueprint $table) {
                if (!Schema::hasColumn('biometrics', 'fingerprint_id')) {
                    $table->unsignedInteger('fingerprint_id')->nullable()->unique()->after('fingerprint_hash');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('debit_accounts')) {
            Schema::table('debit_accounts', function (Blueprint $table) {
                if (Schema::hasColumn('debit_accounts', 'card_token')) {
                    $table->dropColumn('card_token');
                }
            });
        }

        if (Schema::hasTable('biometrics')) {
            Schema::table('biometrics', function (Blueprint $table) {
                if (Schema::hasColumn('biometrics', 'fingerprint_id')) {
                    $table->dropColumn('fingerprint_id');
                }
            });
        }
    }
};

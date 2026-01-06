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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0)->after('comment');
            }
            if (!Schema::hasColumn('users', 'withdrawal_methods')) {
                $table->json('withdrawal_methods')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('users', 'minimum_withdrawal_limit')) {
                $table->decimal('minimum_withdrawal_limit', 10, 2)->nullable()->after('withdrawal_methods');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'minimum_withdrawal_limit')) {
                $table->dropColumn('minimum_withdrawal_limit');
            }
            if (Schema::hasColumn('users', 'withdrawal_methods')) {
                $table->dropColumn('withdrawal_methods');
            }
            if (Schema::hasColumn('users', 'balance')) {
                $table->dropColumn('balance');
            }
        });
    }
};




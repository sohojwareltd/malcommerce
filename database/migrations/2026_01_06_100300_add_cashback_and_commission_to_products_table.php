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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'cashback_amount')) {
                $table->decimal('cashback_amount', 10, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'commission_type')) {
                $table->string('commission_type', 20)->default('fixed')->after('cashback_amount'); // fixed, percent
            }
            if (!Schema::hasColumn('products', 'commission_value')) {
                $table->decimal('commission_value', 10, 2)->default(0)->after('commission_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'commission_value')) {
                $table->dropColumn('commission_value');
            }
            if (Schema::hasColumn('products', 'commission_type')) {
                $table->dropColumn('commission_type');
            }
            if (Schema::hasColumn('products', 'cashback_amount')) {
                $table->dropColumn('cashback_amount');
            }
        });
    }
};




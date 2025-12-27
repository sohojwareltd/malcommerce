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
        Schema::table('orders', function (Blueprint $table) {
            // Make address fields nullable (except address which will be the main field)
            $table->string('customer_email')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('upazila')->nullable()->change();
            $table->string('city_village')->nullable()->change();
            $table->string('post_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Note: Cannot easily revert nullable back to required without data loss
            // This is a one-way migration
            $table->string('customer_email')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('upazila')->nullable(false)->change();
            $table->string('city_village')->nullable(false)->change();
            $table->string('post_code')->nullable(false)->change();
        });
    }
};

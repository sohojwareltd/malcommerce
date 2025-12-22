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
            $table->enum('role', ['admin', 'sponsor', 'customer'])->default('customer')->after('email');
            $table->string('affiliate_code')->unique()->nullable()->after('role');
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete()->after('affiliate_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sponsor_id']);
            $table->dropColumn(['role', 'affiliate_code', 'sponsor_id']);
        });
    }
};

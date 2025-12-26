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
            // Make email nullable (phone-based auth)
            $table->string('email')->nullable()->change();
            
            // Make password nullable (OTP-based auth)
            $table->string('password')->nullable()->change();
            
            // Add phone column (unique, nullable for backward compatibility)
            $table->string('phone')->unique()->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            // Note: We can't easily revert email/password nullable changes
            // You may need to manually update existing records
        });
    }
};

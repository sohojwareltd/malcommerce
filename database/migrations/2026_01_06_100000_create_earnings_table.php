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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referral_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('earning_type', 50)->index(); // cashback, referral, dividend, etc.
            $table->string('comment')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_revenue', 10, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('sponsor_id');
            $table->index('referral_id');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};




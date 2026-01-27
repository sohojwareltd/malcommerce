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
            $table->enum('payment_method', ['cod', 'bkash'])->default('cod')->after('status');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending')->after('payment_method');
            $table->string('payment_transaction_id')->nullable()->after('payment_status');
            $table->string('payment_invoice_id')->nullable()->after('payment_transaction_id');
            $table->text('payment_response')->nullable()->after('payment_invoice_id');
            $table->timestamp('payment_completed_at')->nullable()->after('payment_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_transaction_id',
                'payment_invoice_id',
                'payment_response',
                'payment_completed_at',
            ]);
        });
    }
};

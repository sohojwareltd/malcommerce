<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('steadfast_consignment_id')->nullable()->after('payment_completed_at');
            $table->string('steadfast_tracking_code', 50)->nullable()->after('steadfast_consignment_id');
            $table->string('steadfast_delivery_status', 50)->nullable()->after('steadfast_tracking_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['steadfast_consignment_id', 'steadfast_tracking_code', 'steadfast_delivery_status']);
        });
    }
};

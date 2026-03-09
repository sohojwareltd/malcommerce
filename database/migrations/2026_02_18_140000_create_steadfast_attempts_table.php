<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steadfast_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // webhook, create_order, get_status, get_balance
            $table->string('notification_type', 50)->nullable(); // delivery_status, tracking_update (for webhooks)
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('success')->default(false);
            $table->integer('http_status')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('error_message')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steadfast_attempts');
    }
};

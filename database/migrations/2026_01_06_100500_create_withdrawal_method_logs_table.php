<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_method_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('method_key')->nullable();
            $table->string('action', 20); // created, updated, deleted
            $table->json('payload')->nullable(); // snapshot of method data
            $table->timestamps();

            $table->index(['user_id', 'method_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_method_logs');
    }
};




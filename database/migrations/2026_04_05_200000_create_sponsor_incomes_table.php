<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('earning_id')->nullable()->unique()->constrained('earnings')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('category', 255);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sponsor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_incomes');
    }
};

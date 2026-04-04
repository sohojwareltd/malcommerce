<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by_sponsor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('beneficiary_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('kind', 20)->index(); // own | team
            $table->decimal('amount', 12, 2);
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending | accepted | canceled
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('earning_id')->nullable()->constrained('earnings')->nullOnDelete();
            $table->timestamps();

            $table->index(['submitted_by_sponsor_id', 'status']);
            $table->index(['beneficiary_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

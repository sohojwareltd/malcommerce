<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->foreignId('venue_id')->nullable()->after('description')->constrained()->nullOnDelete();
            $table->boolean('show_phone')->default(true)->after('sort_order');
            $table->boolean('show_address')->default(true)->after('show_phone');
            $table->boolean('show_notes')->default(true)->after('show_address');
        });

        Schema::create('workshop_seminar_trade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_seminar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trade_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['workshop_seminar_id', 'trade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_seminar_trade');
        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropColumn(['show_phone', 'show_address', 'show_notes']);
        });
    }
};

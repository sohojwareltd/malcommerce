<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsor_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('rank')->unique()->comment('0 = top anchor; larger = deeper');
            $table->decimal('commission_percent', 6, 2)->default(0);
            $table->boolean('is_default_for_new')->default(false);
            $table->timestamps();
        });

        $now = now();
        DB::table('sponsor_levels')->insert([
            [
                'name' => 'Apex',
                'rank' => 0,
                'commission_percent' => 0,
                'is_default_for_new' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Member',
                'rank' => 1000,
                'commission_percent' => 10,
                'is_default_for_new' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_levels');
    }
};

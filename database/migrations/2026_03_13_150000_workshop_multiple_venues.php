<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_seminar_venue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_seminar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['workshop_seminar_id', 'venue_id']);
        });

        if (Schema::hasColumn('workshop_seminars', 'venue_id')) {
            $rows = \DB::table('workshop_seminars')->whereNotNull('venue_id')->get(['id', 'venue_id']);
            foreach ($rows as $row) {
                \DB::table('workshop_seminar_venue')->insert([
                    'workshop_seminar_id' => $row->id,
                    'venue_id' => $row->venue_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Schema::table('workshop_seminars', function (Blueprint $table) {
                $table->dropForeign(['venue_id']);
                $table->dropColumn('venue_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('workshop_seminars', 'venue_id')) {
            Schema::table('workshop_seminars', function (Blueprint $table) {
                $table->foreignId('venue_id')->nullable()->after('description')->constrained()->nullOnDelete();
            });
            $pivots = \DB::table('workshop_seminar_venue')->orderBy('id')->get();
            foreach ($pivots as $p) {
                \DB::table('workshop_seminars')->where('id', $p->workshop_seminar_id)->update(['venue_id' => $p->venue_id]);
            }
        }
        Schema::dropIfExists('workshop_seminar_venue');
    }
};

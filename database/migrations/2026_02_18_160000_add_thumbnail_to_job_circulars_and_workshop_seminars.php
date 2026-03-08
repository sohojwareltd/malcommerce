<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('slug');
        });

        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });

        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};

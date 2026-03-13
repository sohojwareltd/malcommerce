<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->json('education_options')->nullable()->after('requirements');
            $table->json('experience_options')->nullable()->after('education_options');
        });
    }

    public function down(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->dropColumn(['education_options', 'experience_options']);
        });
    }
};


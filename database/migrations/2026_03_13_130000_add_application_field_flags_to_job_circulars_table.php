<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->boolean('show_email')->default(true)->after('experience_options');
            $table->boolean('show_address')->default(true)->after('show_email');
            $table->boolean('show_date_of_birth')->default(true)->after('show_address');
            $table->boolean('show_gender')->default(true)->after('show_date_of_birth');
            $table->boolean('show_education')->default(true)->after('show_gender');
            $table->boolean('show_experience')->default(true)->after('show_education');
            $table->boolean('show_resume')->default(true)->after('show_experience');
        });
    }

    public function down(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->dropColumn([
                'show_email',
                'show_address',
                'show_date_of_birth',
                'show_gender',
                'show_education',
                'show_experience',
                'show_resume',
            ]);
        });
    }
};


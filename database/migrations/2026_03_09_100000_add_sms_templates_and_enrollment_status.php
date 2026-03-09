<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->json('sms_templates')->nullable()->after('sort_order');
        });

        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->json('sms_templates')->nullable()->after('sort_order');
        });

        Schema::table('workshop_enrollments', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('job_circulars', function (Blueprint $table) {
            $table->dropColumn('sms_templates');
        });

        Schema::table('workshop_seminars', function (Blueprint $table) {
            $table->dropColumn('sms_templates');
        });

        Schema::table('workshop_enrollments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

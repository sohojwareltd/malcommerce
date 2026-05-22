<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digital_courses', function (Blueprint $table) {
            $table->json('sms_templates')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('digital_courses', function (Blueprint $table) {
            $table->dropColumn('sms_templates');
        });
    }
};

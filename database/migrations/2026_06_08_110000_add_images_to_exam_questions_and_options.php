<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->string('image')->nullable()->after('question_text');
        });

        Schema::table('exam_question_options', function (Blueprint $table) {
            $table->string('image')->nullable()->after('option_text');
        });
    }

    public function down(): void
    {
        Schema::table('exam_question_options', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('approval_text')->nullable();
            $table->string('established_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('institute_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('registration_number')->nullable();
            $table->string('roll_number')->nullable();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->foreignId('institute_id')->constrained();
            $table->foreignId('institute_course_id')->constrained();
            $table->string('session')->nullable();
            $table->string('student_thana')->nullable();
            $table->string('student_district')->nullable();
            $table->enum('examinee_type', ['regular', 'irregular'])->default('regular');
            $table->string('photo')->nullable();
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->string('letter_grade', 5)->nullable();
            $table->string('exam_month')->nullable();
            $table->date('issue_date')->nullable();
            $table->unsignedSmallInteger('marks_written')->nullable();
            $table->unsignedSmallInteger('marks_internship')->nullable();
            $table->unsignedSmallInteger('marks_viva')->nullable();
            $table->unsignedSmallInteger('marks_total')->nullable();
            $table->unsignedSmallInteger('marks_full')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('institute_courses');
        Schema::dropIfExists('institutes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        if (Schema::hasTable('institute_courses')) {
            $titles = DB::table('institute_courses')->pluck('name')->unique()->filter();

            foreach ($titles as $title) {
                DB::table('student_courses')->insert([
                    'title' => $title,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('student_course_id')->nullable()->after('institute_id')->constrained();
            $table->string('course_duration')->nullable()->after('session');
        });

        if (Schema::hasTable('institute_courses')) {
            $instituteCourses = DB::table('institute_courses')->get();

            foreach ($instituteCourses as $instituteCourse) {
                $studentCourseId = DB::table('student_courses')->where('title', $instituteCourse->name)->value('id');

                if (!$studentCourseId) {
                    $studentCourseId = DB::table('student_courses')->insertGetId([
                        'title' => $instituteCourse->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('students')
                    ->where('institute_course_id', $instituteCourse->id)
                    ->update([
                        'student_course_id' => $studentCourseId,
                        'course_duration' => $instituteCourse->duration,
                    ]);
            }
        }

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institute_course_id');
        });

        Schema::dropIfExists('institute_courses');
    }

    public function down(): void
    {
        Schema::create('institute_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('institute_course_id')->nullable()->after('institute_id')->constrained();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_course_id');
            $table->dropColumn('course_duration');
        });

        Schema::dropIfExists('student_courses');
    }
};

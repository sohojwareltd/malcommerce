<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('access_type', ['free', 'paid', 'code', 'paid_or_code'])->default('free');
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedTinyInteger('pass_mark')->default(60);
            $table->unsignedTinyInteger('max_exam_attempts')->default(3);
            $table->unsignedTinyInteger('max_code_attempts')->default(6);
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('certificate_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('points')->default(1);
            $table->timestamps();
        });

        Schema::create('exam_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_question_id')->constrained()->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('payment_method')->default('bkash');
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_invoice_id')->nullable();
            $table->text('payment_response')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_access_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->string('code', 6);
            $table->unsignedInteger('max_uses')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['exam_id', 'code']);
        });

        Schema::create('exam_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->enum('access_type', ['free', 'purchase', 'code', 'admin'])->default('free');
            $table->foreignId('exam_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('exam_access_code_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('granted_at');
            $table->timestamps();

            $table->unique(['user_id', 'exam_id']);
        });

        Schema::create('exam_code_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('wrong_attempts')->default(0);
            $table->timestamp('banned_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'exam_id']);
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_enrollment_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_answers')->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->enum('status', ['in_progress', 'completed', 'expired'])->default('in_progress');
            $table->timestamps();
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['exam_attempt_id', 'exam_question_id']);
        });

        Schema::create('exam_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('exam_title');
            $table->decimal('score', 5, 2);
            $table->unsignedTinyInteger('pass_mark');
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_certificates');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_code_attempts');
        Schema::dropIfExists('exam_enrollments');
        Schema::dropIfExists('exam_access_codes');
        Schema::dropIfExists('exam_orders');
        Schema::dropIfExists('exam_question_options');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('digital_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('digital_course_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_youtube_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('digital_course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('youtube_url', 500);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('digital_course_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('digital_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->enum('payment_method', ['bkash'])->default('bkash');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_invoice_id')->nullable();
            $table->text('payment_response')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->foreignId('sponsor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('payment_transaction_id');
        });

        Schema::create('digital_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('digital_course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('digital_course_order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('granted_at');
            $table->timestamps();
            $table->unique(['user_id', 'digital_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_course_enrollments');
        Schema::dropIfExists('digital_course_orders');
        Schema::dropIfExists('digital_course_lessons');
        Schema::dropIfExists('digital_courses');
        Schema::dropIfExists('digital_course_categories');
    }
};

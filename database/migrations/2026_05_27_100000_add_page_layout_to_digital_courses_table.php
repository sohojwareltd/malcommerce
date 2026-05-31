<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digital_courses', function (Blueprint $table) {
            $table->json('page_layout')->nullable()->after('sort_order');
            $table->string('checkout_form_title')->nullable()->after('page_layout');
            $table->string('checkout_button_text')->nullable()->after('checkout_form_title');
        });
    }

    public function down(): void
    {
        Schema::table('digital_courses', function (Blueprint $table) {
            $table->dropColumn(['page_layout', 'checkout_form_title', 'checkout_button_text']);
        });
    }
};

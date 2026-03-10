<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('digital_content_type', 20)->nullable()->after('is_digital'); // 'file' or 'link'
            $table->string('digital_file_path')->nullable()->after('digital_content_type');
            $table->text('digital_link_text')->nullable()->after('digital_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['digital_content_type', 'digital_file_path', 'digital_link_text']);
        });
    }
};

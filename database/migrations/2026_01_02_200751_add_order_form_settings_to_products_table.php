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
            $table->string('order_form_title')->nullable()->after('sort_order');
            $table->string('order_button_text')->nullable()->after('order_form_title');
            $table->decimal('order_min_amount', 10, 2)->nullable()->default(0)->after('order_button_text');
            $table->decimal('order_max_amount', 10, 2)->nullable()->default(0)->after('order_min_amount');
            $table->decimal('order_custom_charge', 10, 2)->nullable()->default(0)->after('order_max_amount');
            $table->text('order_delivery_options')->nullable()->after('order_custom_charge');
            $table->boolean('order_hide_summary')->default(false)->after('order_delivery_options');
            $table->boolean('order_hide_quantity')->default(false)->after('order_hide_summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'order_form_title',
                'order_button_text',
                'order_min_amount',
                'order_max_amount',
                'order_custom_charge',
                'order_delivery_options',
                'order_hide_summary',
                'order_hide_quantity',
            ]);
        });
    }
};

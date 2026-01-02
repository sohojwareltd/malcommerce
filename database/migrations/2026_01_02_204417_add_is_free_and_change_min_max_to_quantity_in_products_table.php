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
        // Add is_free field if it doesn't exist
        if (!Schema::hasColumn('products', 'is_free')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_free')->default(false)->after('price');
            });
        }
        
        // Add new quantity columns if they don't exist
        if (!Schema::hasColumn('products', 'order_min_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('order_min_quantity')->nullable()->default(0)->after('order_button_text');
            });
        }
        
        if (!Schema::hasColumn('products', 'order_max_quantity')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('order_max_quantity')->nullable()->default(0)->after('order_min_quantity');
            });
        }
        
        // Migrate existing data from amount to quantity if old columns exist
        if (Schema::hasColumn('products', 'order_min_amount') && Schema::hasColumn('products', 'order_max_amount')) {
            \DB::statement('UPDATE products SET order_min_quantity = CAST(order_min_amount AS UNSIGNED) WHERE order_min_amount IS NOT NULL');
            \DB::statement('UPDATE products SET order_max_quantity = CAST(order_max_amount AS UNSIGNED) WHERE order_max_amount IS NOT NULL');
            
            // Drop old columns
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['order_min_amount', 'order_max_amount']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Re-add old columns
            $table->decimal('order_min_amount', 10, 2)->nullable()->default(0)->after('order_button_text');
            $table->decimal('order_max_amount', 10, 2)->nullable()->default(0)->after('order_min_amount');
            
            // Drop new columns
            $table->dropColumn(['is_free', 'order_min_quantity', 'order_max_quantity']);
        });
    }
};

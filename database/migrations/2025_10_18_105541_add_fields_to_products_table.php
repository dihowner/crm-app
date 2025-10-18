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
            $table->string('weight')->nullable()->after('category');
            $table->string('dimensions')->nullable()->after('weight');
            $table->integer('stock_quantity')->default(0)->after('dimensions');
            $table->integer('low_stock_threshold')->default(10)->after('stock_quantity');
            $table->boolean('is_active')->default(true)->after('low_stock_threshold');

            // Remove the old status column if it exists
            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'dimensions', 'stock_quantity', 'low_stock_threshold', 'is_active']);
            $table->string('status')->default('active');
        });
    }
};

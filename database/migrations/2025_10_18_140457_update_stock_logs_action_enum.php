<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records to use only allowed actions
        DB::statement("UPDATE stock_logs SET action = 'Add Stock' WHERE action NOT IN ('Add Stock', 'Manual Adjustment')");

        // Alter the enum to only include the two allowed actions
        DB::statement("ALTER TABLE stock_logs MODIFY COLUMN action ENUM('Add Stock', 'Manual Adjustment') DEFAULT 'Add Stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum with all actions
        DB::statement("ALTER TABLE stock_logs MODIFY COLUMN action ENUM('Add Stock', 'Manual Adjustment', 'Order Delivered', 'Stock Transfer', 'Return', 'Damaged', 'Expired') DEFAULT 'Add Stock'");
    }
};

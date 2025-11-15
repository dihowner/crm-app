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
        // Alter the ENUM column to add 'failed' status
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'scheduled', 'not_picking_calls', 'number_off', 'call_back', 'delivered', 'cancelled', 'failed') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'failed' from the ENUM column
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'scheduled', 'not_picking_calls', 'number_off', 'call_back', 'delivered', 'cancelled') DEFAULT 'new'");
    }
};

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
        // Modify the enum to include all SMS types
        DB::statement("ALTER TABLE sms_records MODIFY COLUMN type ENUM('single', 'bulk', 'order_confirmation', 'order_reminder', 'delivery_update', 'delivery_notification', 'marketing', 'marketing_promotion') DEFAULT 'single'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE sms_records MODIFY COLUMN type ENUM('single', 'bulk', 'order_confirmation', 'delivery_update', 'marketing') DEFAULT 'single'");
    }
};


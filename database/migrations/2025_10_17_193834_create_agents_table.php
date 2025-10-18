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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Commission percentage
            $table->timestamps();
        });

        // Insert default agents
        DB::table('agents')->insert([
            [
                'name' => 'Fresh Delivery',
                'email' => 'fresh@delivery.com',
                'phone' => '+2341234567890',
                'company_name' => 'Fresh Delivery Services',
                'address' => 'Lagos, Nigeria',
                'status' => 'active',
                'commission_rate' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Optimal Logistics',
                'email' => 'info@optimallogistics.com',
                'phone' => '+2341234567891',
                'company_name' => 'Optimal Logistics Ltd',
                'address' => 'Abuja, Nigeria',
                'status' => 'active',
                'commission_rate' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Henry Logistics',
                'email' => 'henry@logistics.com',
                'phone' => '+2341234567892',
                'company_name' => 'Henry Logistics Services',
                'address' => 'Port Harcourt, Nigeria',
                'status' => 'active',
                'commission_rate' => 6.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};

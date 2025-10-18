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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Admin, CSR, Logistic Manager
            $table->string('slug')->unique(); // admin, csr, logistic_manager
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Store permissions as JSON
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full access to all CRM features',
                'permissions' => json_encode([
                    'dashboard', 'orders', 'customers', 'products', 'agents',
                    'inventory', 'payments', 'sms_marketing', 'stats', 'staff_performance', 'admin_panel'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CSR',
                'slug' => 'csr',
                'description' => 'Customer Service Representative - Limited access',
                'permissions' => json_encode([
                    'dashboard', 'orders', 'customers', 'add_orders', 'payments', 'sms_marketing'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Logistic Manager',
                'slug' => 'logistic_manager',
                'description' => 'Manages inventory and logistics',
                'permissions' => json_encode([
                    'inventory'
                ]),
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
        Schema::dropIfExists('roles');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('sku')->unique()->nullable();
            $table->string('category')->nullable();
            $table->text('image_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Insert sample products
        DB::table('products')->insert([
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Premium running shoes with responsive cushioning',
                'price' => 65000.00,
                'sku' => 'ADU22-001',
                'category' => 'Footwear',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bose QuietComfort Earbuds',
                'description' => 'Wireless noise-cancelling earbuds',
                'price' => 45000.00,
                'sku' => 'BQE-001',
                'category' => 'Electronics',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultra-thin laptop with premium build quality',
                'price' => 750000.00,
                'sku' => 'DXP13-001',
                'category' => 'Computers',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PlayStation 5',
                'description' => 'Next-generation gaming console',
                'price' => 250000.00,
                'sku' => 'PS5-001',
                'category' => 'Gaming',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with advanced camera system',
                'price' => 850000.00,
                'sku' => 'IP15PM-001',
                'category' => 'Smartphones',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Lightweight laptop with M3 chip',
                'price' => 950000.00,
                'sku' => 'MBA-M3-001',
                'category' => 'Computers',
                'status' => 'active',
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
        Schema::dropIfExists('products');
    }
};

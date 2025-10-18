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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10); // Alert when stock goes below this
            $table->decimal('cost_price', 10, 2)->nullable(); // Cost price for the agent
            $table->decimal('selling_price', 10, 2)->nullable(); // Selling price for the agent
            $table->timestamps();

            // Ensure unique combination of product and agent
            $table->unique(['product_id', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};

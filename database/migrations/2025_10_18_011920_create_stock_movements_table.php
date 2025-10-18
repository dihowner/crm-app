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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // in = stock added, out = stock reduced
            $table->integer('quantity');
            $table->string('reason')->nullable(); // Manual addition, Order delivered, etc.
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('performed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

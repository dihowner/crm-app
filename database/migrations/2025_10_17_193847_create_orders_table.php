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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // CSR assigned
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null'); // Delivery agent
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', [
                'new',
                'scheduled',
                'not_picking_calls',
                'number_off',
                'call_back',
                'delivered',
                'cancelled'
            ])->default('new');
            $table->timestamp('scheduled_delivery_date')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('callback_reminder')->nullable(); // For "not_picking_calls" status
            $table->enum('payment_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

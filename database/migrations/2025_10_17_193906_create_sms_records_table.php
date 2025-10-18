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
        Schema::create('sms_records', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name')->nullable(); // For bulk SMS campaigns
            $table->enum('type', ['single', 'bulk', 'order_confirmation', 'delivery_update', 'marketing'])->default('single');
            $table->text('message');
            $table->string('recipient_phone');
            $table->string('recipient_name')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->string('sms_provider')->nullable(); // SMS service provider used
            $table->string('provider_message_id')->nullable(); // Provider's message ID
            $table->text('error_message')->nullable(); // Error details if failed
            $table->decimal('cost', 8, 2)->default(0.00); // Cost per SMS
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // If related to an order
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null'); // If sent to specific customer
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_records');
    }
};

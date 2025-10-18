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
        Schema::create('product_forms', function (Blueprint $table) {
            $table->id();
            $table->string('form_name');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('redirect_url')->default('https://domain.com');
            $table->string('button_text')->default('Place Order');
            $table->json('packages'); // Store package data as JSON
            $table->text('generated_form')->nullable(); // Store the generated HTML form
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_forms');
    }
};

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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // general, inventory, orders, users, payment, security, integration, ui
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, number, boolean, select, file, email, url
            $table->json('options')->nullable(); // For select fields
            $table->boolean('is_required')->default(false);
            $table->boolean('is_public')->default(false); // Whether it can be accessed by non-admin users
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};

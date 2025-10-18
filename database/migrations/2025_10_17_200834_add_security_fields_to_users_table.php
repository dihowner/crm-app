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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->string('two_factor_secret')->nullable()->after('locked_until');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            $table->timestamp('password_changed_at')->nullable()->after('two_factor_enabled');
            $table->boolean('must_change_password')->default(false)->after('password_changed_at');
            $table->json('login_history')->nullable()->after('must_change_password'); // Store last 10 logins
            $table->boolean('is_active')->default(true)->after('login_history');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'last_login_ip',
                'failed_login_attempts',
                'locked_until',
                'two_factor_secret',
                'two_factor_enabled',
                'password_changed_at',
                'must_change_password',
                'login_history',
                'is_active'
            ]);
        });
    }
};

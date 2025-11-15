<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingsSimplifiedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // App Name
        AppSetting::updateOrCreate(
            ['key' => 'app_name'],
            [
                'category' => 'general',
                'label' => 'Application Name',
                'description' => 'The name displayed throughout the application',
                'value' => 'AfroWellness',
                'type' => 'text',
                'is_required' => true,
                'is_public' => true,
                'sort_order' => 1,
            ]
        );

        // Theme Color
        AppSetting::updateOrCreate(
            ['key' => 'theme_color'],
            [
                'category' => 'ui',
                'label' => 'Theme Color',
                'description' => 'Primary theme color for the application',
                'value' => 'theme-blue',
                'type' => 'select',
                'options' => [
                    'theme-blue' => 'Blue Theme',
                    'theme-purple' => 'Purple Theme',
                    'theme-green' => 'Green Theme',
                    'theme-red' => 'Red Theme',
                    'theme-orange' => 'Orange Theme',
                ],
                'is_required' => true,
                'is_public' => true,
                'sort_order' => 2,
            ]
        );

        // Email Configuration (stored as JSON)
        AppSetting::updateOrCreate(
            ['key' => 'email_config'],
            [
                'category' => 'integration',
                'label' => 'Email API Configuration',
                'description' => 'Email service configuration (SMTP, Mailgun, SES, SendGrid)',
                'value' => json_encode([
                    'driver' => 'smtp',
                    'host' => '',
                    'port' => '587',
                    'username' => '',
                    'password' => '',
                    'encryption' => 'tls',
                    'from_address' => '',
                    'from_name' => '',
                ]),
                'type' => 'textarea',
                'is_required' => false,
                'is_public' => false,
                'sort_order' => 3,
            ]
        );

        // SMS Configuration (stored as JSON)
        AppSetting::updateOrCreate(
            ['key' => 'sms_config'],
            [
                'category' => 'integration',
                'label' => 'SMS API Configuration',
                'description' => 'SMS service configuration',
                'value' => json_encode([
                    'api_url' => 'http://sms.paynex.org/api/sendsms.php',
                    'auth_token' => '',
                    'sender_id' => 'PEAKSYSTEMS',
                    'product_id' => 'dndroute',
                ]),
                'type' => 'textarea',
                'is_required' => false,
                'is_public' => false,
                'sort_order' => 4,
            ]
        );

        // Clear cache
        AppSetting::clearCache();
    }
}

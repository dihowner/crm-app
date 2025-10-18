<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'category' => 'general',
                'key' => 'app_name',
                'label' => 'Application Name',
                'description' => 'The name displayed throughout the application',
                'value' => 'CRM System',
                'type' => 'text',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'general',
                'key' => 'company_phone',
                'label' => 'Company Phone',
                'description' => 'Your company phone number',
                'value' => '',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'category' => 'general',
                'key' => 'company_email',
                'label' => 'Company Email',
                'description' => 'Your company email address',
                'value' => '',
                'type' => 'email',
                'sort_order' => 3,
            ],
            [
                'category' => 'general',
                'key' => 'default_currency',
                'label' => 'Default Currency',
                'description' => 'The primary currency used in the application',
                'value' => '₦',
                'type' => 'select',
                'options' => ['₦' => 'Nigerian Naira (₦)', '$' => 'US Dollar ($)', '€' => 'Euro (€)', '£' => 'British Pound (£)'],
                'sort_order' => 4,
            ],
            [
                'category' => 'general',
                'key' => 'date_format',
                'label' => 'Date Format',
                'description' => 'How dates should be displayed',
                'value' => 'DD/MM/YYYY',
                'type' => 'select',
                'options' => ['DD/MM/YYYY' => 'DD/MM/YYYY', 'MM/DD/YYYY' => 'MM/DD/YYYY', 'YYYY-MM-DD' => 'YYYY-MM-DD'],
                'sort_order' => 5,
            ],
            [
                'category' => 'general',
                'key' => 'timezone',
                'label' => 'Timezone',
                'description' => 'The application timezone',
                'value' => 'Africa/Lagos',
                'type' => 'select',
                'options' => ['Africa/Lagos' => 'West Africa Time (WAT)', 'UTC' => 'UTC', 'America/New_York' => 'Eastern Time'],
                'sort_order' => 6,
            ],

            // Inventory Settings
            [
                'category' => 'inventory',
                'key' => 'auto_stock_alerts',
                'label' => 'Auto Stock Alerts',
                'description' => 'Enable/disable automatic low stock notifications',
                'value' => '1',
                'type' => 'boolean',
                'sort_order' => 1,
            ],
            [
                'category' => 'inventory',
                'key' => 'allow_negative_stock',
                'label' => 'Allow Negative Stock',
                'description' => 'Whether to allow negative inventory values',
                'value' => '0',
                'type' => 'boolean',
                'sort_order' => 2,
            ],
            [
                'category' => 'inventory',
                'key' => 'default_low_stock_threshold',
                'label' => 'Default Low Stock Threshold',
                'description' => 'Default value when creating new products',
                'value' => '10',
                'type' => 'number',
                'sort_order' => 3,
            ],
            [
                'category' => 'inventory',
                'key' => 'stock_alert_email_recipients',
                'label' => 'Stock Alert Email Recipients',
                'description' => 'Comma-separated list of email addresses to receive stock alerts',
                'value' => '',
                'type' => 'textarea',
                'sort_order' => 4,
            ],

            // Order Settings
            [
                'category' => 'orders',
                'key' => 'default_order_status',
                'label' => 'Default Order Status',
                'description' => 'Initial status for new orders',
                'value' => 'new',
                'type' => 'select',
                'options' => ['new' => 'New', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing'],
                'sort_order' => 1,
            ],
            [
                'category' => 'orders',
                'key' => 'order_number_prefix',
                'label' => 'Order Number Prefix',
                'description' => 'Prefix for order numbers (e.g., ORD-, CRM-)',
                'value' => 'ORD-',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'category' => 'orders',
                'key' => 'auto_assign_orders',
                'label' => 'Auto Assign Orders',
                'description' => 'Automatically assign orders to available CSRs',
                'value' => '1',
                'type' => 'boolean',
                'sort_order' => 3,
            ],
            [
                'category' => 'orders',
                'key' => 'order_expiry_days',
                'label' => 'Order Expiry Days',
                'description' => 'How many days before orders expire',
                'value' => '30',
                'type' => 'number',
                'sort_order' => 4,
            ],

            // Integration Settings
            [
                'category' => 'integration',
                'key' => 'api_rate_limit',
                'label' => 'API Rate Limit',
                'description' => 'API requests per minute limit',
                'value' => '1000',
                'type' => 'number',
                'sort_order' => 1,
            ],
            [
                'category' => 'integration',
                'key' => 'webhook_timeout',
                'label' => 'Webhook Timeout (seconds)',
                'description' => 'Timeout for webhook requests',
                'value' => '30',
                'type' => 'number',
                'sort_order' => 2,
            ],
            [
                'category' => 'integration',
                'key' => 'data_sync_interval',
                'label' => 'Data Sync Interval (minutes)',
                'description' => 'Interval for external data synchronization',
                'value' => '60',
                'type' => 'number',
                'sort_order' => 3,
            ],

            // UI Settings
            [
                'category' => 'ui',
                'key' => 'default_page_size',
                'label' => 'Default Page Size',
                'description' => 'Items per page in lists',
                'value' => '20',
                'type' => 'number',
                'sort_order' => 1,
            ],
            [
                'category' => 'ui',
                'key' => 'show_advanced_features',
                'label' => 'Show Advanced Features',
                'description' => 'Display advanced features to users',
                'value' => '1',
                'type' => 'boolean',
                'sort_order' => 2,
            ],
            [
                'category' => 'ui',
                'key' => 'theme_color',
                'label' => 'Theme Color',
                'description' => 'Primary theme color for the application',
                'value' => 'theme-blue',
                'type' => 'select',
                'options' => [
                    'theme-blue' => 'Blue Theme',
                    'theme-red' => 'Red Theme',
                    'theme-green' => 'Green Theme',
                    'theme-purple' => 'Purple Theme',
                    'theme-orange' => 'Orange Theme',
                    'theme-pink' => 'Pink Theme',
                    'theme-teal' => 'Teal Theme',
                    'theme-dark' => 'Dark Theme'
                ],
                'sort_order' => 3,
            ],
            [
                'category' => 'ui',
                'key' => 'sidebar_collapsed',
                'label' => 'Sidebar Collapsed by Default',
                'description' => 'Start with sidebar collapsed',
                'value' => '0',
                'type' => 'boolean',
                'sort_order' => 4,
            ],
            [
                'category' => 'ui',
                'key' => 'enable_animations',
                'label' => 'Enable Animations',
                'description' => 'Enable UI animations and transitions',
                'value' => '1',
                'type' => 'boolean',
                'sort_order' => 5,
            ],

            // SMS & Communication Settings
            [
                'category' => 'sms',
                'key' => 'sms_provider',
                'label' => 'SMS Provider',
                'description' => 'Choose your SMS service provider',
                'value' => 'none',
                'type' => 'select',
                'options' => [
                    'none' => 'No SMS Provider (Test Mode)',
                    'twilio' => 'Twilio',
                    'nexmo' => 'Vonage (Nexmo)',
                    'africastalking' => 'Africa\'s Talking',
                    'textlocal' => 'TextLocal',
                    'smartsms' => 'SmartSMS',
                    'custom' => 'Custom Provider'
                ],
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'category' => 'sms',
                'key' => 'sms_api_key',
                'label' => 'SMS API Key',
                'description' => 'API key from your SMS provider',
                'value' => '',
                'type' => 'password',
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'category' => 'sms',
                'key' => 'sms_api_secret',
                'label' => 'SMS API Secret',
                'description' => 'API secret from your SMS provider',
                'value' => '',
                'type' => 'password',
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'category' => 'sms',
                'key' => 'sms_sender_id',
                'label' => 'SMS Sender ID',
                'description' => 'Your registered sender ID or phone number',
                'value' => '',
                'type' => 'text',
                'is_required' => false,
                'sort_order' => 4,
            ],
            [
                'category' => 'sms',
                'key' => 'sms_cost_per_message',
                'label' => 'Cost Per SMS (₦)',
                'description' => 'Cost per SMS message in your local currency',
                'value' => '0.50',
                'type' => 'number',
                'is_required' => false,
                'sort_order' => 5,
            ],
            [
                'category' => 'sms',
                'key' => 'sms_api_url',
                'label' => 'SMS API URL',
                'description' => 'API endpoint URL (for custom providers)',
                'value' => '',
                'type' => 'url',
                'is_required' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

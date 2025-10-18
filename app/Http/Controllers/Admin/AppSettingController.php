<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AppSettingController extends Controller
{
    /**
     * Display the settings management page
     */
    public function index(Request $request)
    {
        $selectedCategory = $request->get('category', 'general');
        $categories = AppSetting::getCategories();
        $settings = AppSetting::getByCategory($selectedCategory);

        return view('admin.app-settings.index', compact('categories', 'settings', 'selectedCategory'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                $setting = AppSetting::where('key', $key)->first();

                if ($setting) {
                    // Handle file uploads
                    if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                        $file = $request->file("settings.{$key}");
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('settings', $filename, 'public');

                        // Delete old file if exists
                        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                            Storage::disk('public')->delete($setting->value);
                        }

                        $value = $path;
                    }

                    $setting->update(['value' => $value]);

                    // Handle theme color changes
                    if ($key === 'theme_color') {
                        $this->updateThemeInEnv($value);
                    }
                }
            }

            // Clear cache
            AppSetting::clearCache();

            return redirect()->route('admin.app-settings.index')
                ->with('success', 'Settings updated successfully! The theme will be applied on the next page refresh.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to default values
     */
    public function reset(Request $request)
    {
        $category = $request->get('category', 'general');

        try {
            // Get default settings for the category
            $defaultSettings = $this->getDefaultSettings($category);

            foreach ($defaultSettings as $key => $defaultValue) {
                $setting = AppSetting::where('key', $key)->first();
                if ($setting) {
                    $setting->update(['value' => $defaultValue]);
                }
            }

            AppSetting::clearCache();

            return redirect()->route('admin.app-settings.index', ['category' => $category])
                ->with('success', 'Settings reset to default values successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Get default settings for a category
     */
    private function getDefaultSettings($category)
    {
        $defaults = [
            'general' => [
                'app_name' => 'CRM System',
                'company_phone' => '',
                'company_email' => '',
                'default_currency' => '₦',
                'date_format' => 'DD/MM/YYYY',
                'timezone' => 'Africa/Lagos',
            ],
            'inventory' => [
                'auto_stock_alerts' => '1',
                'allow_negative_stock' => '0',
                'default_low_stock_threshold' => '10',
                'stock_alert_email_recipients' => '',
            ],
            'orders' => [
                'default_order_status' => 'new',
                'order_number_prefix' => 'ORD-',
                'auto_assign_orders' => '1',
                'order_expiry_days' => '30',
            ],
            'integration' => [
                'api_rate_limit' => '1000',
                'webhook_timeout' => '30',
                'data_sync_interval' => '60',
            ],
            'ui' => [
                'default_page_size' => '20',
                'show_advanced_features' => '1',
                'theme_color' => 'theme-blue',
                'sidebar_collapsed' => '0',
                'enable_animations' => '1',
            ],
        ];

        return $defaults[$category] ?? [];
    }

    /**
     * Update theme in .env file
     */
    private function updateThemeInEnv($theme)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        // Update or add APP_THEME
        if (preg_match('/^APP_THEME=.*$/m', $envContent)) {
            $envContent = preg_replace('/^APP_THEME=.*$/m', "APP_THEME={$theme}", $envContent);
        } else {
            $envContent .= "\nAPP_THEME={$theme}\n";
        }

        file_put_contents($envFile, $envContent);

        // Clear config cache to apply changes
        \Artisan::call('config:clear');
    }
}

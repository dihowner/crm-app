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
        // Get simplified settings
        $appName = AppSetting::getValue('app_name', 'AfroWellness');
        $themeColor = AppSetting::getValue('theme_color', 'theme-blue');
        
        // Email configuration (JSON)
        $emailConfig = json_decode(AppSetting::getValue('email_config', '{"driver":"smtp","host":"","port":"587","username":"","password":"","encryption":"tls","from_address":"","from_name":""}'), true);
        
        // SMS configuration (JSON)
        $smsConfig = json_decode(AppSetting::getValue('sms_config', '{"api_url":"http://sms.paynex.org/api/sendsms.php","auth_token":"","sender_id":"PEAKSYSTEMS","product_id":"dndroute"}'), true);

        return view('admin.app-settings.index', compact('appName', 'themeColor', 'emailConfig', 'smsConfig'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $settingType = $request->get('setting_type', 'all');

        try {
            DB::beginTransaction();

            switch ($settingType) {
                case 'app_name':
                    $request->validate([
                        'app_name' => 'required|string|max:255',
                    ]);

                    // Update App Name
                    AppSetting::setValue('app_name', $request->app_name);

                    $message = 'App Name updated successfully!';
                    break;

                case 'theme_color':
                    $request->validate([
                        'theme_color' => 'required|string|max:255',
                    ]);

                    // Update Theme Color
                    AppSetting::setValue('theme_color', $request->theme_color);
                    $this->updateThemeInEnv($request->theme_color);

                    $message = 'Theme Color updated successfully!';
                    break;

                case 'email':
                    $rules = [
                        'email_config' => 'required|array',
                        'email_config.driver' => 'required|string|in:smtp,mailgun,ses,sendgrid',
                        'email_config.from_address' => 'required|email|max:255',
                        'email_config.from_name' => 'nullable|string|max:255',
                    ];

                    // SMTP specific rules
                    $rules['email_config.host'] = 'required_if:email_config.driver,smtp|nullable|string|max:255';
                    $rules['email_config.port'] = 'required_if:email_config.driver,smtp|nullable|integer';
                    $rules['email_config.username'] = 'nullable|string|max:255';
                    $rules['email_config.password'] = 'nullable|string|max:255';
                    $rules['email_config.encryption'] = 'nullable|string|in:tls,ssl';

                    // Mailgun specific rules
                    $rules['email_config.mailgun_domain'] = 'required_if:email_config.driver,mailgun|nullable|string|max:255';
                    $rules['email_config.mailgun_secret'] = 'required_if:email_config.driver,mailgun|nullable|string|max:500';
                    $rules['email_config.mailgun_endpoint'] = 'nullable|string|max:255';

                    // SendGrid specific rules
                    $rules['email_config.sendgrid_api_key'] = 'required_if:email_config.driver,sendgrid|nullable|string|max:500';

                    // SES specific rules
                    $rules['email_config.ses_access_key'] = 'required_if:email_config.driver,ses|nullable|string|max:255';
                    $rules['email_config.ses_secret_key'] = 'required_if:email_config.driver,ses|nullable|string|max:500';
                    $rules['email_config.ses_region'] = 'required_if:email_config.driver,ses|nullable|string|max:50';

                    $request->validate($rules);

                    // Prepare email config data
                    $emailConfigData = $request->email_config;
                    
                    // If from_name is empty, use app name as default
                    if (empty($emailConfigData['from_name'])) {
                        $emailConfigData['from_name'] = AppSetting::getValue('app_name', 'AfroWellness');
                    }

                    // Update Email Configuration (store as JSON)
                    AppSetting::setValue('email_config', json_encode($emailConfigData));
                    
                    // Use the processed config for .env updates
                    $request->merge(['email_config' => $emailConfigData]);
                    
                    // Update .env and config files based on driver
                    $driver = $request->email_config['driver'];
                    if ($driver === 'smtp') {
                        $this->updateEmailInEnv($request->email_config);
                    } elseif ($driver === 'mailgun') {
                        $this->updateMailgunInEnv($request->email_config);
                        $this->updateMailgunInServices($request->email_config);
                    } elseif ($driver === 'sendgrid') {
                        $this->updateSendGridInEnv($request->email_config);
                        $this->updateSendGridInServices($request->email_config);
                    } elseif ($driver === 'ses') {
                        $this->updateSESInEnv($request->email_config);
                        $this->updateSESInServices($request->email_config);
                    }

                    // Always update MAIL_MAILER and MAIL_FROM
                    $this->updateMailDefaults($request->email_config);

                    $message = 'Email API Configuration updated successfully!';
                    break;

                case 'sms':
                    $request->validate([
                        'sms_config' => 'required|array',
                        'sms_config.api_url' => 'required|url|max:500',
                        'sms_config.auth_token' => 'required|string|max:500',
                        'sms_config.sender_id' => 'required|string|max:50',
                        'sms_config.product_id' => 'required|string|max:50',
                    ]);

                    // Update SMS Configuration (store as JSON)
                    AppSetting::setValue('sms_config', json_encode($request->sms_config));

                    $message = 'SMS API Configuration updated successfully!';
                    break;

                default:
                    // Update all settings (for backward compatibility)
                    $request->validate([
                        'app_name' => 'required|string|max:255',
                        'theme_color' => 'required|string|max:255',
                        'email_config' => 'required|array',
                        'email_config.driver' => 'required|string|in:smtp,mailgun,ses,sendgrid',
                        'email_config.host' => 'required_if:email_config.driver,smtp|nullable|string|max:255',
                        'email_config.port' => 'required_if:email_config.driver,smtp|nullable|integer',
                        'email_config.username' => 'nullable|string|max:255',
                        'email_config.password' => 'nullable|string|max:255',
                        'email_config.encryption' => 'nullable|string|in:tls,ssl',
                        'email_config.from_address' => 'required|email|max:255',
                        'email_config.from_name' => 'nullable|string|max:255',
                        'sms_config' => 'required|array',
                        'sms_config.api_url' => 'required|url|max:500',
                        'sms_config.auth_token' => 'required|string|max:500',
                        'sms_config.sender_id' => 'required|string|max:50',
                        'sms_config.product_id' => 'required|string|max:50',
                    ]);

                    // Update App Name
                    AppSetting::setValue('app_name', $request->app_name);

                    // Update Theme Color
                    AppSetting::setValue('theme_color', $request->theme_color);
                    $this->updateThemeInEnv($request->theme_color);

                    // Update Email Configuration
                    AppSetting::setValue('email_config', json_encode($request->email_config));
                    
                    // Update .env and config files based on driver
                    $driver = $request->email_config['driver'];
                    if ($driver === 'smtp') {
                        $this->updateEmailInEnv($request->email_config);
                    } elseif ($driver === 'mailgun') {
                        $this->updateMailgunInEnv($request->email_config);
                        $this->updateMailgunInServices($request->email_config);
                    } elseif ($driver === 'sendgrid') {
                        $this->updateSendGridInEnv($request->email_config);
                        $this->updateSendGridInServices($request->email_config);
                    } elseif ($driver === 'ses') {
                        $this->updateSESInEnv($request->email_config);
                        $this->updateSESInServices($request->email_config);
                    }

                    // Always update MAIL_MAILER and MAIL_FROM
                    $this->updateMailDefaults($request->email_config);

                    // Update SMS Configuration
                    AppSetting::setValue('sms_config', json_encode($request->sms_config));

                    $message = 'All settings updated successfully!';
                    break;
            }

            DB::commit();

            // Clear cache
            AppSetting::clearCache();

            return redirect()->route('admin.app-settings.index')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Update email configuration in .env file
     */
    private function updateEmailInEnv($emailConfig)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        // Update MAIL_MAILER
        $envContent = preg_replace('/^MAIL_MAILER=.*$/m', "MAIL_MAILER={$emailConfig['driver']}", $envContent);

        // Update SMTP settings if driver is smtp
        if ($emailConfig['driver'] === 'smtp') {
            $envContent = preg_replace('/^MAIL_HOST=.*$/m', "MAIL_HOST={$emailConfig['host']}", $envContent);
            $envContent = preg_replace('/^MAIL_PORT=.*$/m', "MAIL_PORT={$emailConfig['port']}", $envContent);
            $envContent = preg_replace('/^MAIL_USERNAME=.*$/m', "MAIL_USERNAME={$emailConfig['username']}", $envContent);
            $envContent = preg_replace('/^MAIL_PASSWORD=.*$/m', "MAIL_PASSWORD={$emailConfig['password']}", $envContent);
            $encryption = $emailConfig['encryption'] ?? 'tls';
            $envContent = preg_replace('/^MAIL_ENCRYPTION=.*$/m', "MAIL_ENCRYPTION={$encryption}", $envContent);
        }

        // Update FROM settings
        $envContent = preg_replace('/^MAIL_FROM_ADDRESS=.*$/m', "MAIL_FROM_ADDRESS={$emailConfig['from_address']}", $envContent);
        $fromName = $emailConfig['from_name'] ?? config('app.name');
        $envContent = preg_replace('/^MAIL_FROM_NAME=.*$/m', "MAIL_FROM_NAME=\"{$fromName}\"", $envContent);

        file_put_contents($envFile, $envContent);
        \Artisan::call('config:clear');
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

    /**
     * Update mail defaults (MAIL_MAILER, MAIL_FROM) in .env
     */
    private function updateMailDefaults($emailConfig)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        // Update MAIL_MAILER
        $envContent = preg_replace('/^MAIL_MAILER=.*$/m', "MAIL_MAILER={$emailConfig['driver']}", $envContent);

        // Update FROM settings
        $envContent = preg_replace('/^MAIL_FROM_ADDRESS=.*$/m', "MAIL_FROM_ADDRESS={$emailConfig['from_address']}", $envContent);
        $fromName = $emailConfig['from_name'] ?? config('app.name');
        $envContent = preg_replace('/^MAIL_FROM_NAME=.*$/m', "MAIL_FROM_NAME=\"{$fromName}\"", $envContent);

        file_put_contents($envFile, $envContent);
    }

    /**
     * Update Mailgun configuration in .env file
     */
    private function updateMailgunInEnv($emailConfig)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        // Add or update MAILGUN_DOMAIN
        if (preg_match('/^MAILGUN_DOMAIN=/m', $envContent)) {
            $envContent = preg_replace('/^MAILGUN_DOMAIN=.*$/m', "MAILGUN_DOMAIN={$emailConfig['mailgun_domain']}", $envContent);
        } else {
            $envContent .= "\nMAILGUN_DOMAIN={$emailConfig['mailgun_domain']}";
        }

        // Add or update MAILGUN_SECRET
        if (preg_match('/^MAILGUN_SECRET=/m', $envContent)) {
            $envContent = preg_replace('/^MAILGUN_SECRET=.*$/m', "MAILGUN_SECRET={$emailConfig['mailgun_secret']}", $envContent);
        } else {
            $envContent .= "\nMAILGUN_SECRET={$emailConfig['mailgun_secret']}";
        }

        // Add or update MAILGUN_ENDPOINT if provided
        if (!empty($emailConfig['mailgun_endpoint'])) {
            if (preg_match('/^MAILGUN_ENDPOINT=/m', $envContent)) {
                $envContent = preg_replace('/^MAILGUN_ENDPOINT=.*$/m', "MAILGUN_ENDPOINT={$emailConfig['mailgun_endpoint']}", $envContent);
            } else {
                $envContent .= "\nMAILGUN_ENDPOINT={$emailConfig['mailgun_endpoint']}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Update Mailgun configuration in config/services.php
     */
    private function updateMailgunInServices($emailConfig)
    {
        $servicesFile = config_path('services.php');
        if (!file_exists($servicesFile)) {
            return;
        }

        $servicesContent = file_get_contents($servicesFile);

        // Update or add Mailgun configuration
        $mailgunConfig = "'mailgun' => [\n        'domain' => env('MAILGUN_DOMAIN'),\n        'secret' => env('MAILGUN_SECRET'),\n        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),\n    ],";

        if (preg_match("/'mailgun' => \[.*?\],/s", $servicesContent)) {
            $servicesContent = preg_replace("/'mailgun' => \[.*?\],/s", $mailgunConfig, $servicesContent);
        } else {
            // Insert after 'postmark' or at the end of the array
            if (preg_match("/'postmark' => \[.*?\],/s", $servicesContent)) {
                $servicesContent = preg_replace("/('postmark' => \[.*?\],)/s", "$1\n\n    " . $mailgunConfig, $servicesContent);
            } else {
                $servicesContent = str_replace('    ],', "    ],\n\n    " . $mailgunConfig, $servicesContent);
            }
        }

        file_put_contents($servicesFile, $servicesContent);
    }

    /**
     * Update SendGrid configuration in .env file
     */
    private function updateSendGridInEnv($emailConfig)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        // Add or update SENDGRID_API_KEY
        if (preg_match('/^SENDGRID_API_KEY=/m', $envContent)) {
            $envContent = preg_replace('/^SENDGRID_API_KEY=.*$/m', "SENDGRID_API_KEY={$emailConfig['sendgrid_api_key']}", $envContent);
        } else {
            $envContent .= "\nSENDGRID_API_KEY={$emailConfig['sendgrid_api_key']}";
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Update SendGrid configuration in config/services.php
     */
    private function updateSendGridInServices($emailConfig)
    {
        $servicesFile = config_path('services.php');
        if (!file_exists($servicesFile)) {
            return;
        }

        $servicesContent = file_get_contents($servicesFile);

        // Update or add SendGrid configuration
        $sendgridConfig = "'sendgrid' => [\n        'key' => env('SENDGRID_API_KEY'),\n    ],";

        if (preg_match("/'sendgrid' => \[.*?\],/s", $servicesContent)) {
            $servicesContent = preg_replace("/'sendgrid' => \[.*?\],/s", $sendgridConfig, $servicesContent);
        } else {
            // Insert after 'resend' or at the end of the array
            if (preg_match("/'resend' => \[.*?\],/s", $servicesContent)) {
                $servicesContent = preg_replace("/('resend' => \[.*?\],)/s", "$1\n\n    " . $sendgridConfig, $servicesContent);
            } else {
                $servicesContent = str_replace('    ],', "    ],\n\n    " . $sendgridConfig, $servicesContent);
            }
        }

        file_put_contents($servicesFile, $servicesContent);
    }

    /**
     * Update SES configuration in .env file
     */
    private function updateSESInEnv($emailConfig)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $envContent = file_get_contents($envFile);

        // Add or update AWS credentials
        if (preg_match('/^AWS_ACCESS_KEY_ID=/m', $envContent)) {
            $envContent = preg_replace('/^AWS_ACCESS_KEY_ID=.*$/m', "AWS_ACCESS_KEY_ID={$emailConfig['ses_access_key']}", $envContent);
        } else {
            $envContent .= "\nAWS_ACCESS_KEY_ID={$emailConfig['ses_access_key']}";
        }

        if (preg_match('/^AWS_SECRET_ACCESS_KEY=/m', $envContent)) {
            $envContent = preg_replace('/^AWS_SECRET_ACCESS_KEY=.*$/m', "AWS_SECRET_ACCESS_KEY={$emailConfig['ses_secret_key']}", $envContent);
        } else {
            $envContent .= "\nAWS_SECRET_ACCESS_KEY={$emailConfig['ses_secret_key']}";
        }

        if (preg_match('/^AWS_DEFAULT_REGION=/m', $envContent)) {
            $envContent = preg_replace('/^AWS_DEFAULT_REGION=.*$/m', "AWS_DEFAULT_REGION={$emailConfig['ses_region']}", $envContent);
        } else {
            $envContent .= "\nAWS_DEFAULT_REGION={$emailConfig['ses_region']}";
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Update SES configuration in config/services.php
     */
    private function updateSESInServices($emailConfig)
    {
        $servicesFile = config_path('services.php');
        if (!file_exists($servicesFile)) {
            return;
        }

        $servicesContent = file_get_contents($servicesFile);

        // Update SES configuration (it should already exist, but update it)
        $sesConfig = "'ses' => [\n        'key' => env('AWS_ACCESS_KEY_ID'),\n        'secret' => env('AWS_SECRET_ACCESS_KEY'),\n        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),\n    ],";

        if (preg_match("/'ses' => \[.*?\],/s", $servicesContent)) {
            $servicesContent = preg_replace("/'ses' => \[.*?\],/s", $sesConfig, $servicesContent);
        } else {
            // Insert after 'resend' or at the end of the array
            if (preg_match("/'resend' => \[.*?\],/s", $servicesContent)) {
                $servicesContent = preg_replace("/('resend' => \[.*?\],)/s", "$1\n\n    " . $sesConfig, $servicesContent);
            } else {
                $servicesContent = str_replace('    ],', "    ],\n\n    " . $sesConfig, $servicesContent);
            }
        }

        file_put_contents($servicesFile, $servicesContent);
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            $emailService = new \App\Services\EmailService();

            if (!$emailService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Email is not configured. Please configure email settings first.'
                ], 400);
            }

            $subject = 'Test Email from ' . AppSetting::getValue('app_name', 'AfroWellness');
            $body = 'This is a test email to verify that your email configuration is working correctly.';
            
            $result = $emailService->sendEmail($request->test_email, $subject, $body);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully! Please check your inbox.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Failed to send test email.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error sending test email: ' . $e->getMessage()
            ], 500);
        }
    }
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px;">Welcome!</h1>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 16px;">Your account has been created</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-top: 0;">Dear {{ $user->name }},</p>
            
            <p style="font-size: 16px;">We're excited to inform you that your account has been created for {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}!</p>

            <!-- Login Credentials -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h2 style="color: #667eea; margin-top: 0; font-size: 20px;">Your Login Credentials</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666; width: 40%;">Email Address:</td>
                        <td style="padding: 8px 0; color: #333; font-family: monospace;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Password:</td>
                        <td style="padding: 8px 0; color: #333; font-family: monospace; font-size: 18px; font-weight: bold;">{{ $plainPassword }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Role:</td>
                        <td style="padding: 8px 0; color: #333;">{{ $user->role->name ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Important Security Notice -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0; font-size: 16px;">🔒 Important Security Notice</h3>
                <ul style="margin: 10px 0; padding-left: 20px; color: #856404;">
                    <li>Please keep your login credentials secure and confidential</li>
                    <li>We recommend changing your password after your first login</li>
                    <li>Do not share your password with anyone</li>
                    <li>If you didn't request this account, please contact your administrator immediately</li>
                </ul>
            </div>

            <!-- Login Button -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #0c5460; margin-top: 0; font-size: 16px;">🚀 Get Started</h3>
                <p style="margin: 10px 0; color: #0c5460;">Click the button below to access your account:</p>
                <p style="margin: 10px 0;">
                    <a href="{{ url('/login') }}" style="display: inline-block; background-color: #2196F3; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">Login to Dashboard</a>
                </p>
            </div>

            @if($user->max_orders_per_day)
            <!-- Additional Information -->
            <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #155724; margin-top: 0; font-size: 16px;">ℹ️ Account Information</h3>
                <p style="margin: 5px 0; color: #155724;">
                    <strong>Maximum Orders Per Day:</strong> {{ $user->max_orders_per_day }}<br>
                    <strong>Account Status:</strong> {{ $user->is_active ? 'Active' : 'Inactive' }}
                </p>
            </div>
            @endif

            <p style="font-size: 16px; margin-top: 30px;">If you have any questions or need assistance, please don't hesitate to contact your administrator.</p>

            <p style="font-size: 16px; margin-top: 20px;">
                Welcome aboard!<br>
                <strong>{{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }} Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #666; font-size: 12px;">
                This is an automated email. Please do not reply to this message.<br>
                © {{ date('Y') }} {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>


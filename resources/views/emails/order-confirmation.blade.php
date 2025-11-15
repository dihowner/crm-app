<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px;">Order Confirmed!</h1>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 16px;">Thank you for your order</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-top: 0;">Dear {{ $order->customer->name }},</p>
            
            <p style="font-size: 16px;">We're excited to confirm that your order has been successfully placed!</p>

            <!-- Order Details -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h2 style="color: #667eea; margin-top: 0; font-size: 20px;">Order Details</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Order Number:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Product:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->product->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Quantity:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->quantity }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Unit Price:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">₦{{ number_format($order->unit_price, 2) }}</td>
                    </tr>
                    <tr style="border-top: 2px solid #dee2e6;">
                        <td style="padding: 12px 0; font-weight: bold; font-size: 18px; color: #333;">Total Amount:</td>
                        <td style="padding: 12px 0; text-align: right; font-weight: bold; font-size: 18px; color: #667eea;">₦{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Delivery Information -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0; font-size: 16px;">📦 Delivery Information</h3>
                <p style="margin: 5px 0; color: #856404;">
                    <strong>Address:</strong> {{ $order->customer->address }}, {{ $order->customer->state }}
                </p>
                <p style="margin: 5px 0; color: #856404;">
                    <strong>Phone:</strong> {{ $order->customer->phone }}
                </p>
            </div>

            <!-- Status -->
            <div style="margin: 20px 0;">
                <p style="margin: 5px 0; color: #666;">
                    <strong>Order Status:</strong> 
                    <span style="display: inline-block; background-color: #667eea; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: bold;">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </p>
            </div>

            <!-- Next Steps -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #0c5460; margin-top: 0; font-size: 16px;">What's Next?</h3>
                <p style="margin: 5px 0; color: #0c5460;">We'll keep you updated on your order status. You'll receive notifications when your order is out for delivery.</p>
            </div>

            <p style="font-size: 16px; margin-top: 30px;">If you have any questions, please don't hesitate to contact us.</p>

            <p style="font-size: 16px; margin-top: 20px;">
                Best regards,<br>
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


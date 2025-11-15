<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order is Out for Delivery - {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 30px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px;">🚚 Out for Delivery!</h1>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 16px;">Your order is on its way</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-top: 0;">Dear {{ $order->customer->name }},</p>
            
            <p style="font-size: 16px;">Great news! Your order is now out for delivery and should arrive soon!</p>

            <!-- Order Summary -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h2 style="color: #28a745; margin-top: 0; font-size: 20px;">Order Summary</h2>
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
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Total Amount:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: bold; color: #28a745; font-size: 18px;">₦{{ number_format($order->total_price, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Delivery Details -->
            <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #155724; margin-top: 0; font-size: 18px;">📍 Delivery Address</h3>
                <p style="margin: 5px 0; color: #155724; font-size: 16px;">
                    {{ $order->customer->address }}<br>
                    {{ $order->customer->state }}
                </p>
                <p style="margin: 10px 0 5px 0; color: #155724;">
                    <strong>Contact Phone:</strong> {{ $order->customer->phone }}
                </p>
            </div>

            @if($order->agent)
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0; font-size: 16px;">👤 Delivery Agent</h3>
                <p style="margin: 5px 0; color: #856404;">
                    <strong>Name:</strong> {{ $order->agent->name }}<br>
                    @if($order->agent->phone)
                    <strong>Phone:</strong> {{ $order->agent->phone }}
                    @endif
                </p>
            </div>
            @endif

            <!-- Important Notice -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #0c5460; margin-top: 0; font-size: 16px;">📋 Important Information</h3>
                <ul style="margin: 10px 0; padding-left: 20px; color: #0c5460;">
                    <li>Please ensure someone is available to receive the order</li>
                    <li>Have your payment ready (if pay on delivery)</li>
                    <li>Keep your phone nearby for delivery updates</li>
                </ul>
            </div>

            <p style="font-size: 16px; margin-top: 30px;">We'll notify you once your order has been delivered.</p>

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


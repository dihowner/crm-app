<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 30px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px;">✅ Payment Received!</h1>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 16px;">Thank you for your payment</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-top: 0;">Dear {{ $order->customer->name }},</p>
            
            <p style="font-size: 16px;">We're pleased to confirm that we have successfully received your payment!</p>

            <!-- Payment Details -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h2 style="color: #17a2b8; margin-top: 0; font-size: 20px;">Payment Details</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Order Number:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Payment Date:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $paymentRecord->payment_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Payment Method:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ ucwords(str_replace('_', ' ', $paymentRecord->payment_method ?? 'cash')) }}</td>
                    </tr>
                    @if($paymentRecord->reference_number)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Reference Number:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $paymentRecord->reference_number }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 2px solid #dee2e6;">
                        <td style="padding: 12px 0; font-weight: bold; font-size: 18px; color: #333;">Amount Paid:</td>
                        <td style="padding: 12px 0; text-align: right; font-weight: bold; font-size: 18px; color: #17a2b8;">₦{{ number_format($paymentRecord->amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Order Summary -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #0c5460; margin-top: 0; font-size: 16px;">📦 Order Summary</h3>
                <p style="margin: 5px 0; color: #0c5460;">
                    <strong>Product:</strong> {{ $order->product->name }}<br>
                    <strong>Quantity:</strong> {{ $order->quantity }}<br>
                    <strong>Order Total:</strong> ₦{{ number_format($order->total_price, 2) }}
                </p>
            </div>

            @if($paymentRecord->notes)
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0; font-size: 16px;">📝 Payment Notes</h3>
                <p style="margin: 5px 0; color: #856404;">{{ $paymentRecord->notes }}</p>
            </div>
            @endif

            <!-- Status Update -->
            <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #155724; font-size: 16px;">
                    <strong>Order Status:</strong> 
                    <span style="display: inline-block; background-color: #28a745; color: #fff; padding: 4px 12px; border-radius: 12px; font-size: 14px; font-weight: bold; margin-left: 10px;">
                        Paid
                    </span>
                </p>
            </div>

            <p style="font-size: 16px; margin-top: 30px;">Thank you for your business! We appreciate your trust in {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}.</p>

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


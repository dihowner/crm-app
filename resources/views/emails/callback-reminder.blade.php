<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback Reminder - {{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 30px; text-align: center;">
            <h1 style="color: #fff; margin: 0; font-size: 24px;">📞 Callback Reminder</h1>
            <p style="color: #fff; margin: 10px 0 0 0; font-size: 16px;">Time to follow up with a customer</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-top: 0;">Hello {{ $order->assignedUser->name }},</p>
            
            <p style="font-size: 16px;">This is a reminder that you need to call back a customer.</p>

            <!-- Order Details -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h2 style="color: #17a2b8; margin-top: 0; font-size: 20px;">Order Details</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Order Number:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Customer Name:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->customer->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Phone Number:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">
                            <a href="tel:{{ $order->customer->phone }}" style="color: #17a2b8; text-decoration: none;">{{ $order->customer->phone }}</a>
                        </td>
                    </tr>
                    @if($order->customer->whatsapp_number)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">WhatsApp:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->customer->whatsapp_number }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #666;">Product:</td>
                        <td style="padding: 8px 0; text-align: right; color: #333;">{{ $order->product->name }}</td>
                    </tr>
                </table>
            </div>

            <!-- Callback Time -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #856404; margin-top: 0; font-size: 18px;">⏰ Scheduled Callback Time</h3>
                <p style="margin: 5px 0; color: #856404; font-size: 18px; font-weight: bold;">
                    {{ $order->callback_reminder->format('l, F d, Y') }}<br>
                    {{ $order->callback_reminder->format('h:i A') }}
                </p>
            </div>

            <!-- Quick Actions -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #0c5460; margin-top: 0; font-size: 16px;">🔗 Quick Actions</h3>
                <p style="margin: 10px 0; color: #0c5460;">
                    <a href="{{ route('orders.show', $order) }}" style="display: inline-block; background-color: #2196F3; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 5px 0;">View Order Details</a>
                </p>
                <p style="margin: 10px 0; color: #0c5460;">
                    <a href="tel:{{ $order->customer->phone }}" style="display: inline-block; background-color: #28a745; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 5px 0;">Call Customer Now</a>
                </p>
            </div>

            <!-- Google Calendar Link -->
            <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h3 style="color: #155724; margin-top: 0; font-size: 16px;">📅 Add to Google Calendar</h3>
                <p style="margin: 5px 0; color: #155724;">Click the link below to add this callback to your Google Calendar:</p>
                <p style="margin: 10px 0;">
                    @php
                        $startTime = $order->callback_reminder->format('Ymd\THis\Z');
                        $endTime = $order->callback_reminder->copy()->addMinutes(15)->format('Ymd\THis\Z');
                        $title = urlencode('Callback: ' . $order->customer->name . ' - ' . $order->order_number);
                        $details = urlencode('Order: ' . $order->order_number . "\n" . 'Customer: ' . $order->customer->name . "\n" . 'Phone: ' . $order->customer->phone . "\n" . 'Product: ' . $order->product->name);
                        $location = urlencode($order->customer->address . ', ' . $order->customer->state);
                        $googleCalendarUrl = "https://www.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$startTime}/{$endTime}&details={$details}&location={$location}";
                    @endphp
                    <a href="{{ $googleCalendarUrl }}" target="_blank" style="display: inline-block; background-color: #4285F4; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin: 5px 0;">Add to Google Calendar</a>
                </p>
            </div>

            <p style="font-size: 16px; margin-top: 30px;">Thank you for your attention to this callback!</p>

            <p style="font-size: 16px; margin-top: 20px;">
                Best regards,<br>
                <strong>{{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }} System</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #666; font-size: 12px;">
                This is an automated reminder email. Please do not reply to this message.<br>
                © {{ date('Y') }} {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f4f4f4; padding: 20px; border-radius: 5px;">
        <h2 style="color: #2c3e50; margin-top: 0;">{{ $subject }}</h2>
        
        <div style="background-color: #fff; padding: 20px; border-radius: 5px; margin-top: 20px;">
            {!! nl2br(e($body)) !!}
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666;">
            <p>This email was sent from {{ $from_name }}.</p>
            <p>If you did not expect this email, please ignore it.</p>
        </div>
    </div>
</body>
</html>


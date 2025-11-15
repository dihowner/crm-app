<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use App\Services\EmailService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule callback reminders check (runs every 15 minutes)
Schedule::call(function () {
    $emailService = new EmailService();
    
    // Find orders with callback reminders that are due (within the next 15 minutes)
    $dueTime = now()->addMinutes(15);
    
    $orders = Order::where('status', 'call_back')
        ->whereNotNull('callback_reminder')
        ->where('callback_reminder', '<=', $dueTime)
        ->where('callback_reminder', '>=', now())
        ->whereHas('assignedUser', function ($query) {
            $query->whereNotNull('email');
        })
        ->with(['customer', 'product', 'assignedUser'])
        ->get();

    foreach ($orders as $order) {
        try {
            // Check if reminder was already sent (you can add a field to track this if needed)
            // For now, we'll send reminders for any callback due within 15 minutes
            $emailService->sendCallbackReminder($order);
            \Log::info("Callback reminder sent for order {$order->order_number} to {$order->assignedUser->email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send callback reminder for order {$order->order_number}: " . $e->getMessage());
        }
    }
})->everyFifteenMinutes()->name('check-callback-reminders');

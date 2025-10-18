<?php

namespace App\Http\Controllers;

use App\Models\SmsRecord;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmsMarketingController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions
        if (!Auth::user()->hasPermission('sms_marketing')) {
            abort(403, 'You do not have permission to access SMS Marketing.');
        }

        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $query = SmsRecord::with(['sentBy', 'customer', 'order']);

        // Filter by campaign name
        if ($request->filled('campaign_name')) {
            $query->where('campaign_name', 'like', '%' . $request->campaign_name . '%');
        }

        // Filter by SMS type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sent_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter by sent by user
        if ($request->filled('sent_by')) {
            $query->where('sent_by', $request->sent_by);
        }

        $smsRecords = $query->orderBy('sent_at', 'desc')->paginate(15);

        // Get filter options
        $smsTypes = [
            'bulk' => 'Bulk SMS',
            'single' => 'Single SMS',
            'order_confirmation' => 'Order Confirmation',
            'order_reminder' => 'Order Reminder',
            'delivery_notification' => 'Delivery Notification'
        ];

        $smsStatuses = [
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
            'pending' => 'Pending'
        ];

        // Get users who have sent SMS (for filter)
        $sentByUsers = User::whereHas('smsRecords')->get();

        return view('sms-marketing.index', compact('smsRecords', 'smsTypes', 'smsStatuses', 'sentByUsers'));
    }

    public function create()
    {
        // Check permissions
        if (!Auth::user()->hasPermission('sms_marketing')) {
            abort(403, 'You do not have permission to access SMS Marketing.');
        }

        // Get customer groups for targeting
        $customerGroups = [
            'all' => 'All Customers',
            'delivered_orders' => 'Customers with Delivered Orders',
            'pending_orders' => 'Customers with Pending Orders',
            'new_customers' => 'New Customers (Last 30 days)',
            'returning_customers' => 'Returning Customers'
        ];

        // Get SMS templates
        $smsTemplates = [
            'order_confirmation' => 'Order Confirmation',
            'order_reminder' => 'Order Reminder',
            'delivery_notification' => 'Delivery Notification',
            'marketing_promotion' => 'Marketing Promotion',
            'custom' => 'Custom Message'
        ];

        return view('sms-marketing.create', compact('customerGroups', 'smsTemplates'));
    }

    public function store(Request $request)
    {
        // Check permissions
        if (!Auth::user()->hasPermission('sms_marketing')) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'message' => 'required|string|max:160',
            'customer_group' => 'required|string',
            'sms_type' => 'required|string',
            'template' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Get target customers based on group
            $customers = $this->getTargetCustomers($request->customer_group);

            if ($customers->count() === 0) {
                return response()->json(['error' => 'No customers found for the selected group.'], 400);
            }

            $successCount = 0;
            $failedCount = 0;

            // Initialize SMS service
            $smsService = new \App\Services\SmsService();

            // Send SMS to each customer
            foreach ($customers as $customer) {
                $personalizedMessage = $this->personalizeMessage($request->message, $customer);

                // Send SMS via provider
                $smsResult = $smsService->sendSms($customer->phone, $personalizedMessage, $customer->name);

                $smsRecord = SmsRecord::create([
                    'campaign_name' => $request->campaign_name,
                    'type' => $request->sms_type,
                    'message' => $personalizedMessage,
                    'recipient_phone' => $customer->phone,
                    'recipient_name' => $customer->name,
                    'status' => $smsResult['status'],
                    'sms_provider' => $smsService->getProvider(),
                    'provider_message_id' => $smsResult['message_id'] ?? null,
                    'error_message' => $smsResult['success'] ? null : $smsResult['error'],
                    'cost' => $smsResult['cost'],
                    'sent_by' => Auth::id(),
                    'customer_id' => $customer->id,
                    'sent_at' => $smsResult['success'] ? now() : null,
                    'delivered_at' => $smsResult['success'] ? now()->addMinutes(1) : null,
                ]);

                if ($smsResult['success']) {
                    $successCount++;
                } else {
                    $failedCount++;
                    \Log::error("SMS failed for customer {$customer->id}: {$smsResult['error']}");
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "SMS campaign sent successfully! Sent: {$successCount}, Failed: {$failedCount}",
                'stats' => [
                    'total' => $customers->count(),
                    'sent' => $successCount,
                    'failed' => $failedCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to send SMS campaign: ' . $e->getMessage()], 500);
        }
    }

    public function show(SmsRecord $smsRecord)
    {
        // Check permissions
        if (!Auth::user()->hasPermission('sms_marketing')) {
            abort(403, 'You do not have permission to access SMS Marketing.');
        }

        $smsRecord->load(['sentBy', 'customer', 'order']);

        return view('sms-marketing.show', compact('smsRecord'));
    }

    private function getTargetCustomers($customerGroup)
    {
        switch ($customerGroup) {
            case 'all':
                return Customer::all();

            case 'delivered_orders':
                return Customer::whereHas('orders', function ($query) {
                    $query->where('status', 'delivered');
                })->get();

            case 'pending_orders':
                return Customer::whereHas('orders', function ($query) {
                    $query->whereIn('status', ['new', 'scheduled', 'not picking calls', 'number off', 'call back']);
                })->get();

            case 'new_customers':
                return Customer::where('created_at', '>=', now()->subDays(30))->get();

            case 'returning_customers':
                return Customer::whereHas('orders', function ($query) {
                    $query->where('status', 'delivered');
                })->where('created_at', '<', now()->subDays(30))->get();

            default:
                return collect();
        }
    }

    private function personalizeMessage($message, $customer)
    {
        // Replace placeholders with customer data
        $personalizedMessage = str_replace('[name]', $customer->name, $message);
        $personalizedMessage = str_replace('[phone]', $customer->phone, $personalizedMessage);
        $personalizedMessage = str_replace('[email]', $customer->email ?? 'N/A', $personalizedMessage);

        return $personalizedMessage;
    }

    public function getCampaignStats()
    {
        // Check permissions
        if (!Auth::user()->hasPermission('sms_marketing')) {
            abort(403, 'You do not have permission to access SMS Marketing.');
        }

        $stats = [
            'total_sms' => SmsRecord::count(),
            'sent_today' => SmsRecord::whereDate('sent_at', today())->count(),
            'delivered_today' => SmsRecord::whereDate('delivered_at', today())->count(),
            'failed_today' => SmsRecord::whereDate('sent_at', today())->where('status', 'failed')->count(),
            'total_cost' => SmsRecord::sum('cost'),
            'cost_today' => SmsRecord::whereDate('sent_at', today())->sum('cost')
        ];

        return response()->json($stats);
    }
}

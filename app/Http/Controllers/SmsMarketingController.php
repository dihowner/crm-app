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

        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            abort(403, 'Access denied. CSR users cannot access SMS Marketing.');
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

        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            abort(403, 'Access denied. CSR users cannot access SMS Marketing.');
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

        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            return response()->json(['error' => 'Access denied. CSR users cannot access SMS Marketing.'], 403);
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

        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            abort(403, 'Access denied. CSR users cannot access SMS Marketing.');
        }

        $smsRecord->load(['sentBy', 'customer', 'order']);

        return view('sms-marketing.show', compact('smsRecord'));
    }

    private function getTargetCustomers($customerGroup)
    {
        switch ($customerGroup) {
            case 'all':
                // Return unique customers by name+phone combination
                return Customer::select('customers.*')
                    ->join(DB::raw('(SELECT name, phone, MIN(id) as min_id FROM customers GROUP BY name, phone) as unique_customers'), function($join) {
                        $join->on('customers.name', '=', 'unique_customers.name')
                             ->on('customers.phone', '=', 'unique_customers.phone')
                             ->on('customers.id', '=', 'unique_customers.min_id');
                    })
                    ->get();

            case 'delivered_orders':
                // Return unique customers who have delivered orders
                $customerIds = Customer::whereHas('orders', function ($query) {
                    $query->where('status', 'delivered');
                })->pluck('id');
                
                if ($customerIds->isEmpty()) {
                    return collect();
                }
                
                return Customer::whereIn('id', $customerIds)
                    ->select('customers.*')
                    ->join(DB::raw('(SELECT name, phone, MIN(id) as min_id FROM customers WHERE id IN (' . $customerIds->implode(',') . ') GROUP BY name, phone) as unique_customers'), function($join) {
                        $join->on('customers.name', '=', 'unique_customers.name')
                             ->on('customers.phone', '=', 'unique_customers.phone')
                             ->on('customers.id', '=', 'unique_customers.min_id');
                    })
                    ->get();

            case 'pending_orders':
                // Return unique customers who have pending orders
                $customerIds = Customer::whereHas('orders', function ($query) {
                    $query->whereIn('status', ['new', 'scheduled', 'not_picking_calls', 'number_off', 'call_back']);
                })->pluck('id');
                
                if ($customerIds->isEmpty()) {
                    return collect();
                }
                
                return Customer::whereIn('id', $customerIds)
                    ->select('customers.*')
                    ->join(DB::raw('(SELECT name, phone, MIN(id) as min_id FROM customers WHERE id IN (' . $customerIds->implode(',') . ') GROUP BY name, phone) as unique_customers'), function($join) {
                        $join->on('customers.name', '=', 'unique_customers.name')
                             ->on('customers.phone', '=', 'unique_customers.phone')
                             ->on('customers.id', '=', 'unique_customers.min_id');
                    })
                    ->get();

            case 'new_customers':
                // Return unique customers created in last 30 days
                return Customer::where('created_at', '>=', now()->subDays(30))
                    ->select('customers.*')
                    ->join(DB::raw('(SELECT name, phone, MIN(id) as min_id FROM customers GROUP BY name, phone) as unique_customers'), function($join) {
                        $join->on('customers.name', '=', 'unique_customers.name')
                             ->on('customers.phone', '=', 'unique_customers.phone')
                             ->on('customers.id', '=', 'unique_customers.min_id');
                    })
                    ->get();

            case 'returning_customers':
                // Returning Customers: Unique name+phone combinations that have duplicate records
                // Return only one record per unique name+phone combination (the one with the minimum ID)
                return Customer::select('customers.*')
                    ->join(DB::raw('(SELECT name, phone, MIN(id) as min_id FROM customers GROUP BY name, phone HAVING COUNT(*) > 1) as duplicate_customers'), function($join) {
                        $join->on('customers.name', '=', 'duplicate_customers.name')
                             ->on('customers.phone', '=', 'duplicate_customers.phone')
                             ->on('customers.id', '=', 'duplicate_customers.min_id');
                    })
                    ->get();

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

        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            abort(403, 'Access denied. CSR users cannot access SMS Marketing.');
        }

        $stats = [
            'total_sms' => SmsRecord::count(),
            'sent_today' => SmsRecord::whereDate('sent_at', today())->count(),
            'delivered_today' => SmsRecord::whereDate('delivered_at', today())->count(),
            'failed_today' => SmsRecord::whereDate('sent_at', today())->where('status', 'failed')->count(),
            'total_cost' => (float) SmsRecord::whereNotNull('cost')->sum('cost'),
            'cost_today' => (float) SmsRecord::whereDate('sent_at', today())->whereNotNull('cost')->sum('cost')
        ];

        return response()->json($stats);
    }

    public function getCustomerCount(Request $request)
    {
        // CSR users cannot access SMS Marketing
        if (Auth::user()->isCSR()) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied. CSR users cannot access SMS Marketing.'
            ], 403);
        }

        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            return response()->json([
                'success' => false,
                'error' => 'Access denied. Logistic Managers can only access inventory management.'
            ], 403);
        }

        $request->validate([
            'customer_group' => 'required|string|in:all,delivered_orders,pending_orders,new_customers,returning_customers'
        ]);

        try {
            $customers = $this->getTargetCustomers($request->customer_group);
            $count = $customers->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get customer count: ' . $e->getMessage()
            ], 500);
        }
    }
}

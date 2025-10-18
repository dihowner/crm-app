<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SmsRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        // Get customers who have at least one delivered order
        $query = Customer::whereHas('orders', function ($q) {
            $q->where('status', 'delivered');
        })->withCount(['orders' => function ($q) {
            $q->where('status', 'delivered');
        }]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('orders.product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->where('product_id', $request->product_id)
                  ->where('status', 'delivered');
            });
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('state', $request->location);
        }

        $customers = $query->orderBy('name')->paginate(15);

        // Get filter options
        $products = Product::orderBy('name')->get();
        $locations = Customer::distinct()->pluck('state')->filter()->sort()->values();

        return view('customers.index', compact('customers', 'products', 'locations'));
    }

    public function show(Customer $customer)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        // Load customer with relationships
        $customer->load(['orders.product', 'orders.assignedUser']);

        // Get delivered orders count
        $deliveredOrdersCount = $customer->orders()->where('status', 'delivered')->count();

        // Get recent orders (last 10)
        $recentOrders = $customer->orders()
            ->with(['product', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get SMS history for this customer
        $smsHistory = SmsRecord::where('customer_id', $customer->id)
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get();

        return view('customers.show', compact('customer', 'deliveredOrdersCount', 'recentOrders', 'smsHistory'));
    }

    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id'
        ]);

        $customers = Customer::whereIn('id', $request->customer_ids)->get();
        $sentCount = 0;
        $failedCount = 0;
        $totalCost = 0;

        // Initialize SMS service
        $smsService = new \App\Services\SmsService();

        foreach ($customers as $customer) {
            // Personalize message
            $personalizedMessage = str_replace('[name]', $customer->name, $request->message);

            // Send SMS via provider
            $smsResult = $smsService->sendSms($customer->phone, $personalizedMessage, $customer->name);

            // Create SMS record
            $smsRecord = SmsRecord::create([
                'campaign_name' => 'Bulk SMS Campaign',
                'type' => 'bulk',
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
                $sentCount++;
                $totalCost += $smsResult['cost'];
            } else {
                $failedCount++;
                \Log::error("SMS failed for customer {$customer->id}: {$smsResult['error']}");
            }
        }

        // Prepare response message
        $message = "Bulk SMS completed! Sent: {$sentCount}, Failed: {$failedCount}";
        if ($totalCost > 0) {
            $message .= ", Total Cost: ₦" . number_format($totalCost, 2);
        }

        return redirect()->back()->with('success', $message);
    }

    public function getCustomersForBulkSms(Request $request)
    {
        $query = Customer::whereHas('orders', function ($q) {
            $q->where('status', 'delivered');
        });

        if ($request->filled('product_id')) {
            $query->whereHas('orders', function ($q) use ($request) {
                $q->where('product_id', $request->product_id)
                  ->where('status', 'delivered');
            });
        }

        if ($request->filled('location')) {
            $query->where('state', $request->location);
        }

        $customers = $query->select('id', 'name', 'phone')->get();

        return response()->json($customers);
    }
}

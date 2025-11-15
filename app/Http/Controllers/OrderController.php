<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $query = Order::with(['customer', 'product', 'assignedUser']);

        // Role-based filtering
        if (Auth::user()->isCSR()) {
            $query->where('assigned_to', Auth::id());
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Assigned to filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Today filter (for CSR new orders)
        if ($request->filled('today')) {
            $query->whereDate('assigned_at', today());
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $statuses = ['new', 'scheduled', 'not picking calls', 'number off', 'call back', 'delivered', 'failed', 'paid'];
        $assignedUsers = User::whereHas('role', function($q) {
            $q->where('slug', 'csr');
        })->get(); // Only CSR
        $products = Product::all();

        return view('orders.index', compact('orders', 'statuses', 'assignedUsers', 'products'));
    }

    public function todaysOrders(Request $request)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $query = Order::with(['customer', 'product', 'assignedUser'])
                     ->whereDate('created_at', today());

        // Role-based filtering
        if (Auth::user()->isCSR()) {
            $query->where('assigned_to', Auth::id());
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Assigned to filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Product filter (searchable dropdown)
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $statuses = ['new', 'scheduled', 'not picking calls', 'number off', 'call back', 'delivered', 'failed', 'paid'];
        $assignedUsers = User::whereHas('role', function($q) {
            $q->where('slug', 'csr');
        })->get(); // Only CSR
        $products = Product::all();

        return view('orders.todays', compact('orders', 'statuses', 'assignedUsers', 'products'));
    }

    public function overdueDeliveries(Request $request)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $query = Order::with(['customer', 'product', 'assignedUser'])
                     ->where('status', 'scheduled')
                     ->whereNotNull('scheduled_delivery_date')
                     ->where('scheduled_delivery_date', '<', now());

        // Role-based filtering
        if (Auth::user()->isCSR()) {
            $query->where('assigned_to', Auth::id());
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Assigned to filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $orders = $query->orderBy('scheduled_delivery_date', 'asc')->paginate(15);

        // Get filter options
        $statuses = ['new', 'scheduled', 'not_picking_calls', 'number_off', 'call_back', 'delivered', 'failed', 'paid'];
        $assignedUsers = User::whereHas('role', function($q) {
            $q->where('slug', 'csr');
        })->get(); // Only CSR
        $products = Product::all();

        return view('orders.overdue', compact('orders', 'statuses', 'assignedUsers', 'products'));
    }

    public function show(Order $order)
    {
        // Check if user can view this order
        if (Auth::user()->isCSR() && (string)$order->assigned_to !== (string)Auth::id()) {
            abort(403, 'You can only view orders assigned to you.');
        }

        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $order->load(['customer', 'product', 'assignedUser', 'statusHistory']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        $products = Product::where('is_active', true)->get();
        $customers = Customer::all();
        $users = User::whereHas('role', function($q) {
            $q->where('slug', 'csr');
        })->get();
        $agents = Agent::all();

        return view('orders.edit', compact('order', 'products', 'customers', 'users', 'agents'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:new,scheduled,not_picking_calls,number_off,call_back,delivered,cancelled,failed,paid',
            'callback_reminder' => 'nullable|date|required_if:status,call_back|after_or_equal:now',
            'assigned_to' => 'nullable|exists:users,id',
            'agent_id' => 'nullable|exists:agents,id',
            'source' => 'nullable|in:Website purchase,R or R,Messaging',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_price' => $request->quantity * $request->unit_price,
                'status' => $request->status,
                'agent_id' => $request->agent_id,
                'source' => $request->source,
                'notes' => $request->notes,
            ];

            // Set assigned_at when assigned_to changes
            if ($order->assigned_to !== $request->assigned_to) {
                $updateData['assigned_to'] = $request->assigned_to;
                $updateData['assigned_at'] = $request->assigned_to ? now() : null;
            }

            // Handle callback reminder
            if ($request->status === 'call_back' && $request->filled('callback_reminder')) {
                $updateData['callback_reminder'] = $request->callback_reminder;
            } elseif ($request->status !== 'call_back') {
                // Clear callback reminder if status is changed away from call_back
                $updateData['callback_reminder'] = null;
            }

            // Track if status changed to delivered
            $statusChangedToDelivered = $order->status !== 'delivered' && $request->status === 'delivered';

            // Track if status changed to call_back
            $oldStatus = $order->status;
            $statusChangedToCallBack = $oldStatus !== 'call_back' && $request->status === 'call_back';

            $order->update($updateData);

            DB::commit();

            // Send delivery notification email if status changed to delivered
            if ($statusChangedToDelivered) {
                try {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendDeliveryNotification($order->fresh(['customer', 'product', 'agent']));
                } catch (\Exception $e) {
                    // Log error but don't fail the order update
                    \Log::error('Failed to send delivery notification email: ' . $e->getMessage());
                }
            }

            // Send callback reminder email if status changed to call_back with reminder time
            if ($statusChangedToCallBack && $order->callback_reminder && $order->assignedUser) {
                try {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendCallbackReminder($order->fresh(['customer', 'product', 'assignedUser']));
                } catch (\Exception $e) {
                    // Log error but don't fail the order update
                    \Log::error('Failed to send callback reminder email: ' . $e->getMessage());
                }
            }

            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function invoice(Order $order)
    {
        return view('orders.invoice', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:new,scheduled,not_picking_calls,number_off,call_back,delivered,cancelled,failed,paid',
            'scheduled_delivery_date' => 'nullable|date',
            'callback_reminder' => 'nullable|date|required_if:status,call_back|after_or_equal:now',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldStatus = $order->status;

        // Track if status changed to delivered
        $statusChangedToDelivered = $oldStatus !== 'delivered' && $request->status === 'delivered';

        // Track if status changed to call_back
        $statusChangedToCallBack = $oldStatus !== 'call_back' && $request->status === 'call_back';

        // Update order with additional fields
        $updateData = [
            'status' => $request->status,
            'updated_at' => now()
        ];

        if ($request->filled('scheduled_delivery_date')) {
            $updateData['scheduled_delivery_date'] = $request->scheduled_delivery_date;
        }

        if ($request->filled('tracking_number')) {
            $updateData['tracking_number'] = $request->tracking_number;
        }

        // Handle callback reminder
        if ($request->status === 'call_back' && $request->filled('callback_reminder')) {
            $updateData['callback_reminder'] = $request->callback_reminder;
        } elseif ($request->status !== 'call_back') {
            // Clear callback reminder if status is changed away from call_back
            $updateData['callback_reminder'] = null;
        }

        $order->update($updateData);

        // Create detailed status change message
        $statusChangeMessage = "Status changed from '" . ucfirst(str_replace('_', ' ', $oldStatus)) . "' to '" . ucfirst(str_replace('_', ' ', $request->status)) . "'";

        if ($request->filled('notes')) {
            $statusChangeMessage .= ". Notes: " . $request->notes;
        }

        // Add to status history
        $order->statusHistory()->create([
            'status' => $request->status,
            'notes' => $statusChangeMessage,
            'changed_by' => Auth::id()
        ]);

        // Send delivery notification email if status changed to delivered
        if ($statusChangedToDelivered) {
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendDeliveryNotification($order->fresh(['customer', 'product', 'agent']));
            } catch (\Exception $e) {
                // Log error but don't fail the status update
                \Log::error('Failed to send delivery notification email: ' . $e->getMessage());
            }
        }

        // Send callback reminder email if status changed to call_back with reminder time
        if ($statusChangedToCallBack && $order->callback_reminder && $order->assignedUser) {
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendCallbackReminder($order->fresh(['customer', 'product', 'assignedUser']));
            } catch (\Exception $e) {
                // Log error but don't fail the status update
                \Log::error('Failed to send callback reminder email: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function assignOrder(Request $request, Order $order)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $oldAssignedTo = $order->assigned_to;
        $newUser = User::find($request->assigned_to);

        // Get the old user name before updating
        $oldUser = $oldAssignedTo ? User::find($oldAssignedTo) : null;

        $order->update([
            'assigned_to' => $request->assigned_to,
            'assigned_at' => now()
        ]);

        // Add to status history
        $order->statusHistory()->create([
            'status' => $order->status,
            'notes' => $oldAssignedTo ?
                "Order reassigned from " . $oldUser->name . " to " . $newUser->name :
                "Order assigned to " . $newUser->name,
            'changed_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Order assigned successfully.');
    }

    public function create()
    {
        // Only CSR can add orders
        if (!Auth::user()->isCSR()) {
            abort(403, 'Only CSR users can add orders.');
        }

        $products = Product::all();
        $agents = Agent::where('status', 'active')->get();
        // Exclude 'paid' from manual status selection - it's automatically set when payment is added
        $statuses = ['new', 'scheduled', 'not picking calls', 'number off', 'call back', 'delivered', 'failed'];

        return view('orders.create', compact('products', 'agents', 'statuses'));
    }

    public function store(Request $request)
    {
        // Only CSR can add orders
        if (!Auth::user()->isCSR()) {
            abort(403, 'Only CSR users can add orders.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'required|string|max:500',
            'customer_state' => 'required|string|max:100',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:new,scheduled,not picking calls,number off,call back,delivered,failed,paid',
            'notes' => 'nullable|string|max:500',
            'agent_id' => 'nullable|exists:agents,id',
            'source' => 'nullable|in:Website purchase,R or R,Messaging',
        ]);

        DB::beginTransaction();
        try {
            // Create or find customer by name + phone combination
            $customer = Customer::where('name', $request->customer_name)
                ->where('phone', $request->customer_phone)
                ->first();

            if (!$customer) {
                // Create new customer
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'whatsapp_number' => $request->customer_whatsapp,
                    'address' => $request->customer_address,
                    'state' => $request->customer_state,
                ]);
            } else {
                // Update customer info if it exists (update email if provided and customer doesn't have one, or if provided)
                $updateData = [
                    'whatsapp_number' => $request->customer_whatsapp,
                    'address' => $request->customer_address,
                    'state' => $request->customer_state,
                ];
                // Only update email if provided and customer doesn't have one, or always update if provided
                if ($request->customer_email) {
                    $updateData['email'] = $request->customer_email;
                }
                $customer->update($updateData);
            }

            // Generate order number
            $orderNumber = 'ORD-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);

            // Calculate total price
            $totalPrice = $request->quantity * $request->unit_price;

            // Assign order to available CSR randomly (respecting max_orders_per_day and is_active)
            $assignedCsr = $this->getAvailableCSR();
            
            if (!$assignedCsr) {
                DB::rollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No available CSR to assign order. All CSRs are either inactive or have reached their daily order limit.');
            }

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
                'assigned_to' => $assignedCsr->id, // Assign to random available CSR
                'assigned_at' => now(), // Set assignment timestamp
                'agent_id' => $request->agent_id,
                'source' => $request->source,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total_price' => $totalPrice,
                'status' => $request->status,
                'notes' => $request->notes,
                'payment_status' => 'pending',
                'amount_paid' => 0,
            ]);

            // Add initial status to history
            $order->statusHistory()->create([
                'status' => $request->status,
                'notes' => 'Order created',
                'changed_by' => Auth::id(),
                'changed_at' => now()
            ]);

            DB::commit();

            // Send order confirmation email to customer
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendOrderConfirmation($order->fresh(['customer', 'product']));
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            // For CSR users, redirect to orders list instead of order view to avoid permission issues
            if (Auth::user()->isCSR()) {
                return redirect()->route('orders.index')
                               ->with('success', 'Order created successfully!');
            }

            return redirect()->route('orders.show', $order)
                           ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Get an available CSR for order assignment
     * - Only active CSRs (is_active = true)
     * - Must not exceed max_orders_per_day
     * - Random selection from available CSRs
     * 
     * @return User|null
     */
    private function getAvailableCSR()
    {
        // Get all active CSR users
        $activeCSRs = User::whereHas('role', function($query) {
            $query->where('slug', 'csr');
        })
        ->where('is_active', true)
        ->get();

        if ($activeCSRs->isEmpty()) {
            return null; // No active CSRs available
        }

        // Filter CSRs who haven't reached their daily limit
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $availableCSRs = $activeCSRs->filter(function($csr) use ($todayStart, $todayEnd) {
            // Count orders assigned to this CSR today
            $todayOrdersCount = Order::where('assigned_to', $csr->id)
                ->whereBetween('assigned_at', [$todayStart, $todayEnd])
                ->count();

            // Check if CSR has reached their daily limit
            $maxOrders = $csr->max_orders_per_day ?? 50; // Default to 50 if not set
            return $todayOrdersCount < $maxOrders;
        });

        if ($availableCSRs->isEmpty()) {
            return null; // All CSRs have reached their daily limit
        }

        // Randomly select from available CSRs
        return $availableCSRs->random();
    }
}

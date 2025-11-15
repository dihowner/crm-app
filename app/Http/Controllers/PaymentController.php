<?php

namespace App\Http\Controllers;

use App\Models\PaymentRecord;
use App\Models\Order;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Show delivered and paid orders on payment records page
        $query = Order::with(['customer', 'product', 'assignedUser', 'agent', 'paymentRecords'])
                     ->whereIn('status', ['delivered', 'paid']);

        // Role-based filtering
        if (Auth::user()->isCSR()) {
            $query->where('assigned_to', Auth::id());
        }
        // Logistic Managers and Admins see all orders

        // Date filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->whereHas('paymentRecords');
            } elseif ($request->payment_status === 'unpaid') {
                $query->whereDoesntHave('paymentRecords');
            }
        }

        // CSR filter (Admin only)
        if ($request->filled('csr_id') && Auth::user()->isAdmin()) {
            $query->where('assigned_to', $request->csr_id);
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
        $paymentStatuses = [
            'all' => 'All',
            'paid' => 'Paid',
            'unpaid' => 'Unpaid'
        ];

        // Get CSRs for admin filter
        $csrs = collect();
        if (Auth::user()->isAdmin()) {
            $csrs = User::whereHas('role', function ($q) {
                $q->where('slug', 'csr');
            })->get();
        }

        return view('payments.index', compact('orders', 'paymentStatuses', 'csrs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Only Logistic Managers can add payment records
        if (Auth::user()->isAdmin() || Auth::user()->isCSR()) {
            return response()->json(['error' => 'You do not have permission to add payment records.'], 403);
        }

        // Check if user can add payment for this order
        $order = Order::findOrFail($request->order_id);
        if (!$order || $order->status !== 'delivered') {
            return response()->json(['error' => 'Payment records can only be added to delivered orders.'], 403);
        }

        try {
            DB::beginTransaction();

            $paymentRecord = PaymentRecord::create([
                'order_id' => $request->order_id,
                'agent_id' => $order->agent_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
                'recorded_by' => Auth::id()
            ]);

            // Update order status to 'paid' when payment is added
            $order->update([
                'status' => 'paid'
            ]);

            DB::commit();

            // Send payment confirmation email to customer
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendPaymentConfirmation(
                    $order->fresh(['customer', 'product']),
                    $paymentRecord->fresh()
                );
            } catch (\Exception $e) {
                // Log error but don't fail the payment record creation
                \Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment record added successfully!',
                'payment' => [
                    'amount' => number_format($paymentRecord->amount, 2),
                    'date' => $paymentRecord->payment_date->format('M d, Y'),
                    'notes' => $paymentRecord->notes
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to add payment record: ' . $e->getMessage()], 500);
        }
    }

    public function getOrderPayments(Order $order)
    {
        // Check if user can view payments for this order
        if (Auth::user()->isCSR() && (string)$order->assigned_to !== (string)Auth::id()) {
            return response()->json(['error' => 'You can only view payments for orders assigned to you.'], 403);
        }

        $payments = $order->paymentRecords()->with('recordedBy')->get();

        return response()->json([
            'payments' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => number_format($payment->amount, 2),
                    'payment_date' => $payment->payment_date->format('M d, Y'),
                    'notes' => $payment->notes,
                    'recorded_by' => $payment->recordedBy->name ?? 'Unknown',
                    'recorded_at' => $payment->recorded_at->format('M d, Y H:i')
                ];
            })
        ]);
    }
}

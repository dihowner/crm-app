<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(7);
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        // Base query for orders based on user role
        $baseQuery = Order::query();

        if ($user->isCSR()) {
            // CSR sees only their assigned orders
            $baseQuery->where('assigned_to', $user->id);
        }
        // Admin and Logistic Manager see all orders

        // Calculate metrics
        $metrics = [
            'total_orders' => (clone $baseQuery)
                ->where('created_at', '>=', $sevenDaysAgo)
                ->count(),

            'new_orders' => (clone $baseQuery)
                ->where('created_at', '>=', $todayStart)
                ->where('created_at', '<=', $todayEnd)
                ->count(),

            'scheduled_orders' => (clone $baseQuery)
                ->where('status', 'scheduled')
                ->where('created_at', '>=', $sevenDaysAgo)
                ->count(),

            'delivered_today' => (clone $baseQuery)
                ->where('status', 'delivered')
                ->where('updated_at', '>=', $todayStart)
                ->where('updated_at', '<=', $todayEnd)
                ->count(),
        ];

        // Get recent orders (last 10)
        $recentOrders = (clone $baseQuery)
            ->with(['customer', 'product', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get unassigned orders count (Admin only)
        $unassignedOrdersCount = 0;
        if ($user->isAdmin()) {
            $unassignedOrdersCount = Order::whereNull('assigned_to')
                ->where('status', 'new')
                ->count();
        }

        // Get my orders count (CSR only)
        $myOrdersCount = 0;
        $myTodayOrdersCount = 0;
        if ($user->isCSR()) {
            $myOrdersCount = Order::where('assigned_to', $user->id)->count();
            $myTodayOrdersCount = Order::where('assigned_to', $user->id)
                ->whereDate('assigned_at', today())
                ->count();
        }

        return view('dashboard.index', compact(
            'user',
            'metrics',
            'recentOrders',
            'unassignedOrdersCount',
            'myOrdersCount',
            'myTodayOrdersCount'
        ));
    }
}

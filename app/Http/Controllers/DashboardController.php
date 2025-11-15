<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Agent;
use App\Models\Product;
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

        // Logistic Manager specific metrics
        $logisticMetrics = [];
        if ($user->isLogisticManager()) {
            // Inventory stats
            $totalInventoryItems = Inventory::count();
            $lowStockItems = Inventory::whereRaw('quantity <= low_stock_threshold')->count();
            $outOfStockItems = Inventory::where('quantity', 0)->count();
            
            // Calculate total stock value (using product price or inventory selling price)
            $totalStockValue = Inventory::join('products', 'inventory.product_id', '=', 'products.id')
                ->selectRaw('SUM(inventory.quantity * COALESCE(inventory.selling_price, products.price, 0)) as total_value')
                ->value('total_value') ?? 0;

            // Delivery stats
            $deliveredToday = Order::where('status', 'delivered')
                ->whereDate('updated_at', today())
                ->count();
            $deliveredThisWeek = Order::where('status', 'delivered')
                ->whereBetween('updated_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
                ->count();
            $ordersWithAgents = Order::whereNotNull('agent_id')->count();
            $paidOrders = Order::where('status', 'paid')->count();

            // Agent stats
            $activeAgents = Agent::where('status', 'active')->count();
            $agentsWithOrders = Agent::whereHas('orders')->where('status', 'active')->count();

            $logisticMetrics = [
                'total_inventory_items' => $totalInventoryItems,
                'low_stock_items' => $lowStockItems,
                'out_of_stock_items' => $outOfStockItems,
                'total_stock_value' => $totalStockValue,
                'delivered_today' => $deliveredToday,
                'delivered_this_week' => $deliveredThisWeek,
                'orders_with_agents' => $ordersWithAgents,
                'paid_orders' => $paidOrders,
                'active_agents' => $activeAgents,
                'agents_with_orders' => $agentsWithOrders,
            ];
        }

        return view('dashboard.index', compact(
            'user',
            'metrics',
            'recentOrders',
            'unassignedOrdersCount',
            'myOrdersCount',
            'myTodayOrdersCount',
            'logisticMetrics'
        ));
    }
}

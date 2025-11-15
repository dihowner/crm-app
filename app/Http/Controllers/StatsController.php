<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Admin users can access statistics.');
        }

        // Logistic Managers can only access inventory pages
        if (Auth::user()->isLogisticManager()) {
            abort(403, 'Access denied. Logistic Managers can only access inventory management.');
        }

        // Get filter parameters
        $filter = $request->get('filter', '7_days'); // Default to 7 days
        $startDate = $this->getStartDate($filter);
        $endDate = now();

        // Total Summary Statistics
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $weekOrders = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthOrders = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        // Commission per delivered order
        $commissionPerOrder = 6000;
        
        // Revenue calculations - Revenue = total_price - commission per delivered order
        $deliveredOrdersCount = Order::where('status', 'delivered')->count();
        $totalRevenueRaw = Order::where('status', 'delivered')->sum('total_price');
        $totalRevenue = $totalRevenueRaw - ($deliveredOrdersCount * $commissionPerOrder);
        
        // Today's Revenue - revenue from today's delivered orders
        $todayDeliveredOrders = Order::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->get();
        $todayRevenueRaw = $todayDeliveredOrders->sum('total_price');
        $todayDeliveredCount = $todayDeliveredOrders->count();
        $todayRevenue = $todayRevenueRaw - ($todayDeliveredCount * $commissionPerOrder);

        // Fulfillment and failure rates
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $failedOrders = Order::where('status', 'failed')->count();
        $fulfillmentRate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;
        $failedDeliveryRate = $totalOrders > 0 ? ($failedOrders / $totalOrders) * 100 : 0;

        // Customer Insights
        // Track by duplicate customer records in customers table (same name+phone appearing multiple times)
        // New Customers: Unique name+phone combinations in orders within filtered period that DON'T have duplicates in customers table
        $newCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereNotExists(function ($query) {
                // Check if this name+phone appears multiple times in customers table (duplicate record)
                $query->select(DB::raw(1))
                    ->from('customers as other_customers')
                    ->whereRaw('other_customers.name = customers.name')
                    ->whereRaw('other_customers.phone = customers.phone')
                    ->whereColumn('other_customers.id', '!=', 'customers.id'); // Different customer record
            })
            ->distinct()
            ->select('customers.name', 'customers.phone')
            ->count();
        
        // Returning Customers: Customers with duplicate records in customers table (same name+phone appearing multiple times)
        // Count unique name+phone combinations that have multiple customer records
        $returningCustomers = DB::table('customers')
            ->select('name', 'phone', DB::raw('COUNT(*) as count'))
            ->groupBy('name', 'phone')
            ->having('count', '>', 1) // Has duplicate records
            ->count();
        
        // Chat Customers: Orders with source = "Messaging"
        $chatCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.source', 'Messaging')
            ->distinct()
            ->select('customers.name', 'customers.phone')
            ->count();

        // Product Statistics
        $productStats = $this->getProductStats($startDate, $endDate);

        // Revenue by Product (for chart)
        $revenueByProduct = $this->getRevenueByProduct($startDate, $endDate);

        return view('stats.index', compact(
            'filter',
            'totalOrders',
            'todayOrders',
            'weekOrders',
            'monthOrders',
            'totalRevenue',
            'todayRevenue',
            'fulfillmentRate',
            'failedDeliveryRate',
            'newCustomers',
            'returningCustomers',
            'chatCustomers',
            'productStats',
            'revenueByProduct'
        ));
    }

    private function getStartDate($filter)
    {
        switch ($filter) {
            case '7_days':
                return now()->subDays(7);
            case '30_days':
                return now()->subDays(30);
            case 'this_month':
                return now()->startOfMonth();
            case 'last_month':
                return now()->subMonth()->startOfMonth();
            case 'this_year':
                return now()->startOfYear();
            default:
                return now()->subDays(7);
        }
    }

    private function getProductStats($startDate, $endDate)
    {
        $products = Product::withCount(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->withCount(['orders as today_orders' => function ($query) {
            $query->whereDate('created_at', today());
        }])
        ->withCount(['orders as week_orders' => function ($query) {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }])
        ->withCount(['orders as month_orders' => function ($query) {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }])
        ->withCount(['orders as delivered_orders' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'delivered')
                  ->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->withCount(['orders as failed_orders' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'failed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get();

        return $products->map(function ($product) {
            $totalOrders = $product->orders_count;
            $deliveredOrders = $product->delivered_orders_count;
            $failedOrders = $product->failed_orders_count;

            $fulfillmentRate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;
            $failedRate = $totalOrders > 0 ? ($failedOrders / $totalOrders) * 100 : 0;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'total_orders' => $totalOrders,
                'today_orders' => $product->today_orders_count,
                'week_orders' => $product->week_orders_count,
                'month_orders' => $product->month_orders_count,
                'fulfillment_rate' => round($fulfillmentRate, 2),
                'failed_delivery_rate' => round($failedRate, 2)
            ];
        });
    }

    private function getRevenueByProduct($startDate, $endDate)
    {
        $commissionPerOrder = 6000;
        
        return Product::withCount([
            'orders as delivered_orders_count' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'delivered')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
        ->withSum(['orders' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'delivered')
                  ->whereBetween('created_at', [$startDate, $endDate]);
        }], 'total_price')
        ->get()
        ->map(function ($product) use ($commissionPerOrder) {
            $revenueRaw = $product->orders_sum_total_price ?? 0;
            $deliveredCount = $product->delivered_orders_count ?? 0;
            $revenue = $revenueRaw - ($deliveredCount * $commissionPerOrder);
            
            return [
                'name' => $product->name,
                'revenue' => $revenue
            ];
        })
        ->sortByDesc('revenue')
        ->take(15) // Top 15 products
        ->values();
    }

    public function getChartData(Request $request)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $filter = $request->get('filter', '7_days');
        $startDate = $this->getStartDate($filter);
        $endDate = now();

        // Revenue by Product data for chart
        $revenueData = $this->getRevenueByProduct($startDate, $endDate);

        return response()->json([
            'revenue_by_product' => $revenueData
        ]);
    }
}

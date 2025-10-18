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

        // Filtered orders for the selected period
        $filteredOrders = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Revenue calculations
        $totalRevenue = Order::where('status', 'delivered')->sum('total_price');
        $filteredRevenue = $filteredOrders->where('status', 'delivered')->sum('total_price');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Fulfillment and failure rates
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $failedOrders = Order::where('status', 'failed')->count();
        $fulfillmentRate = $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;
        $failedDeliveryRate = $totalOrders > 0 ? ($failedOrders / $totalOrders) * 100 : 0;

        // Customer Insights
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $returningCustomers = Customer::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->where('created_at', '<', $startDate)->count();
        $chatCustomers = Customer::whereHas('orders', function ($query) {
            $query->where('status', 'not picking calls');
        })->count();

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
            'filteredRevenue',
            'avgOrderValue',
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
        ->withCount(['orders as delivered_orders' => function ($query) {
            $query->where('status', 'delivered');
        }])
        ->withCount(['orders as failed_orders' => function ($query) {
            $query->where('status', 'failed');
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
        return Product::withSum(['orders' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'delivered')
                  ->whereBetween('created_at', [$startDate, $endDate]);
        }], 'total_price')
        ->get()
        ->map(function ($product) {
            return [
                'name' => $product->name,
                'revenue' => $product->orders_sum_total_price ?? 0
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

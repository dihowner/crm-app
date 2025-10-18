<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffPerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Admin users can access Staff Performance.');
        }

        // Get filter parameters
        $productId = $request->get('product_id');
        $staffId = $request->get('staff_id');
        $timeframe = $request->get('timeframe', '30_days');
        $timeRange = $request->get('time_range', '30_days');

        // Get date range based on timeframe
        $dateRange = $this->getDateRange($timeframe);
        $timeRangeDates = $this->getDateRange($timeRange);

        // Get products for filter dropdown
        $products = Product::orderBy('name')->get();

        // Get staff for filter dropdown
        $staff = User::whereHas('orders')->withCount('orders')->orderBy('name')->get();

        // Get top performing staff
        $topStaff = $this->getTopPerformingStaff($productId, $staffId, $timeRangeDates, 20);

        // Get individual staff performance if staff is selected
        $staffPerformance = null;
        if ($staffId) {
            $staffPerformance = $this->getStaffPerformance($staffId, $productId, $dateRange);
        }

        return view('staff-performance.index', compact(
            'products',
            'staff',
            'topStaff',
            'staffPerformance',
            'productId',
            'staffId',
            'timeframe',
            'timeRange'
        ));
    }

    private function getDateRange($timeframe)
    {
        switch ($timeframe) {
            case '7_days':
                return [
                    'start' => now()->subDays(7),
                    'end' => now()
                ];
            case '30_days':
                return [
                    'start' => now()->subDays(30),
                    'end' => now()
                ];
            case 'this_month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'last_month':
                return [
                    'start' => now()->subMonth()->startOfMonth(),
                    'end' => now()->subMonth()->endOfMonth()
                ];
            case 'this_year':
                return [
                    'start' => now()->startOfYear(),
                    'end' => now()->endOfYear()
                ];
            default:
                return [
                    'start' => now()->subDays(30),
                    'end' => now()
                ];
        }
    }

    private function getTopPerformingStaff($productId, $staffId, $dateRange, $limit = 20)
    {
        $query = User::withCount([
            'orders as delivered_orders_count' => function ($query) use ($productId, $dateRange) {
                $query->where('status', 'delivered')
                      ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            },
            'orders as total_orders_count' => function ($query) use ($productId, $dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            }
        ])
        ->withSum([
            'orders as total_revenue' => function ($query) use ($productId, $dateRange) {
                $query->where('status', 'delivered')
                      ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                if ($productId) {
                    $query->where('product_id', $productId);
                }
            }
        ], 'total_price')
        ->whereHas('orders', function ($query) use ($productId, $dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            if ($productId) {
                $query->where('product_id', $productId);
            }
        });

        if ($staffId) {
            $query->where('id', $staffId);
        }

        return $query->orderBy('delivered_orders_count', 'desc')
                    ->orderBy('total_revenue', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($user, $index) {
                        $deliveryRate = $user->total_orders_count > 0
                            ? ($user->delivered_orders_count / $user->total_orders_count) * 100
                            : 0;

                        return [
                            'rank' => $index + 1,
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'delivered_orders' => $user->delivered_orders_count,
                            'total_orders' => $user->total_orders_count,
                            'total_revenue' => $user->total_revenue ?? 0,
                            'delivery_rate' => round($deliveryRate, 2)
                        ];
                    });
    }

    private function getStaffPerformance($staffId, $productId, $dateRange)
    {
        $staff = User::findOrFail($staffId);

        $query = Order::where('assigned_to', $staffId)
                     ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $orders = $query->get();

        $deliveredOrders = $orders->where('status', 'delivered');
        $totalRevenue = $deliveredOrders->sum('total_price');
        $deliveryRate = $orders->count() > 0 ? ($deliveredOrders->count() / $orders->count()) * 100 : 0;

        // Get performance by status
        $statusBreakdown = $orders->groupBy('status')->map(function ($statusOrders) {
            return $statusOrders->count();
        });

        // Get performance by product (if no specific product selected)
        $productBreakdown = [];
        if (!$productId) {
            $productBreakdown = $orders->groupBy('product_id')->map(function ($productOrders, $productId) {
                $product = Product::find($productId);
                return [
                    'product_name' => $product ? $product->name : 'Unknown',
                    'orders_count' => $productOrders->count(),
                    'delivered_count' => $productOrders->where('status', 'delivered')->count(),
                    'revenue' => $productOrders->where('status', 'delivered')->sum('total_price')
                ];
            });
        }

        return [
            'staff' => $staff,
            'total_orders' => $orders->count(),
            'delivered_orders' => $deliveredOrders->count(),
            'total_revenue' => $totalRevenue,
            'delivery_rate' => round($deliveryRate, 2),
            'status_breakdown' => $statusBreakdown,
            'product_breakdown' => $productBreakdown,
            'date_range' => $dateRange
        ];
    }

    public function getStaffStats(Request $request)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $staffId = $request->get('staff_id');
        $productId = $request->get('product_id');
        $timeframe = $request->get('timeframe', '30_days');

        if (!$staffId) {
            return response()->json(['error' => 'Staff ID is required'], 400);
        }

        $dateRange = $this->getDateRange($timeframe);
        $staffPerformance = $this->getStaffPerformance($staffId, $productId, $dateRange);

        return response()->json($staffPerformance);
    }
}

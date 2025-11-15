@extends('layouts.dashboard')

@section('page-title', 'CRM Stats Dashboard')

@section('content')
<!-- Filter Section and Total Summary -->
<div class="row mb-4">
    <!-- Filter Statistics -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">Filter Statistics</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('stats.index') }}">
                    <div class="mb-3">
                        <label for="filter" class="form-label">Time Period</label>
                        <select class="form-select" id="filter" name="filter" onchange="this.form.submit()">
                            <option value="7_days" {{ $filter == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30_days" {{ $filter == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ $filter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_year" {{ $filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Total Summary -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">Total Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-primary mb-1">{{ number_format($totalOrders) }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-success mb-1">{{ number_format($todayOrders) }}</h3>
                            <p class="text-muted mb-0">Today</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-info mb-1">{{ number_format($weekOrders) }}</h3>
                            <p class="text-muted mb-0">This Week</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-warning mb-1">{{ number_format($monthOrders) }}</h3>
                            <p class="text-muted mb-0">This Month</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-primary mb-1">{{ number_format($fulfillmentRate, 1) }}%</h3>
                            <p class="text-muted mb-0">Fulfillment Rate</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center">
                            <h3 class="text-danger mb-1">{{ number_format($failedDeliveryRate, 1) }}%</h3>
                            <p class="text-muted mb-0">Failed Delivery Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue and Customer Insights -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">Revenue Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <h2 class="text-success mb-1">₦{{ number_format($totalRevenue, 2) }}</h2>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h2 class="text-primary mb-1">₦{{ number_format($todayRevenue, 2) }}</h2>
                            <p class="text-muted mb-0">Today's Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">Customer Insights</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="text-success mb-1">{{ number_format($newCustomers) }}</h3>
                            <p class="text-muted mb-0">New Customers</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="text-info mb-1">{{ number_format($chatCustomers) }}</h3>
                            <p class="text-muted mb-0">Chat Customers</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <h3 class="text-warning mb-1">{{ number_format($returningCustomers) }}</h3>
                            <p class="text-muted mb-0">Returning Customers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Statistics Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">Total Summary per Product</h4>
            </div>
            <div class="card-body">
                @if($productStats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Total Orders</th>
                                    <th>Today</th>
                                    <th>This Week</th>
                                    <th>This Month</th>
                                    <th>Fulfillment Rate</th>
                                    <th>Failed Delivery Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productStats as $product)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $product['name'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ number_format($product['total_orders']) }}</span>
                                        </td>
                                        <td>{{ number_format($product['today_orders']) }}</td>
                                        <td>{{ number_format($product['week_orders']) }}</td>
                                        <td>{{ number_format($product['month_orders']) }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($product['fulfillment_rate'], 1) }}%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ number_format($product['failed_delivery_rate'], 1) }}%</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-chart-bar text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No Product Statistics</h5>
                        <p class="text-muted">No product data available for the selected period.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

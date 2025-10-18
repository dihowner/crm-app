@extends('layouts.dashboard')

@section('page-title', 'Staff Performance Tracker')

@section('content')
<!-- Staff Performance Tracker Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">
                    <i class="ti ti-user me-2"></i>Staff Performance Tracker
                </h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('staff-performance.index') }}" id="performanceForm">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Select Product</label>
                                <select class="form-select" id="product_id" name="product_id">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Select Staff</label>
                                <select class="form-select" id="staff_id" name="staff_id">
                                    <option value="">All Staff</option>
                                    @foreach($staff as $staffMember)
                                        <option value="{{ $staffMember->id }}" {{ $staffId == $staffMember->id ? 'selected' : '' }}>
                                            {{ $staffMember->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="timeframe" class="form-label">Timeframe</label>
                                <select class="form-select" id="timeframe" name="timeframe">
                                    <option value="7_days" {{ $timeframe == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                                    <option value="30_days" {{ $timeframe == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                                    <option value="this_month" {{ $timeframe == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ $timeframe == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_year" {{ $timeframe == 'this_year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-chart-bar me-1"></i>View Stats
                                    </button>
                                    <a href="{{ route('staff-performance.index') }}" class="btn btn-light">
                                        <i class="ti ti-x me-1"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Individual Staff Performance (if staff selected) -->
@if($staffPerformance)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">
                    <i class="ti ti-user-check me-2"></i>{{ $staffPerformance['staff']->name }} - Performance Details
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-primary-subtle">
                            <div class="card-body text-center">
                                <h3 class="text-primary mb-1">{{ $staffPerformance['total_orders'] }}</h3>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-success-subtle">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-1">{{ $staffPerformance['delivered_orders'] }}</h3>
                                <p class="text-muted mb-0">Delivered Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-info-subtle">
                            <div class="card-body text-center">
                                <h3 class="text-info mb-1">{{ number_format($staffPerformance['delivery_rate'], 1) }}%</h3>
                                <p class="text-muted mb-0">Delivery Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card bg-warning-subtle">
                            <div class="card-body text-center">
                                <h3 class="text-warning mb-1">₦{{ number_format($staffPerformance['total_revenue'], 2) }}</h3>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Breakdown -->
                @if($staffPerformance['status_breakdown']->count() > 0)
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <h6 class="mb-3">Orders by Status</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffPerformance['status_breakdown'] as $status => $count)
                                        @php
                                            $percentage = $staffPerformance['total_orders'] > 0 ? ($count / $staffPerformance['total_orders']) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($status) }}</span>
                                            </td>
                                            <td>{{ $count }}</td>
                                            <td>{{ number_format($percentage, 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="mb-3">Performance by Product</h6>
                        @if($staffPerformance['product_breakdown']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Orders</th>
                                            <th>Delivered</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($staffPerformance['product_breakdown'] as $product)
                                            <tr>
                                                <td>{{ $product['product_name'] }}</td>
                                                <td>{{ $product['orders_count'] }}</td>
                                                <td>{{ $product['delivered_count'] }}</td>
                                                <td>₦{{ number_format($product['revenue'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No product breakdown available (specific product selected).</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Top Performing Staff Leaderboard -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">
                    <i class="ti ti-trophy me-2"></i>Top Performing Staff ({{ ucfirst(str_replace('_', ' ', $timeRange)) }})
                </h4>
                <div class="ms-auto">
                    <select class="form-select form-select-sm" id="timeRange" onchange="updateTimeRange()">
                        <option value="7_days" {{ $timeRange == '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30_days" {{ $timeRange == '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ $timeRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $timeRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if($topStaff->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Staff</th>
                                    <th>Delivered Orders</th>
                                    <th>Total Orders</th>
                                    <th>Delivery Rate</th>
                                    <th>Total Revenue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topStaff as $staff)
                                    <tr>
                                        <td>
                                        @if($staff['rank'] <= 3)
                                            @if($staff['rank'] == 1)
                                                <span class="badge bg-warning text-dark">🥇 #{{ $staff['rank'] }}</span>
                                            @elseif($staff['rank'] == 2)
                                                <span class="badge bg-secondary">🥈 #{{ $staff['rank'] }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">🥉 #{{ $staff['rank'] }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-dark">#{{ $staff['rank'] }}</span>
                                        @endif
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $staff['name'] }}</h6>
                                                <small class="text-muted">{{ $staff['email'] }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($staff['delivered_orders']) }}</span>
                                        </td>
                                        <td>{{ number_format($staff['total_orders']) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($staff['delivery_rate'], 1) }}%</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">₦{{ number_format($staff['total_revenue'], 2) }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewStaffDetails({{ $staff['id'] }})">
                                                <i class="ti ti-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-trophy text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No Staff Performance Data</h5>
                        <p class="text-muted">No performance data found for the selected criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateTimeRange() {
    const timeRange = document.getElementById('timeRange').value;
    const form = document.getElementById('performanceForm');
    const timeRangeInput = document.createElement('input');
    timeRangeInput.type = 'hidden';
    timeRangeInput.name = 'time_range';
    timeRangeInput.value = timeRange;
    form.appendChild(timeRangeInput);
    form.submit();
}

function viewStaffDetails(staffId) {
    const form = document.getElementById('performanceForm');
    const staffInput = document.createElement('input');
    staffInput.type = 'hidden';
    staffInput.name = 'staff_id';
    staffInput.value = staffId;
    form.appendChild(staffInput);
    form.submit();
}

// Auto-submit form when filters change
document.getElementById('product_id').addEventListener('change', function() {
    document.getElementById('performanceForm').submit();
});

document.getElementById('staff_id').addEventListener('change', function() {
    document.getElementById('performanceForm').submit();
});

document.getElementById('timeframe').addEventListener('change', function() {
    document.getElementById('performanceForm').submit();
});
</script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($user->isLogisticManager())
        <!-- Logistic Manager Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <h1 class="text-primary mb-3">Welcome, {{ $user->name }}!</h1>
                    <p class="text-muted fs-16 mb-4">You have access to manage inventory and delivery operations.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-info-circle me-3 fs-20"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Logistic Manager Access</h6>
                                    <p class="mb-0">You can manage inventory levels, track stock movements, and oversee delivery operations from the navigation menu.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @else
        <!-- Admin and CSR Dashboard -->

    <!-- Key Metrics Cards -->
    <div class="row g-3 dashboard-metrics">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-primary-subtle">
                                <span class="avatar-title rounded bg-primary text-white font-size-18">
                                    <i class="ti ti-shopping-cart"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Orders</p>
                            <h4 class="mb-0">{{ $metrics['total_orders'] }}</h4>
                            <p class="text-muted mb-0">Last 7 days</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="text-primary">
                                <i class="ti ti-trending-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-info-subtle">
                                <span class="avatar-title rounded bg-info text-white font-size-18">
                                    <i class="ti ti-plus"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">New Orders</p>
                            <h4 class="mb-0">{{ $metrics['new_orders'] }}</h4>
                            <p class="text-muted mb-0">Today</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="text-info">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-warning-subtle">
                                <span class="avatar-title rounded bg-warning text-white font-size-18">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Scheduled Orders</p>
                            <h4 class="mb-0">{{ $metrics['scheduled_orders'] }}</h4>
                            <p class="text-muted mb-0">Last 7 days</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="text-warning">
                                <i class="ti ti-calendar-event"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-success-subtle">
                                <span class="avatar-title rounded bg-success text-white font-size-18">
                                    <i class="ti ti-truck"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Delivered Today</p>
                            <h4 class="mb-0">{{ $metrics['delivered_today'] }}</h4>
                            <p class="text-muted mb-0">Today</p>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="text-success">
                                <i class="ti ti-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="ti ti-bolt me-2"></i>Quick Actions
                    </h5>
                    <div class="row">
                        @if($user->isAdmin())
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('orders.index', ['status' => 'unassigned']) }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-list me-2"></i>
                                    View Unassigned Orders
                                    @if($unassignedOrdersCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $unassignedOrdersCount }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif

                        @if($user->isCSR())
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('orders.index', ['assigned_to' => auth()->id()]) }}" class="btn btn-info w-100 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-user me-2"></i>
                                    My Orders
                                    @if($myOrdersCount > 0)
                                        <span class="badge bg-light text-dark ms-2">{{ $myOrdersCount }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('orders.index', ['assigned_to' => auth()->id(), 'today' => '1']) }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-calendar me-2"></i>
                                    New Orders
                                    @if($myTodayOrdersCount > 0)
                                        <span class="badge bg-light text-dark ms-2">{{ $myTodayOrdersCount }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('orders.create') }}" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-plus me-2"></i>
                                    Add New Order
                                </a>
                            </div>
                        @endif

                        @if($user->isLogisticManager())
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('inventory.index') }}" class="btn btn-warning w-100 d-flex align-items-center justify-content-center">
                                    <i class="ti ti-package me-2"></i>
                                    Inventory Management
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="ti ti-clock me-2"></i>Recent Orders
                    </h5>

                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->customer->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $order->customer->phone ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $order->product->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'new' => 'bg-primary',
                                                        'scheduled' => 'bg-warning',
                                                        'delivered' => 'bg-success',
                                                        'not picking calls' => 'bg-danger',
                                                        'number off' => 'bg-secondary',
                                                        'call back' => 'bg-info'
                                                    ];
                                                    $statusColor = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                                    $statusDisplay = ucfirst($order->status);
                                                @endphp
                                                <span class="badge {{ $statusColor }} text-white">{{ $statusDisplay }}</span>
                                            </td>
                                            <td>
                                                {{ $order->assignedUser->name ?? 'Unassigned' }}
                                            </td>
                                            <td>
                                                {{ $order->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No Recent Orders</h5>
                            <p class="text-muted">No orders have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

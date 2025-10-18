@extends('layouts.dashboard')

@section('page-title', 'Orders List')

@section('content')


<!-- Summary Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-theme">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Total Orders:</strong> {{ $orders->total() }} orders found
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="order_status" class="form-label">Status</label>
                        <select class="form-select" id="order_status" name="status">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">All</option>
                            @foreach($assignedUsers as $user)
                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Search by name, phone, product..." value="{{ request('search') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="ti ti-x me-1"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $order->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $order->customer->phone }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $order->product->name }}</td>
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
                                    <td>{{ $order->assignedUser ? $order->assignedUser->name : 'Unassigned' }}</td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm" target="_blank">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-inbox fs-48 mb-3"></i>
                                            <h5>No orders found</h5>
                                            <p>Try adjusting your filters or check back later.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="pagination-container">
                        {{ $orders->appends(request()->query())->links('pagination.bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

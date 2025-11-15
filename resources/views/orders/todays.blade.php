@extends('layouts.dashboard')

@section('page-title', "Today's Orders")

@section('content')

<!-- Summary Section -->
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-theme">
            <i class="ti ti-calendar-check me-2"></i>
            <strong>Today's Orders:</strong> {{ $orders->total() }} orders created today
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('orders.todays') }}" class="row g-3">
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
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="source" class="form-label">Source</label>
                        <select class="form-select" id="source" name="source">
                            <option value="">All Sources</option>
                            <option value="Website purchase" {{ request('source') == 'Website purchase' ? 'selected' : '' }}>Website purchase</option>
                            <option value="R or R" {{ request('source') == 'R or R' ? 'selected' : '' }}>R or R</option>
                            <option value="Messaging" {{ request('source') == 'Messaging' ? 'selected' : '' }}>Messaging</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               placeholder="Search by name, phone, product..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="ti ti-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('orders.todays') }}" class="btn btn-secondary flex-fill">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
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
                                                'not_picking_calls' => 'bg-danger',
                                                'number off' => 'bg-secondary',
                                                'number_off' => 'bg-secondary',
                                                'call back' => 'bg-info',
                                                'call_back' => 'bg-info',
                                                'cancelled' => 'bg-dark',
                                                'failed' => 'bg-danger',
                                                'paid' => 'bg-success'
                                            ];
                                            $statusColor = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                            $statusDisplay = ucwords(str_replace('_', ' ', $order->status));
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
                                            <i class="ti ti-calendar-check fs-48 mb-3"></i>
                                            <h5>No orders for today</h5>
                                            <p>No orders were created today with the current filters.</p>
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

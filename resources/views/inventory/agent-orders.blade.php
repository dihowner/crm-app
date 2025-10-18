@extends('layouts.dashboard')

@section('page-title', $agent->name . ' - Orders')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">{{ $agent->name }} - Orders</h4>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                    <i class="ti ti-arrow-left me-1"></i>Back to Inventory
                </a>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $order->order_number }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $order->customer->name }}</h6>
                                                <small class="text-muted">{{ $order->customer->phone }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $order->product->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $order->quantity }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'new' => 'bg-primary',
                                                    'scheduled' => 'bg-warning',
                                                    'not picking calls' => 'bg-info',
                                                    'number off' => 'bg-secondary',
                                                    'call back' => 'bg-dark',
                                                    'delivered' => 'bg-success'
                                                ];
                                                $color = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $color }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td>{{ $order->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        {{ $orders->links('pagination.bootstrap-4') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-shopping-cart text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No Orders Found</h5>
                        <p class="text-muted">This agent doesn't have any orders yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

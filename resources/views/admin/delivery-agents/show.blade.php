@extends('layouts.admin')

@section('title', 'Delivery Agent Details')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">{{ $deliveryAgent->name }}</h4>
                            <p class="text-muted mb-0">Delivery agent profile and information</p>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.delivery-agents.edit', $deliveryAgent) }}" class="btn btn-primary">
                                    <i class="ti ti-edit me-1"></i>Edit Agent
                                </a>
                                <a href="{{ route('admin.delivery-agents.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Back to Agents
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Agent Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Agent Name</label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary-subtle me-2">
                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                {{ strtoupper(substr($deliveryAgent->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <h5 class="mb-0">{{ $deliveryAgent->name }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Phone Number</label>
                                    <p class="mb-0">
                                        @if($deliveryAgent->phone)
                                            <i class="ti ti-phone me-2"></i>{{ $deliveryAgent->phone }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Email Address</label>
                                    <p class="mb-0">
                                        @if($deliveryAgent->email)
                                            <i class="ti ti-mail me-2"></i>{{ $deliveryAgent->email }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    <div>
                                        @if($deliveryAgent->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="ti ti-user-check me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="ti ti-user-off me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($deliveryAgent->address)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Address</label>
                                    <p class="mb-0">
                                        <i class="ti ti-map-pin me-2"></i>{{ $deliveryAgent->address }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Stats</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary mb-1">{{ $deliveryAgent->inventories()->count() }}</h4>
                                            <small class="text-muted">Inventory Items</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-1">{{ $deliveryAgent->orders()->count() }}</h4>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Items -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Inventory Items</h5>
                            @if($deliveryAgent->inventories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Current Stock</th>
                                                <th>Low Stock Threshold</th>
                                                <th>Status</th>
                                                <th>Last Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveryAgent->inventories as $inventory)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $inventory->product->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $inventory->quantity }}</span>
                                                    </td>
                                                    <td>{{ $inventory->low_stock_threshold }}</td>
                                                    <td>
                                                        @if($inventory->quantity <= $inventory->low_stock_threshold)
                                                            <span class="badge bg-warning">Low Stock</span>
                                                        @else
                                                            <span class="badge bg-success">In Stock</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $inventory->updated_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-package text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No inventory items assigned to this agent</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Recent Orders</h5>
                            @if($deliveryAgent->orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveryAgent->orders->take(10) as $order)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('orders.show', $order) }}" class="text-primary">
                                                            #{{ $order->order_number }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $order->customer->name }}</td>
                                                    <td>{{ $order->product->name }}</td>
                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'new' => 'bg-primary',
                                                                'scheduled' => 'bg-info',
                                                                'not picking calls' => 'bg-warning',
                                                                'number off' => 'bg-secondary',
                                                                'call back' => 'bg-warning',
                                                                'delivered' => 'bg-success'
                                                            ];
                                                            $statusColor = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                                        @endphp
                                                        <span class="badge {{ $statusColor }}">{{ ucfirst($order->status) }}</span>
                                                    </td>
                                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-shopping-cart text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No orders assigned to this agent</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

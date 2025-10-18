@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">{{ $product->name }}</h4>
                            <p class="text-muted mb-0">Product Details</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>Edit Product
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Product Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Product Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Product Name:</strong></td>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SKU:</strong></td>
                                            <td>{{ $product->sku ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Category:</strong></td>
                                            <td><span class="badge bg-secondary">{{ $product->category }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td><strong>₦{{ number_format($product->price, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Weight:</strong></td>
                                            <td>{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dimensions:</strong></td>
                                            <td>{{ $product->dimensions ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge {{ $product->status_badge_class }}">
                                                    {{ $product->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $product->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @if($product->description)
                                            <tr>
                                                <td><strong>Description:</strong></td>
                                                <td>{{ $product->description }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Product Image Card -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Product Image</h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                             class="img-fluid rounded" style="max-height: 200px; max-width: 100%;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" style="height: 150px; background-color: #f8f9fa;">
                                            <div class="text-center">
                                                <i class="ti ti-photo text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2">No image available</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                            <i class="ti ti-edit me-2"></i>Edit Product
                                        </a>
                                        <a href="{{ route('admin.agent-inventory.index') }}?product_id={{ $product->id }}" class="btn btn-info">
                                            <i class="ti ti-package me-2"></i>View Inventory
                                        </a>
                                        <a href="{{ route('admin.product-forms.create') }}?product_id={{ $product->id }}" class="btn btn-success">
                                            <i class="ti ti-file-text me-2"></i>Create Form
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information and Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Stock Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-primary mb-1">{{ $product->stock_quantity }}</h3>
                                                <p class="text-muted mb-0">Current Stock</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-warning mb-1">{{ $product->low_stock_threshold }}</h3>
                                                <p class="text-muted mb-0">Low Stock Threshold</p>
                                            </div>
                                        </div>
                                    </div>
                                    @if($product->isLowStock())
                                        <div class="alert alert-warning mt-3">
                                            <i class="ti ti-alert-triangle me-2"></i>
                                            <strong>Low Stock Alert:</strong> This product is running low on stock!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Order Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-success mb-1">{{ $product->orders->count() }}</h3>
                                                <p class="text-muted mb-0">Total Orders</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-info mb-1">{{ $product->inventories->count() }}</h3>
                                                <p class="text-muted mb-0">Agent Inventories</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Recent Orders -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Recent Orders</h5>
                                </div>
                                <div class="card-body">
                                    @if($product->orders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Customer</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->orders->take(5) as $order)
                                                        <tr>
                                                            <td>{{ $order->order_number }}</td>
                                                            <td>{{ $order->customer->name }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $order->created_at->format('M d') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No orders found for this product.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

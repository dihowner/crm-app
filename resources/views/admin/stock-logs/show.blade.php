@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-1">Stock Log Details</h4>
                    <p class="text-muted mb-0">View stock movement information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.stock-logs.edit', $stockLog) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-2"></i>Edit Stock Log
                    </a>
                    <a href="{{ route('admin.stock-logs.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Stock Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Log Details -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Movement Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Product Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Product Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($stockLog->product->image_url)
                                            <img src="{{ $stockLog->product->image_url }}"
                                                 alt="{{ $stockLog->product->name }}"
                                                 class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h5 class="mb-1">{{ $stockLog->product->name }}</h5>
                                            @if($stockLog->product->sku)
                                                <p class="text-muted mb-0">SKU: {{ $stockLog->product->sku }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="30%"><strong>Category:</strong></td>
                                            <td><span class="badge bg-secondary">{{ $stockLog->product->category }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td><strong>₦{{ number_format($stockLog->product->price, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Current Stock:</strong></td>
                                            <td>{{ $stockLog->product->stock_quantity }} units</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Agent Details</h6>
                                </div>
                                <div class="card-body">
                                    <h5 class="mb-1">{{ $stockLog->agent->name }}</h5>
                                    <p class="text-muted mb-3">{{ $stockLog->agent->company_name }}</p>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td width="30%"><strong>Email:</strong></td>
                                            <td>{{ $stockLog->agent->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $stockLog->agent->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge {{ $stockLog->agent->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $stockLog->agent->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Movement Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Movement Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="text-primary mb-1">
                                                    @if($stockLog->quantity_changed > 0)
                                                        <span class="text-success">+{{ $stockLog->quantity_changed }}</span>
                                                    @elseif($stockLog->quantity_changed < 0)
                                                        <span class="text-danger">{{ $stockLog->quantity_changed }}</span>
                                                    @else
                                                        <span class="text-muted">0</span>
                                                    @endif
                                                </h4>
                                                <p class="text-muted mb-0">Quantity Changed</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <span class="badge {{ $stockLog->action_badge_class }} fs-6">
                                                    {{ $stockLog->action }}
                                                </span>
                                                <p class="text-muted mb-0 mt-2">Action Type</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h6 class="mb-1">{{ $stockLog->createdBy->name }}</h6>
                                                <p class="text-muted mb-0">{{ $stockLog->createdBy->role->name }}</p>
                                                <small class="text-muted">Created By</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h6 class="mb-1">{{ $stockLog->created_at->format('M d, Y') }}</h6>
                                                <p class="text-muted mb-0">{{ $stockLog->created_at->format('H:i A') }}</p>
                                                <small class="text-muted">Created At</small>
                                            </div>
                                        </div>
                                    </div>

                                    @if($stockLog->comment)
                                        <div class="mt-4">
                                            <h6>Comment:</h6>
                                            <div class="p-3 bg-light rounded">
                                                <p class="mb-0">{{ $stockLog->comment }}</p>
                                            </div>
                                        </div>
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

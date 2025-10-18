@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-1">Stock Logs</h4>
                    <p class="text-muted mb-0">Track all stock movements and inventory changes</p>
                </div>
                <div>
                    <a href="{{ route('admin.stock-logs.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Add Stock Log
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.stock-logs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Search products, agents, users...">
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <label for="agent_id" class="form-label">Agent</label>
                            <select class="form-select" id="agent_id" name="agent_id">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="action" class="form-label">Action</label>
                            <select class="form-select" id="action" name="action">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Agent</th>
                                    <th>Quantity Changed</th>
                                    <th>Action</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockLogs as $stockLog)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($stockLog->product->image_url)
                                                    <img src="{{ $stockLog->product->image_url }}"
                                                         alt="{{ $stockLog->product->name }}"
                                                         class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $stockLog->product->name }}</div>
                                                    @if($stockLog->product->sku)
                                                        <small class="text-muted">{{ $stockLog->product->sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $stockLog->agent->name }}</div>
                                                <small class="text-muted">{{ $stockLog->agent->company_name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($stockLog->quantity_changed > 0)
                                                <span class="text-success fw-bold">+{{ $stockLog->quantity_changed }}</span>
                                            @elseif($stockLog->quantity_changed < 0)
                                                <span class="text-danger fw-bold">{{ $stockLog->quantity_changed }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $stockLog->action_badge_class }}">
                                                {{ $stockLog->action }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $stockLog->createdBy->name }}</div>
                                                <small class="text-muted">{{ $stockLog->createdBy->role->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $stockLog->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $stockLog->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.stock-logs.show', $stockLog) }}"
                                                   class="btn btn-outline-primary btn-sm" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.stock-logs.edit', $stockLog) }}"
                                                   class="btn btn-outline-warning btn-sm" title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        title="Delete"
                                                        onclick="deleteStockLog({{ $stockLog->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-package-off" style="font-size: 3rem;"></i>
                                                <h5 class="mt-2">No stock logs found</h5>
                                                <p>Start by creating your first stock log entry.</p>
                                                <a href="{{ route('admin.stock-logs.create') }}" class="btn btn-primary">
                                                    <i class="ti ti-plus me-2"></i>Add Stock Log
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($stockLogs->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $stockLogs->firstItem() }} to {{ $stockLogs->lastItem() }}
                                of {{ $stockLogs->total() }} results
                            </div>
                            <div>
                                {{ $stockLogs->appends(request()->query())->links('pagination.bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteStockLog(stockLogId) {
    bootbox.confirm({
        message: 'Are you sure you want to delete this stock log? This action will also revert the inventory changes.',
        buttons: {
            confirm: {
                label: 'Yes, Delete',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                const form = document.getElementById('delete-form');
                form.action = `/admin/stock-logs/${stockLogId}`;
                form.submit();
            }
        }
    });
}
</script>
@endsection

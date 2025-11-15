@extends('layouts.admin')

@section('title', 'Add Stock - Agent Inventory')

@section('content')
<div class="container-fluid">
    <!-- Page title box -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            @php
                                $user = auth()->user();
                                if ($user->isAdmin()) {
                                    echo 'ADMIN Dashboard';
                                } elseif ($user->isCSR()) {
                                    echo 'CSR Dashboard';
                                } elseif ($user->isLogisticManager()) {
                                    echo 'Logistic Manager Dashboard';
                                } else {
                                    echo 'Dashboard';
                                }
                            @endphp
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.agent-inventory.index') }}">Agent Inventory</a></li>
                        <li class="breadcrumb-item active">Add Stock</li>
                    </ol>
                </div>
                <h4 class="page-title">Add Stock</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Stock to Inventory</h4>
                    <p class="text-muted mb-0">Add stock to an existing agent's inventory</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.agent-inventory.add-stock', $inventory) }}">
                        @csrf

                        <!-- Agent Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Agent</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <div class="avatar-sm rounded-circle bg-primary-subtle me-2">
                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                {{ strtoupper(substr($inventory->agent->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </span>
                                    <input type="text" class="form-control" value="{{ $inventory->agent->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Product</label>
                                <input type="text" class="form-control" value="{{ $inventory->product->name }}" readonly>
                            </div>
                        </div>

                        <!-- Current Stock Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Current Stock</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-package"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ $inventory->quantity }}" readonly>
                                    <span class="input-group-text">units</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Low Stock Threshold</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-alert-triangle"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ $inventory->low_stock_threshold }}" readonly>
                                    <span class="input-group-text">units</span>
                                </div>
                            </div>
                        </div>

                        <!-- Add Stock Form -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity" name="quantity" min="1" value="{{ old('quantity') }}" required>
                                <div class="form-text">Enter the number of units to add to the current stock</div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="reason" class="form-label">Reason (Optional)</label>
                                <input type="text" class="form-control @error('reason') is-invalid @enderror"
                                       id="reason" name="reason" value="{{ old('reason') }}"
                                       placeholder="e.g., Restock, Return, Manual addition">
                                <div class="form-text">Provide a reason for adding this stock</div>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="ti ti-info-circle me-2"></i>Stock Addition Preview
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Current Stock:</strong> <span id="currentStock">{{ $inventory->quantity }}</span> units
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Adding:</strong> <span id="addingStock">0</span> units
                                        </div>
                                        <div class="col-md-4">
                                            <strong>New Total:</strong> <span id="newTotal">{{ $inventory->quantity }}</span> units
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.agent-inventory.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-arrow-left me-1"></i>Back to Inventory
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i>Add Stock
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live preview of stock addition
document.getElementById('quantity').addEventListener('input', function() {
    const currentStock = {{ $inventory->quantity }};
    const addingStock = parseInt(this.value) || 0;
    const newTotal = currentStock + addingStock;

    document.getElementById('addingStock').textContent = addingStock;
    document.getElementById('newTotal').textContent = newTotal;

    // Update preview alert color based on new total vs threshold
    const alertElement = document.querySelector('.alert-info');
    const threshold = {{ $inventory->low_stock_threshold }};

    if (newTotal <= threshold) {
        alertElement.className = 'alert alert-warning';
        alertElement.querySelector('.alert-heading').innerHTML = '<i class="ti ti-alert-triangle me-2"></i>Stock Addition Preview - Warning: Will be at low stock level';
    } else {
        alertElement.className = 'alert alert-info';
        alertElement.querySelector('.alert-heading').innerHTML = '<i class="ti ti-info-circle me-2"></i>Stock Addition Preview';
    }
});
</script>
@endsection

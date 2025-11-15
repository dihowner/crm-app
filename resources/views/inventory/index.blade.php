@extends('layouts.dashboard')

@section('page-title', 'Product Inventory')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Filter Inventory</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('inventory.index') }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="agent_id" class="form-label">Filter by Agent Name</label>
                                <select class="form-select" id="agent_id" name="agent_id">
                                    <option value="">All Agents</option>
                                    @foreach($allAgents as $agent)
                                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Filter by Product</label>
                                <select class="form-select" id="product_id" name="product_id">
                                    <option value="">All Products</option>
                                    @foreach($allProducts as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="low_stock_only" name="low_stock_only"
                                           value="1" {{ request('low_stock_only') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_stock_only">
                                        Low Stock Only
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-light">
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

@if($agents->count() > 0)
    @foreach($agents as $agent)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">{{ $agent->name }}</h4>
                        <a href="{{ route('inventory.agent-orders', $agent) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-eye me-1"></i>View Orders
                        </a>
                    </div>
                    <div class="card-body">
                        @if($agent->inventories->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Current Stock</th>
                                            <th>Minimum Stock</th>
                                            <th>Status</th>
                                            <th>Add Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($agent->inventories as $inventory)
                                            @php
                                                $stockStatus = $inventory->quantity <= $inventory->low_stock_threshold ? 'danger' :
                                                             ($inventory->quantity <= ($inventory->low_stock_threshold * 2) ? 'warning' : 'success');
                                                $stockText = $inventory->quantity <= $inventory->low_stock_threshold ? 'Low Stock' :
                                                           ($inventory->quantity <= ($inventory->low_stock_threshold * 2) ? 'Medium Stock' : 'Good Stock');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ $inventory->product->name }}</h6>
                                                        <small class="text-muted">{{ $inventory->product->description ?? 'No description' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold fs-16">{{ number_format($inventory->quantity) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ number_format($inventory->low_stock_threshold) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $stockStatus }}">{{ $stockText }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <input type="number" class="form-control form-control-sm"
                                                               id="stock_quantity_{{ $inventory->id }}"
                                                               placeholder="Qty" min="1" style="width: 80px;">
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                                onclick="addStock({{ $inventory->id }}, {{ $agent->id }}, {{ $inventory->product->id }})">
                                                            <i class="ti ti-plus me-1"></i>Add
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="ti ti-package text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="text-muted">No Inventory Found</h5>
                                <p class="text-muted">This agent doesn't have any products in inventory yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="ti ti-package text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted">No Agents Found</h4>
                    <p class="text-muted">No agents found matching your criteria.</p>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
function addStock(inventoryId, agentId, productId) {
    const quantityInput = document.getElementById(`stock_quantity_${inventoryId}`);
    const quantity = parseInt(quantityInput.value);

    if (!quantity || quantity < 1) {
        showAlert('warning', 'Please enter a valid quantity');
        return;
    }

    const button = quantityInput.nextElementSibling;
    const originalText = button.innerHTML;

    // Show loading state
    button.innerHTML = '<i class="ti ti-loader-2 me-1"></i>Adding...';
    button.disabled = true;

    const requestData = {
        agent_id: agentId,
        product_id: productId,
        quantity: quantity
    };

    fetch('{{ route("inventory.add-stock") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Reload page to show updated stock
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.error || 'Failed to add stock');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while adding stock');
    })
    .finally(() => {
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
        quantityInput.value = '';
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insert at top of content
    const content = document.querySelector('.content-page');
    content.insertBefore(alertDiv, content.firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endsection

@extends('layouts.admin')

@section('page-title', 'Agent Inventory')

@section('content')

<!-- Agent Inventory List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.agent-inventory.index') }}" class="row g-3">
                            <!-- Search -->
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Search agents or products..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Agent Filter -->
                            <div class="col-md-3">
                                <select class="form-select" name="agent_id">
                                    <option value="">All Agents</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product Filter -->
                            <div class="col-md-3">
                                <select class="form-select" name="product_id">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Low Stock Filter -->
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="low_stock" value="true"
                                           id="lowStock" {{ request('low_stock') == 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="lowStock">
                                        Low Stock Only
                                    </label>
                                </div>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i>Search
                                </button>
                                <a href="{{ route('admin.agent-inventory.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Agent Inventory Table -->
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th>Agent</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inventory)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" value="{{ $inventory->id }}">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-primary-subtle me-2">
                                                <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                    {{ strtoupper(substr($inventory->agent->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $inventory->agent->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $inventory->product->name }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info font-size-14">{{ $inventory->quantity }}</span>
                                    </td>
                                    <td>{{ $inventory->updated_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Edit Button -->
                                            <a href="{{ route('admin.agent-inventory.edit', $inventory) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Edit Inventory">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                        <!-- Add Stock Button -->
                                        <a href="{{ route('admin.agent-inventory.show-add-stock', $inventory) }}"
                                           class="btn btn-sm btn-outline-success"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Add Stock">
                                            <i class="ti ti-plus"></i>
                                        </a>

                                            <!-- Delete Button -->
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Delete Inventory"
                                                    onclick="deleteInventory({{ $inventory->id }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="ti ti-package text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                        <h5 class="text-muted">No Agent Inventory Found</h5>
                                        <p class="text-muted">No inventory records found matching your criteria.</p>
                                        <a href="{{ route('admin.agent-inventory.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i>Add First Inventory
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($inventories->hasPages())
                    <div class="pagination-container">
                        {{ $inventories->links('pagination.bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Forms for actions -->
<form id="deleteInventoryForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>

function deleteInventory(inventoryId) {
    bootbox.confirm({
        message: "Are you sure you want to delete this inventory record? This action cannot be undone.",
        buttons: {
            confirm: {
                label: 'Delete',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                const form = document.getElementById('deleteInventoryForm');
                form.action = `/admin/agent-inventory/${inventoryId}`;
                form.submit();
            }
        }
    });
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

@endsection

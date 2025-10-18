@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Manage Products</h4>
                            <p class="text-muted mb-0">View and manage all products in the system</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add New Product
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search products..."
                                       value="{{ request('search') }}" onkeyup="searchProducts(this.value)">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" onchange="filterByCategory(this.value)">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" onchange="filterByStatus(this.value)">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="performSearch()">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="ti ti-x me-1"></i>Clear
                            </button>
                        </div>
                        <div class="col-md-2">
                            <!-- Empty column for spacing -->
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                            <label class="form-check-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" value="{{ $product->id }}">
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image_url)
                                                    <div class="avatar-sm me-3">
                                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                             class="img-fluid rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="avatar-sm rounded-circle bg-primary-subtle me-3">
                                                        <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                            {{ strtoupper(substr($product->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                                    @if($product->sku)
                                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                                    @endif
                                                    @if($product->description)
                                                        <small class="text-muted d-block">{{ Str::limit($product->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $product->category }}</span>
                                        </td>
                                        <td>
                                            <strong>₦{{ number_format($product->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge {{ $product->isLowStock() ? 'bg-warning' : 'bg-success' }} me-2">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                                @if($product->isLowStock())
                                                    <i class="ti ti-alert-triangle text-warning" title="Low Stock"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->status_badge_class }}">
                                                {{ $product->status_text }}
                                            </span>
                                        </td>
                                        <td>{{ $product->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="View Product">
                                                    <i class="ti ti-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Edit Product">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <!-- Toggle Status Button -->
                                                <button type="button"
                                                        class="btn btn-sm {{ $product->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="{{ $product->is_active ? 'Deactivate Product' : 'Activate Product' }}"
                                                        onclick="toggleStatus({{ $product->id }})">
                                                    <i class="ti {{ $product->is_active ? 'ti-eye-off' : 'ti-eye' }}"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete Product"
                                                        onclick="deleteProduct({{ $product->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="ti ti-package text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="text-muted">No Products Found</h5>
                                            <p class="text-muted">No products found matching your criteria.</p>
                                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-1"></i>Add First Product
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="pagination-container">
                            {{ $products->appends(request()->query())->links('pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forms for actions -->
<form id="toggleStatusForm" method="POST" style="display: none;">
    @csrf
</form>

<form id="deleteProductForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Toggle Status
function toggleStatus(productId) {
    bootbox.confirm({
        message: "Are you sure you want to toggle this product's status?",
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-primary'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                const form = document.getElementById('toggleStatusForm');
                form.action = `/admin/products/${productId}/toggle-status`;
                form.submit();
            }
        }
    });
}

// Delete Product
function deleteProduct(productId) {
    bootbox.confirm({
        message: "Are you sure you want to delete this product? This action cannot be undone and will affect all related orders and inventory.",
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
                const form = document.getElementById('deleteProductForm');
                form.action = `/admin/products/${productId}`;
                form.submit();
            }
        }
    });
}

// Search functionality
function performSearch() {
    const searchInput = document.querySelector('input[placeholder="Search products..."]');
    const query = searchInput.value.trim();

    const url = new URL(window.location);
    if (query) {
        url.searchParams.set('search', query);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}

function searchProducts(query) {
    // Auto-search on Enter key press
    if (event && event.key === 'Enter') {
        performSearch();
    }
}

// Filter by category
function filterByCategory(category) {
    const url = new URL(window.location);
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    window.location.href = url.toString();
}

// Filter by status
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}

// Clear filters
function clearFilters() {
    window.location.href = '{{ route("admin.products.index") }}';
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection

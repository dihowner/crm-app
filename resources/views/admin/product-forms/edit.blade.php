@extends('layouts.admin')

@section('title', 'Edit Product Form')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Product Form: {{ $productForm->form_name }}</h4>
                    <p class="text-muted mb-0">Update the product form configuration</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.product-forms.update', $productForm) }}" id="productFormForm">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Form Name and Product -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="form_name" class="form-label">Form Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('form_name') is-invalid @enderror"
                                       id="form_name" name="form_name" value="{{ old('form_name', $productForm->form_name) }}"
                                       placeholder="e.g., Order Form, Website A form" required>
                                <div class="form-text">You can put the site domain name here.</div>
                                @error('form_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $productForm->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Redirect URL and Button Text -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="redirect_url" class="form-label">Redirect URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control @error('redirect_url') is-invalid @enderror"
                                       id="redirect_url" name="redirect_url" value="{{ old('redirect_url', $productForm->redirect_url) }}"
                                       placeholder="https://domain.com" required>
                                <div class="form-text">Currently: <span id="currentUrl">{{ old('redirect_url', $productForm->redirect_url) }}</span></div>
                                @error('redirect_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="button_text" class="form-label">Button Text <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('button_text') is-invalid @enderror"
                                       id="button_text" name="button_text" value="{{ old('button_text', $productForm->button_text) }}"
                                       placeholder="Place Order" required>
                                @error('button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Packages Section -->
                        <div class="mb-4">
                            <label class="form-label">Packages <span class="text-danger">*</span></label>
                            <div id="packagesContainer">
                                @if(old('packages'))
                                    @foreach(old('packages') as $index => $package)
                                        <div class="row mb-3 package-row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="packages[{{ $index }}][name]"
                                                       value="{{ $package['name'] ?? '' }}" placeholder="Package Name" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="packages[{{ $index }}][price]"
                                                       value="{{ $package['price'] ?? '' }}" placeholder="Price" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="packages[{{ $index }}][quantity]"
                                                       value="{{ $package['quantity'] ?? 1 }}" placeholder="Quantity" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-package" onclick="removePackage(this)">
                                                    <i class="ti ti-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($productForm->packages as $index => $package)
                                        <div class="row mb-3 package-row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="packages[{{ $index }}][name]"
                                                       value="{{ $package['name'] }}" placeholder="Package Name" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="packages[{{ $index }}][price]"
                                                       value="{{ $package['price'] }}" placeholder="Price" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" name="packages[{{ $index }}][quantity]"
                                                       value="{{ $package['quantity'] }}" placeholder="Quantity" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-package" onclick="removePackage(this)">
                                                    <i class="ti ti-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPackage()">
                                <i class="ti ti-plus"></i> Add Package
                            </button>
                            @error('packages')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview Section -->
                        <div class="mb-4">
                            <label class="form-label">Preview Embeddable Form</label>
                            <div class="border rounded p-3" style="min-height: 200px; background-color: #f8f9fa;">
                                <div class="text-center text-muted">
                                    <i class="ti ti-eye" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Form preview will appear here after you save the changes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Generated Form Section -->
                        <div class="mb-4">
                            <label class="form-label">Generated Form</label>
                            <textarea class="form-control" rows="10" readonly>{{ $productForm->generated_form ?? 'Generated form HTML will appear here after saving...' }}</textarea>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.product-forms.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Back to Forms
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Update Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let packageIndex = {{ count(old('packages', $productForm->packages ?? [])) }};

// Add package row
function addPackage() {
    const container = document.getElementById('packagesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 package-row';
    newRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="packages[${packageIndex}][name]" placeholder="Package Name" required>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="packages[${packageIndex}][price]" placeholder="Price" min="0" step="0.01" required>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control" name="packages[${packageIndex}][quantity]" placeholder="Quantity" value="1" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm remove-package" onclick="removePackage(this)">
                <i class="ti ti-trash"></i> Remove
            </button>
        </div>
    `;
    container.appendChild(newRow);
    packageIndex++;
}

// Remove package row
function removePackage(button) {
    const row = button.closest('.package-row');
    if (document.querySelectorAll('.package-row').length > 1) {
        row.remove();
    } else {
        bootbox.alert({
            message: 'At least one package is required.',
            buttons: { ok: { label: 'OK', className: 'btn-primary' } }
        });
    }
}

// Update current URL display
document.getElementById('redirect_url').addEventListener('input', function() {
    document.getElementById('currentUrl').textContent = this.value || 'https://domain.com';
});

// Initialize with at least one package
document.addEventListener('DOMContentLoaded', function() {
    // Ensure we have at least one package row
    if (document.querySelectorAll('.package-row').length === 0) {
        addPackage();
    }
});
</script>
@endsection

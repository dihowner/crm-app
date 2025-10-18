@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add New Product</h4>
                    <p class="text-muted mb-0">Create a new product for the CRM system</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Row 1: Product Name, Category, SKU -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="Enter product name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                       id="category" name="category" value="{{ old('category') }}"
                                       placeholder="Enter product category" required>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                       id="sku" name="sku" value="{{ old('sku') }}"
                                       placeholder="Enter product SKU">
                                <div class="form-text">Stock Keeping Unit (optional)</div>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Price, Weight, Dimensions -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price') }}"
                                           placeholder="0.00" min="0" step="0.01" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="weight" class="form-label">Weight</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror"
                                           id="weight" name="weight" value="{{ old('weight') }}"
                                           placeholder="0.00" min="0" step="0.01">
                                    <span class="input-group-text">kg</span>
                                </div>
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="dimensions" class="form-label">Dimensions</label>
                                <input type="text" class="form-control @error('dimensions') is-invalid @enderror"
                                       id="dimensions" name="dimensions" value="{{ old('dimensions') }}"
                                       placeholder="e.g., 10 x 5 x 3 cm">
                                <div class="form-text">Length x Width x Height</div>
                                @error('dimensions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 3: Stock Quantity, Low Stock Threshold, Status -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                                       min="0" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="low_stock_threshold" class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                       id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}"
                                       min="0" required>
                                <div class="form-text">Alert when stock falls below this number</div>
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Product
                                    </label>
                                </div>
                                <div class="form-text">Designates whether this product should be treated as active and available for orders.</div>
                            </div>
                        </div>

                        <!-- Row 4: Product Image and Description -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                       id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a product image (JPEG, PNG, JPG, GIF - Max 2MB)</div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4"
                                          placeholder="Enter product description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Back to Products
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-1">Edit Stock Log</h4>
                    <p class="text-muted mb-0">Update stock movement information</p>
                </div>
                <div>
                    <a href="{{ route('admin.stock-logs.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Stock Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stock-logs.update', $stockLog) }}">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Agent, Product, Created By -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="agent_id" class="form-label">Agent <span class="text-danger">*</span></label>
                                <select class="form-select @error('agent_id') is-invalid @enderror"
                                        id="agent_id" name="agent_id" required>
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id', $stockLog->agent_id) == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }} ({{ $agent->company_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_id') is-invalid @enderror"
                                        id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $stockLog->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (₦{{ number_format($product->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="created_by" class="form-label">Created By <span class="text-danger">*</span></label>
                                <select class="form-select @error('created_by') is-invalid @enderror"
                                        id="created_by" name="created_by" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('created_by', $stockLog->created_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('created_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Quantity Changed, Action -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="quantity_changed" class="form-label">Quantity Changed <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity_changed') is-invalid @enderror"
                                       id="quantity_changed" name="quantity_changed"
                                       value="{{ old('quantity_changed', $stockLog->quantity_changed) }}"
                                       placeholder="Enter quantity (positive for addition, negative for removal)" required>
                                <div class="form-text">
                                    <strong>Positive values:</strong> Add stock<br>
                                    <strong>Negative values:</strong> Remove stock
                                </div>
                                @error('quantity_changed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="action" class="form-label">Action <span class="text-danger">*</span></label>
                                <select class="form-select @error('action') is-invalid @enderror"
                                        id="action" name="action" required>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ old('action', $stockLog->action) == $action ? 'selected' : '' }}>
                                            {{ $action }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 3: Comment -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror"
                                          id="comment" name="comment" rows="3"
                                          placeholder="Add any additional notes or comments about this stock movement...">{{ old('comment', $stockLog->comment) }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-2"></i>Update Stock Log
                                    </button>
                                    <a href="{{ route('admin.stock-logs.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-x me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

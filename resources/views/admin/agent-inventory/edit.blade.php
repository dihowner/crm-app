@extends('layouts.admin')

@section('title', 'Edit Agent Inventory')

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
                        <li class="breadcrumb-item active">Edit Inventory</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Agent Inventory</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Inventory Record</h4>
                    <p class="text-muted mb-0">Update agent inventory details</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.agent-inventory.update', $agentInventory) }}">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Agent and Product -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="agent_id" class="form-label">Agent <span class="text-danger">*</span></label>
                                <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id" name="agent_id" required>
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id', $agentInventory->agent_id) == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $agentInventory->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Stock Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Current Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity" name="quantity" min="0" value="{{ old('quantity', $agentInventory->quantity) }}" required>
                                <div class="form-text">Current quantity in stock</div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="low_stock_threshold" class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                       id="low_stock_threshold" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $agentInventory->low_stock_threshold) }}" required>
                                <div class="form-text">Alert when stock falls below this level</div>
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 3: Pricing Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="cost_price" class="form-label">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('cost_price') is-invalid @enderror"
                                           id="cost_price" name="cost_price" step="0.01" min="0" value="{{ old('cost_price', $agentInventory->cost_price) }}">
                                </div>
                                <div class="form-text">Cost price per unit</div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="selling_price" class="form-label">Selling Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                           id="selling_price" name="selling_price" step="0.01" min="0" value="{{ old('selling_price', $agentInventory->selling_price) }}">
                                </div>
                                <div class="form-text">Selling price per unit</div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Information Display -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="ti ti-info-circle me-2"></i>Current Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Agent:</strong> {{ $agentInventory->agent->name }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Product:</strong> {{ $agentInventory->product->name }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Current Stock:</strong> {{ $agentInventory->quantity }} units
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Last Updated:</strong> {{ $agentInventory->updated_at->format('M d, Y H:i') }}
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
                                        <i class="ti ti-device-floppy me-1"></i>Update Inventory
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
@endsection

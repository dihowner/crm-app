@extends('layouts.admin')

@section('title', 'Edit Delivery Agent')

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
                        <li class="breadcrumb-item"><a href="{{ route('admin.delivery-agents.index') }}">Delivery Agents</a></li>
                        <li class="breadcrumb-item active">Edit Agent</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Delivery Agent</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Delivery Agent</h4>
                    <p class="text-muted mb-0">Update delivery agent information</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.delivery-agents.update', $deliveryAgent) }}">
                        @csrf
                        @method('PUT')

                        <!-- Row 1: Name and Phone -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Agent Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $deliveryAgent->name) }}" required>
                                <div class="form-text">Full name of the delivery agent</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $deliveryAgent->phone) }}">
                                <div class="form-text">Contact phone number</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 2: Email and Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $deliveryAgent->email) }}">
                                <div class="form-text">Email address for notifications</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="agent_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="agent_status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $deliveryAgent->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $deliveryAgent->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="form-text">Designates whether this agent should be treated as active</div>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Row 3: Address -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3"
                                          placeholder="Enter the agent's address...">{{ old('address', $deliveryAgent->address) }}</textarea>
                                <div class="form-text">Physical address of the delivery agent</div>
                                @error('address')
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
                                            <strong>Created:</strong> {{ $deliveryAgent->created_at->format('M d, Y H:i') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Last Updated:</strong> {{ $deliveryAgent->updated_at->format('M d, Y H:i') }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong>
                                            @if($deliveryAgent->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Inventory Items:</strong> {{ $deliveryAgent->inventories()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.delivery-agents.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-arrow-left me-1"></i>Back to Agents
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i>Update Agent
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

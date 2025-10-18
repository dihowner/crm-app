@extends('layouts.admin')

@section('title', 'Add Delivery Agent')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add New Delivery Agent</h4>
                    <p class="text-muted mb-0">Create a new delivery agent profile</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.delivery-agents.store') }}">
                        @csrf

                        <!-- Row 1: Name and Phone -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Agent Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                <div class="form-text">Full name of the delivery agent</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}">
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
                                       id="email" name="email" value="{{ old('email') }}">
                                <div class="form-text">Email address for notifications</div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="agent_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="agent_status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                          placeholder="Enter the agent's address...">{{ old('address') }}</textarea>
                                <div class="form-text">Physical address of the delivery agent</div>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        <i class="ti ti-plus me-1"></i>Create Agent
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

@extends('layouts.dashboard')

@section('page-title', 'Edit Order')

@section('content')

<!-- Breadcrumb -->
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">
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
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Order #{{ $order->order_number }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Page Title -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Edit Order</h4>
                <p class="text-muted mb-0">Order #{{ $order->order_number }}</p>
            </div>
            <div>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to Order
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-edit me-2"></i>Order Details
                </h5>
            </div>
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->phone }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $order->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - ₦{{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $order->quantity) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Unit Price (₦)</label>
                                <input type="number" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price" value="{{ old('unit_price', $order->unit_price) }}" step="0.01" min="0" required>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total Price (₦)</label>
                                <input type="text" class="form-control" id="total_price" readonly value="{{ number_format($order->total_price, 2) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="order_status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="order_status" name="status" required>
                                    <option value="new" {{ $order->status == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="scheduled" {{ $order->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="not_picking_calls" {{ $order->status == 'not_picking_calls' ? 'selected' : '' }}>Not Picking Calls</option>
                                    <option value="number_off" {{ $order->status == 'number_off' ? 'selected' : '' }}>Number Off</option>
                                    <option value="call_back" {{ $order->status == 'call_back' ? 'selected' : '' }}>Call Back</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="failed" {{ $order->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    @if($order->status == 'paid')
                                        <option value="paid" selected>Paid</option>
                                    @endif
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Callback Reminder Field (Hidden by default, shown when Call Back is selected) -->
                        <div class="col-md-3" id="callback_reminder_field" style="display: none; transition: all 0.3s ease;">
                            <div class="mb-3">
                                <label for="callback_reminder" class="form-label">Callback Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('callback_reminder') is-invalid @enderror" id="callback_reminder" name="callback_reminder" 
                                       value="{{ old('callback_reminder', $order->callback_reminder ? $order->callback_reminder->format('Y-m-d\TH:i') : '') }}"
                                       min="{{ now()->format('Y-m-d\TH:i') }}">
                                @error('callback_reminder')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3" id="assigned_to_col">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $order->assigned_to == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3" id="agent_col">
                            <div class="mb-3">
                                <label for="agent_id" class="form-label">Delivery Agent</label>
                                <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id" name="agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ $order->agent_id == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3" id="source_col">
                            <div class="mb-3">
                                <label for="source" class="form-label">Source</label>
                                <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                                    <option value="">Select Source</option>
                                    <option value="Website purchase" {{ $order->source == 'Website purchase' ? 'selected' : '' }}>Website purchase</option>
                                    <option value="R or R" {{ $order->source == 'R or R' ? 'selected' : '' }}>R or R</option>
                                    <option value="Messaging" {{ $order->source == 'Messaging' ? 'selected' : '' }}>Messaging</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-2"></i>Update Order
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                            <i class="ti ti-x me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-info-circle me-2"></i>Order Information
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Order Number:</strong><br>
                    <span class="text-muted">{{ $order->order_number }}</span>
                </div>
                <div class="mb-3">
                    <strong>Created:</strong><br>
                    <span class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong><br>
                    <span class="text-muted">{{ $order->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide callback reminder field when status changes
    const statusSelect = document.getElementById('order_status');
    const callbackReminderField = document.getElementById('callback_reminder_field');
    const callbackReminderInput = document.getElementById('callback_reminder');

    function toggleCallbackField() {
        const sourceCol = document.getElementById('source_col');
        
        if (statusSelect && statusSelect.value === 'call_back') {
            if (callbackReminderField) {
                callbackReminderField.style.display = 'block';
            }
            // Hide Source field when callback is active
            if (sourceCol) {
                sourceCol.style.display = 'none';
            }
            if (callbackReminderInput) {
                callbackReminderInput.required = true;
            }
        } else {
            if (callbackReminderField) {
                callbackReminderField.style.display = 'none';
            }
            // Show Source field when callback is not active
            if (sourceCol) {
                sourceCol.style.display = 'block';
            }
            if (callbackReminderInput) {
                callbackReminderInput.required = false;
            }
        }
    }

    // Check initial state
    if (statusSelect) {
        toggleCallbackField();
        // Listen for status changes
        statusSelect.addEventListener('change', toggleCallbackField);
    }

    // Auto-calculate total price
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const totalPriceInput = document.getElementById('total_price');

    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;
        totalPriceInput.value = total.toFixed(2);
    }

    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('input', calculateTotal);
});
</script>
@endpush

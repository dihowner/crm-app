@extends('layouts.dashboard')

@section('page-title', 'Payment Records')

@section('content')
<div class="row">
    <!-- Filter Payments -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Filter Payments</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('payments.index') }}">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i>Submit
                                </button>
                                <a href="{{ route('payments.index') }}" class="btn btn-light">
                                    <i class="ti ti-x me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Orders -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Filter Orders</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('payments.index') }}">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select class="form-select" id="payment_status" name="payment_status">
                                    @foreach($paymentStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ request('payment_status') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="csr_id" class="form-label">CSR</label>
                                <select class="form-select" id="csr_id" name="csr_id">
                                    <option value="">All CSRs</option>
                                    @foreach($csrs as $csr)
                                        <option value="{{ $csr->id }}" {{ request('csr_id') == $csr->id ? 'selected' : '' }}>
                                            {{ $csr->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                        <div class="col-6"></div>
                        @endif
                        <div class="col-8">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Search by name, phone, product..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('payments.index') }}" class="btn btn-light">
                                    <i class="ti ti-x me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Payment Records</h4>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Agent</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $order->customer->name }}</h6>
                                                <small class="text-muted">{{ $order->customer->phone }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $order->product->name }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'new' => 'bg-primary',
                                                    'scheduled' => 'bg-warning',
                                                    'not picking calls' => 'bg-info',
                                                    'number off' => 'bg-secondary',
                                                    'call back' => 'bg-dark',
                                                    'delivered' => 'bg-success'
                                                ];
                                                $color = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $color }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td>{{ $order->assignedUser->name ?? 'Unassigned' }}</td>
                                        <td>{{ $order->agent->name ?? 'No Agent' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($order->paymentRecords->count() > 0)
                                                <div class="payment-info">
                                                    @foreach($order->paymentRecords as $payment)
                                                        <div class="mb-1">
                                                            <span class="text-success fw-bold">₦{{ number_format($payment->amount, 2) }}</span>
                                                            <small class="text-muted">on {{ $payment->payment_date->format('M d, Y') }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="openPaymentModal({{ $order->id }}, '{{ $order->customer->name }}', '{{ $order->product->name }}')">
                                                    Add Payment Record
                                                </button>
                                            @endif
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary btn-sm ms-1" target="_blank">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        {{ $orders->links('pagination.bootstrap-4') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-credit-card text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No Payment Records Found</h5>
                        <p class="text-muted">No orders with agents found matching your criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
function openPaymentModal(orderId, customerName, productName) {
    // Create form HTML
    const formHtml = `
        <form id="paymentForm">
            <input type="hidden" id="order_id" name="order_id" value="${orderId}">

            <div class="mb-3">
                <label class="form-label">Order Details</label>
                <div class="alert alert-light">
                    <strong>${customerName}</strong><br>
                    <small class="text-muted">${productName}</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">₦</span>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Received Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="payment_date" name="payment_date" value="${new Date().toISOString().split('T')[0]}" required>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Note</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about the payment..."></textarea>
            </div>
        </form>
    `;

    // Show Bootbox modal
    bootbox.dialog({
        title: 'Add Payment Record',
        message: formHtml,
        size: 'medium',
        buttons: {
            cancel: {
                label: 'Close',
                className: 'btn-secondary'
            },
            confirm: {
                label: 'Record Payment',
                className: 'btn-primary',
                callback: function() {
                    // Validate form first
                    const form = document.getElementById('paymentForm');
                    if (!form) {
                        bootbox.alert('Form not found!');
                        return false;
                    }

                    const amount = form.querySelector('#amount').value;
                    const paymentDate = form.querySelector('#payment_date').value;

                    if (!amount || !paymentDate) {
                        bootbox.alert('Please fill in all required fields.');
                        return false;
                    }

                    // Submit form and close modal
                    submitPaymentForm(orderId);
                    return true; // Allow modal to close
                }
            }
        }
    });
}

function submitPaymentForm(orderId) {
    const form = document.getElementById('paymentForm');
    if (!form) return false;

    const formData = new FormData(form);

    // Show loading state in modal
    bootbox.hideAll();
    bootbox.dialog({
        message: '<div class="text-center"><i class="ti ti-loader-2 me-2"></i>Recording payment...</div>',
        closeButton: false,
        size: 'small'
    });

    fetch('{{ route("payments.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        bootbox.hideAll();

        if (data.success) {
            // Show success message
            bootbox.alert({
                message: data.message || 'Payment recorded successfully!',
                callback: function() {
                    window.location.reload();
                }
            });
        } else {
            bootbox.alert({
                message: data.error || 'Failed to record payment',
                buttons: {
                    ok: {
                        label: 'OK',
                        className: 'btn-primary'
                    }
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        bootbox.hideAll();
        bootbox.alert({
            message: 'An error occurred while recording payment. Please try again.',
            buttons: {
                ok: {
                    label: 'OK',
                    className: 'btn-primary'
                }
            }
        });
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

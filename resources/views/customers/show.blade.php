@extends('layouts.dashboard')

@section('page-title', 'Customer Details')

@section('content')

<!-- Breadcrumb -->
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active">{{ $customer->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Customer Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="avatar-lg me-3">
                    <div class="avatar-title bg-primary rounded-circle fs-24">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h4 class="mb-1">{{ $customer->name }}</h4>
                    <p class="text-muted mb-0">{{ $customer->phone }} • {{ $deliveredOrdersCount }} delivered orders</p>
                </div>
            </div>
            <div>
                <a href="tel:{{ $customer->phone }}" class="btn btn-success me-2">
                    <i class="ti ti-phone me-1"></i>Call
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" class="btn btn-success" target="_blank">
                    <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-user me-2"></i>Customer Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Full Name:</strong></p>
                        <p class="text-muted">{{ $customer->name }}</p>

                        <p class="mt-3 mb-1"><strong>Phone Number:</strong></p>
                        <p class="text-muted">
                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                {{ $customer->phone }}
                            </a>
                        </p>

                        <p class="mt-3 mb-1"><strong>Email:</strong></p>
                        <p class="text-muted">{{ $customer->email ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Location:</strong></p>
                        <p class="text-muted">{{ $customer->state }}</p>

                        <p class="mt-3 mb-1"><strong>Address:</strong></p>
                        <p class="text-muted">{{ $customer->address ?? 'Not provided' }}</p>

                        <p class="mt-3 mb-1"><strong>Total Spent:</strong></p>
                        <p class="text-success fs-16 fw-bold">₦{{ number_format($customer->total_spent, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-shopping-cart me-2"></i>Recent Orders
                </h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->product->name }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'new' => 'bg-primary',
                                                    'scheduled' => 'bg-warning',
                                                    'delivered' => 'bg-success',
                                                    'not picking calls' => 'bg-danger',
                                                    'number off' => 'bg-secondary',
                                                    'call back' => 'bg-info'
                                                ];
                                                $statusColor = $statusColors[strtolower($order->status)] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $statusColor }} text-white">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td>₦{{ number_format($order->total_price, 2) }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-shopping-cart-off fs-48 text-muted mb-3"></i>
                        <p class="text-muted">No orders found for this customer</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Order Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-chart-bar me-2"></i>Order Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-1 text-primary">{{ $customer->orders->count() }}</h4>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-1 text-success">{{ $deliveredOrdersCount }}</h4>
                        <p class="text-muted mb-0">Delivered</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-1 text-info">{{ $customer->orders->where('status', 'scheduled')->count() }}</h4>
                            <p class="text-muted mb-0">Scheduled</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-1 text-warning">{{ $customer->orders->where('status', 'new')->count() }}</h4>
                        <p class="text-muted mb-0">Pending</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="tel:{{ $customer->phone }}" class="btn btn-outline-success">
                        <i class="ti ti-phone me-1"></i>Call Customer
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" class="btn btn-outline-success" target="_blank">
                        <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                    </a>
                    <button class="btn btn-outline-primary" onclick="copyCustomerInfo(this)">
                        <i class="ti ti-clipboard me-1"></i>Copy Info
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copyCustomerInfo(button) {
    const customerInfo = `Customer: {{ $customer->name }}
Phone: {{ $customer->phone }}
Email: {{ $customer->email ?? 'Not provided' }}
Location: {{ $customer->state }}
Address: {{ $customer->address ?? 'Not provided' }}
Total Spent: ₦{{ number_format($customer->total_spent, 2) }}
Total Orders: {{ $customer->orders->count() }}
Delivered Orders: {{ $deliveredOrdersCount }}`;

    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(customerInfo).then(() => {
            showCopySuccess(button);
        }).catch(err => {
            console.error('Clipboard API failed: ', err);
            fallbackCopy(customerInfo, button);
        });
    } else {
        // Fallback for older browsers or non-secure contexts
        fallbackCopy(customerInfo, button);
    }
}

function fallbackCopy(text, button) {
    // Create a temporary textarea element
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(button);
        } else {
            showCopyError();
        }
    } catch (err) {
        console.error('Fallback copy failed: ', err);
        showCopyError();
    } finally {
        document.body.removeChild(textArea);
    }
}

function showCopySuccess(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="ti ti-check me-1"></i>Copied!';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');

    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

function showCopyError() {
    bootbox.alert({
        message: 'Failed to copy customer info. Please try selecting and copying the text manually.',
        buttons: {
            ok: {
                label: 'OK',
                className: 'btn-primary'
            }
        }
    });
}
</script>
@endpush

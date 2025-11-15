@extends('layouts.dashboard')

@section('page-title', 'Customer Management')

@section('content')


<!-- Filter Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name, phone, product...">
                    </div>

                    <div class="col-md-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="location" class="form-label">State</label>
                        <select class="form-select" id="location" name="location">
                            <option value="">All States</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bulk SMS Section -->
@if(auth()->user()->isAdmin() || auth()->user()->isCSR())
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-message-circle me-2"></i>Bulk SMS Campaign
                </h5>
            </div>
            <div class="card-body">
                <form id="bulkSmsForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="bulk_message" class="form-label">Message Template</label>
                            <textarea class="form-control" id="bulk_message" name="message" rows="3"
                                      placeholder="Enter your message. Use [name] to personalize for each customer. Example: Dear [name], thank you for your recent purchase!"></textarea>
                            <div class="form-text">Use [name] to personalize messages. Available customers: <span id="customer-count">0</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filters</label>
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="bulk_product_filter">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="bulk_location_filter">
                                    <option value="">All States</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-success btn-sm w-100" id="sendBulkSms">
                                <i class="ti ti-send me-1"></i>Send SMS
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Customers Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>State</th>
                                <th>Orders</th>
                                <th>Last Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-primary rounded-circle">
                                                    {{ substr($customer->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $customer->name }}</h6>
                                                @if($customer->email)
                                                    <small class="text-muted">{{ $customer->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                            {{ $customer->phone }}
                                        </a>
                                    </td>
                                    <td>{{ $customer->state }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $customer->orders_count }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $lastOrder = $customer->orders()->orderBy('created_at', 'desc')->first();
                                        @endphp
                                        @if($lastOrder)
                                            {{ $lastOrder->created_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="ti ti-eye me-1"></i>Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-users-off fs-48 mb-3 d-block"></i>
                                            <h5>No customers found</h5>
                                            <p>No customers with delivered orders found matching your criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                    <div class="pagination-container">
                        {{ $customers->appends(request()->query())->links('pagination.bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update customer count when filters change
    function updateCustomerCount() {
        const productId = document.getElementById('bulk_product_filter').value;
        const location = document.getElementById('bulk_location_filter').value;

        fetch(`{{ route('customers.bulk-sms-data') }}?product_id=${productId}&location=${location}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('customer-count').textContent = data.length;
            });
    }

    // Initial count
    updateCustomerCount();

    // Update count when filters change
    document.getElementById('bulk_product_filter').addEventListener('change', updateCustomerCount);
    document.getElementById('bulk_location_filter').addEventListener('change', updateCustomerCount);

    // Send bulk SMS
    document.getElementById('sendBulkSms').addEventListener('click', function() {
        const message = document.getElementById('bulk_message').value;
        const productId = document.getElementById('bulk_product_filter').value;
        const location = document.getElementById('bulk_location_filter').value;

        if (!message.trim()) {
            bootbox.alert({
                message: 'Please enter a message',
                buttons: {
                    ok: {
                        label: 'OK',
                        className: 'btn-primary'
                    }
                }
            });
            return;
        }

        if (!message.includes('[name]')) {
            bootbox.confirm({
                message: 'Your message doesn\'t include [name] for personalization. Continue anyway?',
                buttons: {
                    confirm: {
                        label: 'Continue',
                        className: 'btn-primary'
                    },
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-secondary'
                    }
                },
                callback: function (result) {
                    if (!result) {
                        return;
                    }
                    proceedWithSMS(message, productId, location);
                }
            });
            return;
        }

        proceedWithSMS(message, productId, location);
    });

    function proceedWithSMS(message, productId, location) {
        // Get customers based on filters
        fetch(`{{ route('customers.bulk-sms-data') }}?product_id=${productId || ''}&location=${location || ''}`)
            .then(response => response.json())
            .then(customers => {
                if (customers.length === 0) {
                    bootbox.alert({
                        message: 'No customers found with the selected filters',
                        buttons: {
                            ok: {
                                label: 'OK',
                                className: 'btn-primary'
                            }
                        }
                    });
                    return;
                }

                bootbox.confirm({
                    message: `Send SMS to ${customers.length} customers?`,
                    buttons: {
                        confirm: {
                            label: 'Send SMS',
                            className: 'btn-primary'
                        },
                        cancel: {
                            label: 'Cancel',
                            className: 'btn-secondary'
                        }
                    },
                    callback: function (result) {
                        if (!result) {
                            return;
                        }
                        sendSMSToCustomers(customers, message);
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching customers:', error);
                bootbox.alert({
                    message: 'Error fetching customers. Please try again.',
                    buttons: {
                        ok: {
                            label: 'OK',
                            className: 'btn-primary'
                        }
                    }
                });
            });
    }

    function sendSMSToCustomers(customers, message) {
        const form = document.getElementById('bulkSmsForm');
        const formData = new FormData(form);
        
        // Append customer IDs as array (Laravel expects array format)
        customers.forEach(customer => {
            formData.append('customer_ids[]', customer.id);
        });

        // Show loading state
        const btn = document.getElementById('sendBulkSms');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ti ti-loader-2 me-1"></i>Sending...';
        btn.disabled = true;

        fetch('{{ route("customers.bulk-sms") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            }
            return response.json().then(err => Promise.reject(err));
        })
        .then(() => {
            bootbox.alert({
                message: 'SMS campaign sent successfully!',
                buttons: {
                    ok: {
                        label: 'OK',
                        className: 'btn-primary',
                        callback: function() {
                            location.reload();
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            const errorMessage = error.message || error.error || 'Error sending SMS. Please try again.';
            bootbox.alert({
                message: errorMessage,
                buttons: {
                    ok: {
                        label: 'OK',
                        className: 'btn-primary'
                    }
                }
            });
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
});
</script>
@endpush

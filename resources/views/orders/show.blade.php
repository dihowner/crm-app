@extends('layouts.dashboard')

@section('page-title', 'Order Details')

@section('content')

<!-- Breadcrumb -->
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Order Title -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Order #{{ $order->order_number }}</h4>
                <p class="text-muted mb-0">{{ $order->customer->name }} - {{ $order->product->name }}</p>
            </div>
            <div>
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
                <span class="badge {{ $statusColor }} fs-16 px-3 py-2">{{ ucfirst($order->status) }}</span>
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
                        <p class="mb-1"><strong>Name:</strong></p>
                        <p class="text-muted">{{ $order->customer->name }}</p>

                        <p class="mt-3 mb-1"><strong>Phone Number:</strong></p>
                        <p class="text-muted">
                            <a href="tel:{{ $order->customer->phone }}" class="text-decoration-none">
                                {{ $order->customer->phone }}
                            </a>
                        </p>

                        <p class="mt-3 mb-1"><strong>WhatsApp Number:</strong></p>
                        <p class="text-muted">{{ $order->customer->whatsapp_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Location:</strong></p>
                        <p class="text-muted">{{ $order->customer->state }}</p>

                        <p class="mt-3 mb-1"><strong>Address:</strong></p>
                        <p class="text-muted">{{ $order->customer->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-shopping-cart me-2"></i>Order Information
                    </h5>
                    <div>
                        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-success btn-sm me-2" target="_blank">
                            <i class="ti ti-file-invoice me-1"></i>Generate Invoice
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="deleteOrder({{ $order->id }})">
                            <i class="ti ti-trash me-1"></i>Delete Order
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Product:</strong></p>
                        <p class="text-muted">{{ $order->product->name }}</p>

                        <p class="mt-3 mb-1"><strong>Quantity:</strong></p>
                        <p class="text-muted">{{ $order->quantity }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Total Price:</strong></p>
                        <p class="text-success fs-16 fw-bold">₦{{ number_format($order->total_price, 2) }}</p>

                        <p class="mt-3 mb-1"><strong>Order Date:</strong></p>
                        <p class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-4">
                        <p class="mb-1"><strong>Notes:</strong></p>
                        <p class="text-muted">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Status History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-clock me-2"></i>Status History
                </h5>
            </div>
            <div class="card-body">
                @if($order->statusHistory->count() > 0)
                    <div class="timeline">
                        @foreach($order->statusHistory as $history)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">{{ ucfirst($history->status) }}</h6>
                                    <p class="timeline-text">{{ $history->notes }}</p>
                                    <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }} by {{ $history->changedBy->name }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ti ti-clock fs-48 text-muted mb-3"></i>
                        <p class="text-muted">No status changes yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Assignment -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-users me-2"></i>Assignment
                    </h5>
                    <button class="btn btn-primary btn-sm" onclick="showReassignModal()">
                        <i class="ti ti-user-plus me-1"></i>Reassign
                    </button>
                </div>
            </div>
            <div class="card-body text-center">
                @if($order->assignedUser)
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle fs-22">
                            {{ substr($order->assignedUser->name, 0, 1) }}
                        </div>
                    </div>
                    <h6 class="mb-1">{{ $order->assignedUser->name }}</h6>
                    <p class="text-muted mb-0">{{ $order->assignedUser->role->name }}</p>
                @else
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-secondary rounded-circle fs-22">
                            <i class="ti ti-user-off"></i>
                        </div>
                    </div>
                    <h6 class="mb-1">Unassigned</h6>
                    <p class="text-muted mb-0">No user assigned</p>
                @endif
            </div>
        </div>

        <!-- Update Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-checklist me-2"></i>Update Status
                </h5>
            </div>
            <div class="card-body">
                @if(auth()->user()->isAdmin() || (string)$order->assigned_to === (string)auth()->id())
                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label">New Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="new" {{ $order->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="scheduled" {{ $order->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="not picking calls" {{ $order->status === 'not picking calls' ? 'selected' : '' }}>Not Picking Calls</option>
                                <option value="number off" {{ $order->status === 'number off' ? 'selected' : '' }}>Number Off</option>
                                <option value="call back" {{ $order->status === 'call back' ? 'selected' : '' }}>Call Back</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_delivery_date" class="form-label">Scheduled Delivery Date</label>
                            <input type="date" class="form-control" id="scheduled_delivery_date" name="scheduled_delivery_date" value="{{ $order->scheduled_delivery_date ? $order->scheduled_delivery_date->format('Y-m-d') : '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="delivery_agent" class="form-label">Delivery Agent</label>
                            <select class="form-select" id="delivery_agent" name="delivery_agent">
                                <option value="">Select Agent</option>
                                <option value="fresh_delivery">Fresh Delivery</option>
                                <option value="express_delivery">Express Delivery</option>
                                <option value="standard_delivery">Standard Delivery</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tracking_number" class="form-label">Tracking Number (optional)</label>
                            <input type="text" class="form-control" id="tracking_number" name="tracking_number" placeholder="Enter tracking number">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-checklist me-1"></i>Update Order
                        </button>
                    </form>
                @else
                    <p class="text-muted">You don't have permission to update this order's status.</p>
                @endif
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
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary">
                        <i class="ti ti-edit me-1"></i>Edit Order
                    </a>
                    <button class="btn btn-outline-secondary" onclick="copyOrderInfo()">
                        <i class="ti ti-clipboard me-1"></i>Copy Order Info
                    </button>
                    <a href="tel:{{ $order->customer->phone }}" class="btn btn-outline-success">
                        <i class="ti ti-phone me-1"></i>Call Customer
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->whatsapp_number ?? $order->customer->phone) }}" class="btn btn-outline-success" target="_blank">
                        <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
function showReassignModal() {
    @php
        $users = \App\Models\User::whereHas('role', function($q) { $q->whereIn('slug', ['csr', 'admin']); })->get(['id', 'name', 'role_id']);
        $currentAssignedTo = $order->assigned_to;
    @endphp

    const users = @json($users);
    const currentAssignedTo = {{ $currentAssignedTo ?? 'null' }};

    // Build select options HTML
    let optionsHtml = '<option value="">Select User</option>';
    users.forEach(user => {
        const selected = user.id == currentAssignedTo ? 'selected' : '';
        optionsHtml += `<option value="${user.id}" ${selected}>${user.name}</option>`;
    });

    bootbox.dialog({
        title: 'Reassign Order',
        message: `
            <form id="reassignForm">
                <div class="mb-3">
                    <label for="assigned_to" class="form-label">Assign to User</label>
                    <select class="form-select" id="assigned_to" name="assigned_to" required>
                        ${optionsHtml}
                    </select>
                </div>
            </form>
        `,
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            },
            confirm: {
                label: 'Reassign Order',
                className: 'btn-primary',
                callback: function() {
                    const assignedTo = document.getElementById('assigned_to').value;

                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("orders.assign", $order) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PATCH';

                    const assignedToField = document.createElement('input');
                    assignedToField.type = 'hidden';
                    assignedToField.name = 'assigned_to';
                    assignedToField.value = assignedTo;

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    form.appendChild(assignedToField);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    });
}

function deleteOrder(orderId) {
    bootbox.confirm({
        message: 'Are you sure you want to delete this order? This action cannot be undone.',
        buttons: {
            confirm: {
                label: 'Yes, Delete',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/orders/' + orderId;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    });
}

function copyOrderInfo() {
    const orderInfo = `Order #{{ $order->order_number }}
*Full Name:* {{ $order->customer->name }}
*Phone:* {{ $order->customer->phone }}
*WhatsApp Phone:* {{ $order->customer->whatsapp_number ?? 'N/A' }}
*State:* {{ $order->customer->state }}
*Address:* {{ $order->customer->address }}
*Product:* {{ $order->product->name }}
*Quantity:* {{ $order->quantity }}
*Amount:* ₦{{ number_format($order->total_price, 2) }}`;

    navigator.clipboard.writeText(orderInfo).then(function() {
        bootbox.alert({
            message: 'Order information copied to clipboard successfully!',
            buttons: {
                ok: {
                    label: 'OK',
                    className: 'btn-primary'
                }
            }
        });
    }).catch(function() {
        bootbox.alert({
            message: 'Failed to copy order information. Please try again.',
            buttons: {
                ok: {
                    label: 'OK',
                    className: 'btn-primary'
                }
            }
        });
    });
}
</script>
@endpush

@extends('layouts.dashboard')

@section('page-title', 'SMS Record Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">SMS Record Details</h4>
                <a href="{{ route('sms-marketing.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                    <i class="ti ti-arrow-left me-1"></i>Back to SMS Marketing
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- SMS Information -->
                    <div class="col-lg-8">
                        <div class="mb-4">
                            <h5 class="mb-3">SMS Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Campaign Name</label>
                                        <p class="mb-0">{{ $smsRecord->campaign_name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">SMS Type</label>
                                        <p class="mb-0">
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $smsRecord->type)) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Message</label>
                                <div class="alert alert-light">
                                    {{ $smsRecord->message }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="mb-0">
                                            @php
                                                $statusColors = [
                                                    'sent' => 'bg-primary',
                                                    'delivered' => 'bg-success',
                                                    'failed' => 'bg-danger',
                                                    'pending' => 'bg-warning'
                                                ];
                                                $color = $statusColors[$smsRecord->status] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $color }}">{{ ucfirst($smsRecord->status) }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Cost</label>
                                        <p class="mb-0 fw-bold text-success">₦{{ number_format($smsRecord->cost, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipient Information -->
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <h5 class="mb-3">Recipient Information</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Name</label>
                                        <p class="mb-0">{{ $smsRecord->recipient_name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Phone</label>
                                        <p class="mb-0">{{ $smsRecord->recipient_phone }}</p>
                                    </div>
                                    @if($smsRecord->customer)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Customer ID</label>
                                            <p class="mb-0">#{{ $smsRecord->customer->id }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3">Delivery Information</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary-subtle">
                                    <div class="card-body text-center">
                                        <h6 class="text-primary mb-1">Sent At</h6>
                                        <p class="mb-0">{{ $smsRecord->sent_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            @if($smsRecord->delivered_at)
                                <div class="col-md-3">
                                    <div class="card bg-success-subtle">
                                        <div class="card-body text-center">
                                            <h6 class="text-success mb-1">Delivered At</h6>
                                            <p class="mb-0">{{ $smsRecord->delivered_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <div class="card bg-info-subtle">
                                    <div class="card-body text-center">
                                        <h6 class="text-info mb-1">Sent By</h6>
                                        <p class="mb-0">{{ $smsRecord->sentBy->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning-subtle">
                                    <div class="card-body text-center">
                                        <h6 class="text-warning mb-1">Provider</h6>
                                        <p class="mb-0">{{ ucfirst($smsRecord->sms_provider) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Technical Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Provider Message ID</label>
                                    <p class="mb-0 text-muted">{{ $smsRecord->provider_message_id }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Message Length</label>
                                    <p class="mb-0">{{ strlen($smsRecord->message) }} characters</p>
                                </div>
                            </div>
                        </div>

                        @if($smsRecord->error_message)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-danger">Error Message</label>
                                <div class="alert alert-danger">
                                    {{ $smsRecord->error_message }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Order (if applicable) -->
                @if($smsRecord->order)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Related Order</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Order Number:</strong> {{ $smsRecord->order->order_number }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Product:</strong> {{ $smsRecord->order->product->name }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Status:</strong>
                                            <span class="badge bg-primary">{{ ucfirst($smsRecord->order->status) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('orders.show', $smsRecord->order) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="ti ti-eye me-1"></i>View Order
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

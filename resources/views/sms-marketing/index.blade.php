@extends('layouts.dashboard')

@section('page-title', 'SMS Marketing')

@section('content')
<!-- Campaign Stats -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-primary-subtle">
                            <span class="avatar-title rounded bg-primary-subtle text-primary font-size-18">
                                <i class="ti ti-message-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-medium text-muted mb-1">Total SMS</p>
                        <h4 class="mb-0" id="total-sms">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-success-subtle">
                            <span class="avatar-title rounded bg-success-subtle text-success font-size-18">
                                <i class="ti ti-send"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-medium text-muted mb-1">Sent Today</p>
                        <h4 class="mb-0" id="sent-today">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-info-subtle">
                            <span class="avatar-title rounded bg-info-subtle text-info font-size-18">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-medium text-muted mb-1">Delivered Today</p>
                        <h4 class="mb-0" id="delivered-today">-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-warning-subtle">
                            <span class="avatar-title rounded bg-warning-subtle text-warning font-size-18">
                                <i class="ti ti-currency-dollar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-uppercase fw-medium text-muted mb-1">Cost Today</p>
                        <h4 class="mb-0" id="cost-today">₦-</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Filter SMS Records</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('sms-marketing.index') }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="campaign_name" class="form-label">Campaign Name</label>
                                <input type="text" class="form-control" id="campaign_name" name="campaign_name"
                                       value="{{ request('campaign_name') }}" placeholder="Search campaign...">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">SMS Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    @foreach($smsTypes as $key => $label)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="sms_status" class="form-label">Status</label>
                                <select class="form-select" id="sms_status" name="status">
                                    <option value="">All Status</option>
                                    @foreach($smsStatuses as $key => $label)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="sent_by" class="form-label">Sent By</label>
                                <select class="form-select" id="sent_by" name="sent_by">
                                    <option value="">All Users</option>
                                    @foreach($sentByUsers as $user)
                                        <option value="{{ $user->id }}" {{ request('sent_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('sms-marketing.index') }}" class="btn btn-light">
                                        <i class="ti ti-x me-1"></i>Clear
                                    </a>
                                    <a href="{{ route('sms-marketing.create') }}" class="btn btn-success">
                                        <i class="ti ti-plus me-1"></i>New Campaign
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SMS Records Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">SMS Records</h4>
            </div>
            <div class="card-body">
                @if($smsRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Recipient</th>
                                    <th>Message</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Cost</th>
                                    <th>Sent By</th>
                                    <th>Sent At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($smsRecords as $smsRecord)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $smsRecord->campaign_name }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $smsRecord->recipient_name }}</h6>
                                                <small class="text-muted">{{ $smsRecord->recipient_phone }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $smsRecord->message }}">
                                                {{ $smsRecord->message }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $smsRecord->type)) }}</span>
                                        </td>
                                        <td>
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
                                        </td>
                                        <td>
                                            @if($smsRecord->cost !== null)
                                                <span class="fw-bold">₦{{ number_format($smsRecord->cost, 2) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $smsRecord->sentBy->name ?? 'Unknown' }}</td>
                                        <td>
                                            @if($smsRecord->sent_at)
                                                {{ $smsRecord->sent_at->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">Not sent</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('sms-marketing.show', $smsRecord) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="ti ti-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        {{ $smsRecords->links('pagination.bootstrap-4') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-message-circle text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No SMS Records Found</h5>
                        <p class="text-muted">No SMS messages found matching your criteria.</p>
                        <a href="{{ route('sms-marketing.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Create First Campaign
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Load campaign stats
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("sms-marketing.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-sms').textContent = data.total_sms || 0;
            document.getElementById('sent-today').textContent = data.sent_today || 0;
            document.getElementById('delivered-today').textContent = data.delivered_today || 0;
            
            // Display cost from API (may be null if API doesn't provide cost)
            if (data.cost_today !== null && data.cost_today !== undefined) {
                const costToday = parseFloat(data.cost_today) || 0;
                document.getElementById('cost-today').textContent = '₦' + costToday.toFixed(2);
            } else {
                document.getElementById('cost-today').textContent = 'N/A';
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
});
</script>
@endsection

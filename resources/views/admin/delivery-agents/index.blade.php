@extends('layouts.admin')

@section('title', 'Delivery Agents')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Manage Delivery Agents</h4>
                            <p class="text-muted mb-0">View and manage all delivery agents</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.delivery-agents.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add New Agent
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search agents..."
                                       value="{{ request('search') }}" onkeyup="searchAgents(this.value)">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" onchange="filterByStatus(this.value)">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="performSearch()">
                                <i class="ti ti-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="ti ti-x me-1"></i>Clear
                            </button>
                        </div>
                        <div class="col-md-2">
                            <!-- Empty column for spacing -->
                        </div>
                    </div>

                    <!-- Delivery Agents Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                            <label class="form-check-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agents as $agent)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" value="{{ $agent->id }}">
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-primary-subtle me-2">
                                                    <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                        {{ strtoupper(substr($agent->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $agent->name }}</h6>
                                                    @if($agent->address)
                                                        <small class="text-muted">{{ Str::limit($agent->address, 30) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($agent->phone)
                                                <span class="text-muted">{{ $agent->phone }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($agent->email)
                                                <span class="text-muted">{{ $agent->email }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($agent->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $agent->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.delivery-agents.show', $agent) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="View Details">
                                                    <i class="ti ti-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.delivery-agents.edit', $agent) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Edit Agent">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <!-- Toggle Status Button -->
                                                <button type="button"
                                                        class="btn btn-sm {{ $agent->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="{{ $agent->is_active ? 'Deactivate' : 'Activate' }} Agent"
                                                        onclick="toggleStatus({{ $agent->id }})">
                                                    <i class="ti ti-{{ $agent->is_active ? 'user-off' : 'user-check' }}"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete Agent"
                                                        onclick="deleteAgent({{ $agent->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="ti ti-truck text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="text-muted">No Delivery Agents Found</h5>
                                            <p class="text-muted">No delivery agents found matching your criteria.</p>
                                            <a href="{{ route('admin.delivery-agents.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-1"></i>Add First Agent
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($agents->hasPages())
                        <div class="pagination-container">
                            {{ $agents->appends(request()->query())->links('pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forms for actions -->
<form id="toggleStatusForm" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>

<form id="deleteAgentForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function toggleStatus(agentId) {
    bootbox.confirm({
        message: "Are you sure you want to toggle this agent's status?",
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-primary'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                const form = document.getElementById('toggleStatusForm');
                form.action = `/admin/delivery-agents/${agentId}/toggle-status`;
                form.submit();
            }
        }
    });
}

function deleteAgent(agentId) {
    bootbox.confirm({
        message: "Are you sure you want to delete this delivery agent? This action cannot be undone.",
        buttons: {
            confirm: {
                label: 'Delete',
                className: 'btn-danger'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                const form = document.getElementById('deleteAgentForm');
                form.action = `/admin/delivery-agents/${agentId}`;
                form.submit();
            }
        }
    });
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Search functionality
function performSearch() {
    const searchInput = document.querySelector('input[placeholder="Search agents..."]');
    const query = searchInput.value.trim();

    const url = new URL(window.location);
    if (query) {
        url.searchParams.set('search', query);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}

function searchAgents(query) {
    // Auto-search on Enter key press
    if (event && event.key === 'Enter') {
        performSearch();
    }
}

// Filter by status
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}

// Clear filters
function clearFilters() {
    window.location.href = '{{ route("admin.delivery-agents.index") }}';
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection

@extends('layouts.admin')

@section('page-title', 'Users List')

@section('content')

<!-- Search and Filters -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="search" class="form-label">Type to search</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="{{ request('search') }}" placeholder="Search users...">
                                    <span class="input-group-text">
                                        <i class="ti ti-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="">All Status</option>
                                    <option value="true" {{ request('is_active') === 'true' ? 'selected' : '' }}>Active</option>
                                    <option value="false" {{ request('is_active') === 'false' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search me-1"></i>Search
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                        <i class="ti ti-x me-1"></i>Clear
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

<!-- Users Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Users</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Add User
                    </a>
                </div>

                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                            <label class="form-check-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Is active</th>
                                    <th>Max orders per day</th>
                                    <th>Today's Orders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" value="{{ $user->id }}">
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-primary-subtle me-2">
                                                    <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role->slug === 'admin' ? 'danger' : ($user->role->slug === 'csr' ? 'primary' : 'info') }}">
                                                {{ $user->role->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">
                                                    <i class="ti ti-check me-1"></i>True
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="ti ti-x me-1"></i>False
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->max_orders_per_day ?? 50 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">0</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Edit User">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <!-- Toggle Status Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                                                        onclick="toggleStatus({{ $user->id }})">
                                                    <i class="ti ti-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                                </button>

                                                @if($user->id !== auth()->id())
                                                <!-- Delete Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete User"
                                                        onclick="deleteUser({{ $user->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        {{ $users->links('pagination.bootstrap-4') }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="ti ti-users text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-muted">No Users Found</h5>
                        <p class="text-muted">No users found matching your criteria.</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Add First User
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Count -->
<div class="row">
    <div class="col-12">
        <p class="text-muted">{{ $users->total() }} users</p>
    </div>
</div>

<!-- Forms for actions -->
<form id="toggleStatusForm" method="POST" style="display: none;">
    @csrf
    @method('POST')
</form>

<form id="deleteUserForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function toggleStatus(userId) {
    bootbox.confirm({
        message: "Are you sure you want to toggle this user's status?",
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
                form.action = `/admin/users/${userId}/toggle-status`;
                form.submit();
            }
        }
    });
}

function deleteUser(userId) {
    bootbox.confirm({
        message: "Are you sure you want to delete this user? This action cannot be undone.",
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
                const form = document.getElementById('deleteUserForm');
                form.action = `/admin/users/${userId}`;
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

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection

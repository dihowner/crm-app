@extends('layouts.admin')

@section('title', 'Product Forms')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Manage Product Forms</h4>
                            <p class="text-muted mb-0">Create and manage embeddable order forms</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.product-forms.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Add New Form
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Search forms..."
                                       value="{{ request('search') }}" onkeyup="searchForms(this.value)">
                            </div>
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
                        <div class="col-md-5">
                            <!-- Empty column for spacing -->
                        </div>
                    </div>

                    <!-- Product Forms Table -->
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
                                    <th>Form Name</th>
                                    <th>Product</th>
                                    <th>Packages</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productForms as $form)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" value="{{ $form->id }}">
                                                <label class="form-check-label"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-info-subtle me-2">
                                                    <span class="avatar-title rounded-circle bg-info text-white font-size-16">
                                                        {{ strtoupper(substr($form->form_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $form->form_name }}</h6>
                                                    @if($form->redirect_url)
                                                        <small class="text-muted">{{ Str::limit($form->redirect_url, 30) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $form->product->name }}</span>
                                        </td>
                                        <td>
                                            @if($form->packages && count($form->packages) > 0)
                                                <span class="badge bg-primary">{{ count($form->packages) }} package(s)</span>
                                            @else
                                                <span class="text-muted">No packages</span>
                                            @endif
                                        </td>
                                        <td>{{ $form->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.product-forms.show', $form) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="View Form">
                                                    <i class="ti ti-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.product-forms.edit', $form) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Edit Form">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <!-- Copy Form Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Copy Form HTML"
                                                        onclick="copyFormToClipboard('{{ $form->id }}')">
                                                    <i class="ti ti-copy"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete Form"
                                                        onclick="deleteForm({{ $form->id }})">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="ti ti-file-text text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="text-muted">No Product Forms Found</h5>
                                            <p class="text-muted">No product forms found matching your criteria.</p>
                                            <a href="{{ route('admin.product-forms.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-1"></i>Add First Form
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($productForms->hasPages())
                        <div class="pagination-container">
                            {{ $productForms->appends(request()->query())->links('pagination.bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Forms for actions -->
<form id="deleteFormForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Copy form HTML to clipboard
function copyFormToClipboard(formId) {
    // Show loading message
    const button = event.target.closest('button');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="ti ti-loader-2 spinner-border spinner-border-sm"></i>';
    button.disabled = true;

    // Fetch form HTML from server
    fetch(`/admin/product-forms/${formId}`)
        .then(response => response.text())
        .then(html => {
            // Extract the generated form HTML from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const formHtmlElement = doc.querySelector('pre code');

            if (formHtmlElement && formHtmlElement.textContent) {
                const formHtml = formHtmlElement.textContent;

                // Copy to clipboard
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(formHtml).then(() => {
                        showCopySuccess();
                    }).catch(() => {
                        fallbackCopyTextToClipboard(formHtml);
                    });
                } else {
                    fallbackCopyTextToClipboard(formHtml);
                }
            } else {
                bootbox.alert({
                    message: 'No form HTML found. Please regenerate the form first.',
                    buttons: { ok: { label: 'OK', className: 'btn-primary' } }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching form:', error);
            bootbox.alert({
                message: 'Error fetching form HTML. Please try again.',
                buttons: { ok: { label: 'OK', className: 'btn-primary' } }
            });
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalHtml;
            button.disabled = false;
        });
}

function showCopySuccess() {
    bootbox.alert({
        message: 'Form HTML copied to clipboard successfully!',
        buttons: { ok: { label: 'OK', className: 'btn-primary' } }
    });
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.top = '0';
    textArea.style.left = '0';
    textArea.style.position = 'fixed';

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess();
        } else {
            throw new Error('Copy command was unsuccessful');
        }
    } catch (err) {
        console.error('Fallback: Unable to copy', err);
        bootbox.alert({
            message: 'Failed to copy form HTML. Please try again.',
            buttons: { ok: { label: 'OK', className: 'btn-primary' } }
        });
    }

    document.body.removeChild(textArea);
}

function deleteForm(formId) {
    bootbox.confirm({
        message: "Are you sure you want to delete this product form? This action cannot be undone.",
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
                const form = document.getElementById('deleteFormForm');
                form.action = `/admin/product-forms/${formId}`;
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
    const searchInput = document.querySelector('input[placeholder="Search forms..."]');
    const query = searchInput.value.trim();

    const url = new URL(window.location);
    if (query) {
        url.searchParams.set('search', query);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}

function searchForms(query) {
    // Auto-search on Enter key press
    if (event && event.key === 'Enter') {
        performSearch();
    }
}

// Clear filters
function clearFilters() {
    window.location.href = '{{ route("admin.product-forms.index") }}';
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

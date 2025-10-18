@extends('layouts.admin')

@section('title', 'Product Form Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">{{ $productForm->form_name }}</h4>
                            <p class="text-muted mb-0">Product Form Details</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.product-forms.edit', $productForm) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>Edit Form
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Form Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Form Name:</strong></td>
                                            <td>{{ $productForm->form_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Product:</strong></td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $productForm->product->name }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Redirect URL:</strong></td>
                                            <td>
                                                <a href="{{ $productForm->redirect_url }}" target="_blank" class="text-primary">
                                                    {{ $productForm->redirect_url }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Button Text:</strong></td>
                                            <td>{{ $productForm->button_text }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($productForm->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $productForm->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $productForm->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Packages</h5>
                                </div>
                                <div class="card-body">
                                    @if($productForm->packages && count($productForm->packages) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Package Name</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($productForm->packages as $package)
                                                        <tr>
                                                            <td>{{ $package['name'] }}</td>
                                                            <td>₦{{ number_format($package['price'], 2) }}</td>
                                                            <td>{{ $package['quantity'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No packages configured.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Generated Form HTML -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="card-title mb-0">Generated Form HTML</h5>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-success btn-sm" onclick="copyFormToClipboard()">
                                                <i class="ti ti-copy me-1"></i>Copy Form
                                            </button>
                                            <form method="POST" action="{{ route('admin.product-forms.regenerate', $productForm) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="ti ti-refresh me-1"></i>Regenerate
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($productForm->generated_form)
                                        <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ $productForm->generated_form }}</code></pre>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="ti ti-file-text" style="font-size: 2rem;"></i>
                                            <p class="mt-2">No form HTML generated yet. Click "Regenerate" to create the form HTML.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Preview -->
                    @if($productForm->generated_form)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Form Preview</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                            {!! $productForm->generated_form !!}
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
</div>

<script>
function copyFormToClipboard() {
    const formHtml = @json($productForm->generated_form ?? '');

    if (!formHtml) {
        bootbox.alert({
            message: 'No form HTML available to copy. Please regenerate the form first.',
            buttons: { ok: { label: 'OK', className: 'btn-primary' } }
        });
        return;
    }

    // Create a temporary textarea to copy the text
    const textarea = document.createElement('textarea');
    textarea.value = formHtml;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);

    bootbox.alert({
        message: 'Form HTML copied to clipboard successfully!',
        buttons: { ok: { label: 'OK', className: 'btn-primary' } }
    });
}
</script>
@endsection

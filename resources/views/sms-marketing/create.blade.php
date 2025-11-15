@extends('layouts.dashboard')

@section('page-title', 'Create SMS Campaign')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Create SMS Campaign</h4>
                <a href="{{ route('sms-marketing.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">
                    <i class="ti ti-arrow-left me-1"></i>Back to SMS Marketing
                </a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-theme">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="smsCampaignForm">
                    @csrf

                    <div class="row">
                        <!-- Campaign Details -->
                        <div class="col-lg-6">
                            <h5 class="mb-3">Campaign Details</h5>

                            <div class="mb-3">
                                <label for="campaign_name" class="form-label">Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="campaign_name" name="campaign_name"
                                       placeholder="e.g., Holiday Promotion 2025" required>
                            </div>

                            <div class="mb-3">
                                <label for="sms_type" class="form-label">SMS Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="sms_type" name="sms_type" required>
                                    <option value="">Select SMS Type</option>
                                    <option value="bulk">Bulk SMS</option>
                                    <option value="order_confirmation">Order Confirmation</option>
                                    <option value="order_reminder">Order Reminder</option>
                                    <option value="delivery_notification">Delivery Notification</option>
                                    <option value="marketing_promotion">Marketing Promotion</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="customer_group" class="form-label">Target Audience <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_group" name="customer_group" required>
                                    @foreach($customerGroups as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="col-lg-6">
                            <h5 class="mb-3">Message Content</h5>

                            <div class="mb-3">
                                <label for="template" class="form-label">Message Template</label>
                                <select class="form-select" id="template" name="template">
                                    <option value="">Select Template</option>
                                    @foreach($smsTemplates as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="4"
                                          placeholder="Enter your SMS message here..." required maxlength="160"></textarea>
                                <div class="form-text">
                                    <span id="char-count">0</span>/160 characters
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Available Placeholders</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark">[name]</span>
                                    <span class="badge bg-light text-dark">[phone]</span>
                                    <span class="badge bg-light text-dark">[email]</span>
                                </div>
                                <small class="text-muted">Use these placeholders to personalize messages</small>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">Message Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div id="message-preview" class="text-muted">
                                        Your message preview will appear here...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">Campaign Summary</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="mb-1" id="estimated-recipients">-</h4>
                                                <p class="text-muted mb-0">Estimated Recipients</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="mb-1" id="message-length">-</h4>
                                                <p class="text-muted mb-0">Message Length</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="mb-1" id="sms-count">-</h4>
                                                <p class="text-muted mb-0">SMS Count</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('sms-marketing.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="send-campaign-btn">
                                    <i class="ti ti-send me-1"></i>Send Campaign
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
document.getElementById('message').addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('char-count').textContent = charCount;
    document.getElementById('message-length').textContent = charCount;

    // Update SMS count (160 chars per SMS)
    const smsCount = Math.ceil(charCount / 160);
    document.getElementById('sms-count').textContent = smsCount;

    // Update preview
    updatePreview();
});

// Template selection
document.getElementById('template').addEventListener('change', function() {
    const templates = {
        'order_confirmation': 'Dear [name], your order has been confirmed! Order #12345 will be delivered soon. Thank you for choosing us!',
        'order_reminder': 'Hi [name], this is a reminder about your pending order. Please contact us if you have any questions.',
        'delivery_notification': 'Hello [name], your order is out for delivery! Expected delivery time: 2-4 hours. Track your order: [link]',
        'marketing_promotion': 'Special offer for [name]! Get 20% off on your next order. Use code SAVE20. Valid until end of month.',
        'custom': ''
    };

    if (this.value && templates[this.value]) {
        document.getElementById('message').value = templates[this.value];
        document.getElementById('message').dispatchEvent(new Event('input'));
    }
});

// Update preview
function updatePreview() {
    const message = document.getElementById('message').value;
    const preview = document.getElementById('message-preview');

    if (message.trim()) {
        // Replace placeholders with sample data
        let previewText = message.replace(/\[name\]/g, 'John Doe');
        previewText = previewText.replace(/\[phone\]/g, '+2348012345678');
        previewText = previewText.replace(/\[email\]/g, 'john@example.com');

        preview.innerHTML = `<strong>Sample Preview:</strong><br>${previewText}`;
    } else {
        preview.innerHTML = 'Your message preview will appear here...';
    }
}

// Form submission
document.getElementById('smsCampaignForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById('send-campaign-btn');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.innerHTML = '<i class="ti ti-loader-2 me-1"></i>Sending...';
    submitBtn.disabled = true;

    fetch('{{ route("sms-marketing.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);

            // Show campaign stats
            if (data.stats) {
                showCampaignStats(data.stats);
            }

            // Redirect to SMS marketing index after delay
            setTimeout(() => {
                window.location.href = '{{ route("sms-marketing.index") }}';
            }, 3000);
        } else {
            showAlert('danger', data.error || 'Failed to send SMS campaign');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while sending the campaign');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Show campaign stats
function showCampaignStats(stats) {
    const statsDiv = document.createElement('div');
    statsDiv.className = 'alert alert-success alert-dismissible fade show';
    statsDiv.innerHTML = `
        <h6>Campaign Sent Successfully!</h6>
        <p><strong>Total Recipients:</strong> ${stats.total}</p>
        <p><strong>Successfully Sent:</strong> ${stats.sent}</p>
        <p><strong>Failed:</strong> ${stats.failed}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const content = document.querySelector('.content-page');
    if (content) {
        content.insertBefore(statsDiv, content.firstChild);

        // Auto remove after 5 seconds
        setTimeout(() => {
            statsDiv.remove();
        }, 5000);
    }
}

// Show alert
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

// Load customer count for selected group
document.getElementById('customer_group').addEventListener('change', function() {
    const customerGroup = this.value;
    
    // Show loading state
    document.getElementById('estimated-recipients').textContent = 'Loading...';
    
    // Fetch actual customer count from API
    fetch(`{{ route('sms-marketing.customer-count') }}?customer_group=${customerGroup}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const count = data.count || 0;
            document.getElementById('estimated-recipients').textContent = count;
        } else {
            document.getElementById('estimated-recipients').textContent = '0';
            console.error('Failed to get customer count:', data.error || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error fetching customer count:', error);
        document.getElementById('estimated-recipients').textContent = 'Error';
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    document.getElementById('customer_group').dispatchEvent(new Event('change'));
});
</script>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-1">App Settings</h4>
                    <p class="text-muted mb-0">Configure application settings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Row 1: App Name and Theme Color -->
    <div class="row mb-4">
        <!-- App Name -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-brand-abstract me-2"></i>App Name
                    </h4>
                    <button type="submit" form="appNameForm" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Save
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.app-settings.update') }}" id="appNameForm">
                        @csrf
                        <input type="hidden" name="setting_type" value="app_name">
                        <div class="mb-3">
                            <label for="app_name" class="form-label">Application Name</label>
                            <p class="text-muted small mb-2">This name will be displayed throughout the application</p>
                            <input type="text"
                                   class="form-control @error('app_name') is-invalid @enderror"
                                   id="app_name"
                                   name="app_name"
                                   value="{{ old('app_name', $appName) }}"
                                   required>
                            @error('app_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Theme Color -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-palette me-2"></i>Theme Color
                    </h4>
                    <button type="submit" form="themeColorForm" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Save
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.app-settings.update') }}" id="themeColorForm">
                        @csrf
                        <input type="hidden" name="setting_type" value="theme_color">
                        <div class="mb-3">
                            <label for="theme_color" class="form-label">Primary Theme Color</label>
                            <p class="text-muted small mb-2">Select the primary color theme for the application</p>
                            <select class="form-select @error('theme_color') is-invalid @enderror"
                                    id="theme_color"
                                    name="theme_color"
                                    required>
                                <option value="theme-blue" {{ old('theme_color', $themeColor) == 'theme-blue' ? 'selected' : '' }}>Blue Theme</option>
                                <option value="theme-purple" {{ old('theme_color', $themeColor) == 'theme-purple' ? 'selected' : '' }}>Purple Theme</option>
                                <option value="theme-green" {{ old('theme_color', $themeColor) == 'theme-green' ? 'selected' : '' }}>Green Theme</option>
                                <option value="theme-red" {{ old('theme_color', $themeColor) == 'theme-red' ? 'selected' : '' }}>Red Theme</option>
                                <option value="theme-orange" {{ old('theme_color', $themeColor) == 'theme-orange' ? 'selected' : '' }}>Orange Theme</option>
                            </select>
                            @error('theme_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Email API Configuration (Full Row) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-mail me-2"></i>Email API Configuration
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm" id="testEmailBtn">
                            <i class="ti ti-mail-check me-1"></i>Test Email
                        </button>
                        <button type="submit" form="emailForm" class="btn btn-primary btn-sm">
                            <i class="ti ti-device-floppy me-1"></i>Save
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.app-settings.update') }}" id="emailForm">
                        @csrf
                        <input type="hidden" name="setting_type" value="email">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_driver" class="form-label">Email Driver</label>
                                    <select class="form-select @error('email_config.driver') is-invalid @enderror"
                                            id="email_driver"
                                            name="email_config[driver]"
                                            required>
                                        <option value="smtp" {{ old('email_config.driver', $emailConfig['driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                        <option value="mailgun" {{ old('email_config.driver', $emailConfig['driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        <option value="ses" {{ old('email_config.driver', $emailConfig['driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                        <option value="sendgrid" {{ old('email_config.driver', $emailConfig['driver'] ?? '') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                    </select>
                                    @error('email_config.driver')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email_from_address" class="form-label">From Address <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email_config.from_address') is-invalid @enderror"
                                           id="email_from_address"
                                           name="email_config[from_address]"
                                           value="{{ old('email_config.from_address', $emailConfig['from_address'] ?? '') }}"
                                           required>
                                    @error('email_config.from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SMTP Configuration -->
                        <div id="smtp-config" style="display: {{ (old('email_config.driver', $emailConfig['driver'] ?? 'smtp') == 'smtp') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="email_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('email_config.host') is-invalid @enderror"
                                               id="email_host"
                                               name="email_config[host]"
                                               value="{{ old('email_config.host', $emailConfig['host'] ?? '') }}">
                                        @error('email_config.host')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="email_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                        <input type="number"
                                               class="form-control @error('email_config.port') is-invalid @enderror"
                                               id="email_port"
                                               name="email_config[port]"
                                               value="{{ old('email_config.port', $emailConfig['port'] ?? '587') }}">
                                        @error('email_config.port')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="email_username" class="form-label">SMTP Username</label>
                                        <input type="text"
                                               class="form-control @error('email_config.username') is-invalid @enderror"
                                               id="email_username"
                                               name="email_config[username]"
                                               value="{{ old('email_config.username', $emailConfig['username'] ?? '') }}">
                                        @error('email_config.username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="email_password" class="form-label">SMTP Password</label>
                                        <input type="password"
                                               class="form-control @error('email_config.password') is-invalid @enderror"
                                               id="email_password"
                                               name="email_config[password]"
                                               value="{{ old('email_config.password', $emailConfig['password'] ?? '') }}">
                                        @error('email_config.password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_encryption" class="form-label">Encryption</label>
                                        <select class="form-select @error('email_config.encryption') is-invalid @enderror"
                                                id="email_encryption"
                                                name="email_config[encryption]">
                                            <option value="tls" {{ old('email_config.encryption', $emailConfig['encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('email_config.encryption', $emailConfig['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                        @error('email_config.encryption')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_from_name" class="form-label">From Name</label>
                                        <input type="text"
                                               class="form-control @error('email_config.from_name') is-invalid @enderror"
                                               id="email_from_name"
                                               name="email_config[from_name]"
                                               value="{{ old('email_config.from_name', $emailConfig['from_name'] ?? App\Models\AppSetting::getValue('app_name', 'AfroWellness')) }}">
                                        @error('email_config.from_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mailgun Configuration -->
                        <div id="mailgun-config" style="display: {{ (old('email_config.driver', $emailConfig['driver'] ?? 'smtp') == 'mailgun') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mailgun_domain" class="form-label">Mailgun Domain <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('email_config.mailgun_domain') is-invalid @enderror"
                                               id="mailgun_domain"
                                               name="email_config[mailgun_domain]"
                                               value="{{ old('email_config.mailgun_domain', $emailConfig['mailgun_domain'] ?? '') }}">
                                        <small class="text-muted">Your Mailgun domain (e.g., mg.example.com)</small>
                                        @error('email_config.mailgun_domain')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mailgun_secret" class="form-label">Mailgun Secret Key <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('email_config.mailgun_secret') is-invalid @enderror"
                                               id="mailgun_secret"
                                               name="email_config[mailgun_secret]"
                                               value="{{ old('email_config.mailgun_secret', $emailConfig['mailgun_secret'] ?? '') }}">
                                        <small class="text-muted">Your Mailgun API secret key</small>
                                        @error('email_config.mailgun_secret')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="mailgun_endpoint" class="form-label">Mailgun Endpoint</label>
                                        <input type="text"
                                               class="form-control @error('email_config.mailgun_endpoint') is-invalid @enderror"
                                               id="mailgun_endpoint"
                                               name="email_config[mailgun_endpoint]"
                                               value="{{ old('email_config.mailgun_endpoint', $emailConfig['mailgun_endpoint'] ?? 'api.mailgun.net') }}">
                                        <small class="text-muted">Optional, defaults to api.mailgun.net</small>
                                        @error('email_config.mailgun_endpoint')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_from_name_mailgun" class="form-label">From Name</label>
                                        <input type="text"
                                               class="form-control @error('email_config.from_name') is-invalid @enderror"
                                               id="email_from_name_mailgun"
                                               name="email_config[from_name]"
                                               value="{{ old('email_config.from_name', $emailConfig['from_name'] ?? App\Models\AppSetting::getValue('app_name', 'AfroWellness')) }}">
                                        @error('email_config.from_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SendGrid Configuration -->
                        <div id="sendgrid-config" style="display: {{ (old('email_config.driver', $emailConfig['driver'] ?? 'smtp') == 'sendgrid') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sendgrid_api_key" class="form-label">SendGrid API Key <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('email_config.sendgrid_api_key') is-invalid @enderror"
                                               id="sendgrid_api_key"
                                               name="email_config[sendgrid_api_key]"
                                               value="{{ old('email_config.sendgrid_api_key', $emailConfig['sendgrid_api_key'] ?? '') }}">
                                        <small class="text-muted">Your SendGrid API key</small>
                                        @error('email_config.sendgrid_api_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_from_name_sendgrid" class="form-label">From Name</label>
                                        <input type="text"
                                               class="form-control @error('email_config.from_name') is-invalid @enderror"
                                               id="email_from_name_sendgrid"
                                               name="email_config[from_name]"
                                               value="{{ old('email_config.from_name', $emailConfig['from_name'] ?? App\Models\AppSetting::getValue('app_name', 'AfroWellness')) }}">
                                        @error('email_config.from_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SES Configuration -->
                        <div id="ses-config" style="display: {{ (old('email_config.driver', $emailConfig['driver'] ?? 'smtp') == 'ses') ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ses_access_key" class="form-label">AWS Access Key ID <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('email_config.ses_access_key') is-invalid @enderror"
                                               id="ses_access_key"
                                               name="email_config[ses_access_key]"
                                               value="{{ old('email_config.ses_access_key', $emailConfig['ses_access_key'] ?? '') }}">
                                        <small class="text-muted">Your AWS access key ID</small>
                                        @error('email_config.ses_access_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ses_secret_key" class="form-label">AWS Secret Access Key <span class="text-danger">*</span></label>
                                        <input type="password"
                                               class="form-control @error('email_config.ses_secret_key') is-invalid @enderror"
                                               id="ses_secret_key"
                                               name="email_config[ses_secret_key]"
                                               value="{{ old('email_config.ses_secret_key', $emailConfig['ses_secret_key'] ?? '') }}">
                                        <small class="text-muted">Your AWS secret access key</small>
                                        @error('email_config.ses_secret_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="ses_region" class="form-label">AWS Region <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('email_config.ses_region') is-invalid @enderror"
                                               id="ses_region"
                                               name="email_config[ses_region]"
                                               value="{{ old('email_config.ses_region', $emailConfig['ses_region'] ?? 'us-east-1') }}">
                                        <small class="text-muted">AWS region (e.g., us-east-1, eu-west-1)</small>
                                        @error('email_config.ses_region')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_from_name_ses" class="form-label">From Name</label>
                                        <input type="text"
                                               class="form-control @error('email_config.from_name') is-invalid @enderror"
                                               id="email_from_name_ses"
                                               name="email_config[from_name]"
                                               value="{{ old('email_config.from_name', $emailConfig['from_name'] ?? App\Models\AppSetting::getValue('app_name', 'AfroWellness')) }}">
                                        @error('email_config.from_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: SMS API Configuration (Full Row) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-message-circle me-2"></i>SMS API Configuration
                    </h4>
                    <button type="submit" form="smsForm" class="btn btn-primary btn-sm">
                        <i class="ti ti-device-floppy me-1"></i>Save
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.app-settings.update') }}" id="smsForm">
                        @csrf
                        <input type="hidden" name="setting_type" value="sms">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sms_api_url" class="form-label">API URL <span class="text-danger">*</span></label>
                                    <input type="url"
                                           class="form-control @error('sms_config.api_url') is-invalid @enderror"
                                           id="sms_api_url"
                                           name="sms_config[api_url]"
                                           value="{{ old('sms_config.api_url', $smsConfig['api_url'] ?? 'http://sms.paynex.org/api/sendsms.php') }}"
                                           required>
                                    @error('sms_config.api_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sms_auth_token" class="form-label">Authorization Token <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('sms_config.auth_token') is-invalid @enderror"
                                           id="sms_auth_token"
                                           name="sms_config[auth_token]"
                                           value="{{ old('sms_config.auth_token', $smsConfig['auth_token'] ?? '') }}"
                                           required>
                                    <small class="text-muted">This token is used in the Authorization header</small>
                                    @error('sms_config.auth_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sms_sender_id" class="form-label">Sender ID <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('sms_config.sender_id') is-invalid @enderror"
                                           id="sms_sender_id"
                                           name="sms_config[sender_id]"
                                           value="{{ old('sms_config.sender_id', $smsConfig['sender_id'] ?? 'PEAKSYSTEMS') }}"
                                           required>
                                    @error('sms_config.sender_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sms_product_id" class="form-label">Product ID <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('sms_config.product_id') is-invalid @enderror"
                                           id="sms_product_id"
                                           name="sms_config[product_id]"
                                           value="{{ old('sms_config.product_id', $smsConfig['product_id'] ?? 'dndroute') }}"
                                           required>
                                    @error('sms_config.product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailDriver = document.getElementById('email_driver');
    const smtpConfig = document.getElementById('smtp-config');
    const mailgunConfig = document.getElementById('mailgun-config');
    const sendgridConfig = document.getElementById('sendgrid-config');
    const sesConfig = document.getElementById('ses-config');

    // Sync "From Name" field values across all driver sections
    const fromNameFields = [
        'email_from_name',
        'email_from_name_mailgun',
        'email_from_name_sendgrid',
        'email_from_name_ses'
    ];

    function syncFromNameFields(sourceField) {
        const value = sourceField ? sourceField.value : '';
        fromNameFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field !== sourceField) {
                field.value = value;
            }
        });
    }

    // Add event listeners to sync "From Name" fields
    fromNameFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                syncFromNameFields(this);
            });
        }
    });

    function toggleEmailConfig() {
        const driver = emailDriver.value;

        // Get current "From Name" value before hiding sections
        const currentFromNameField = fromNameFields.map(id => document.getElementById(id)).find(field => field && field.offsetParent !== null);
        const currentFromNameValue = currentFromNameField ? currentFromNameField.value : '';

        // Hide all config sections
        if (smtpConfig) smtpConfig.style.display = 'none';
        if (mailgunConfig) mailgunConfig.style.display = 'none';
        if (sendgridConfig) sendgridConfig.style.display = 'none';
        if (sesConfig) sesConfig.style.display = 'none';

        // Remove required attributes from all fields
        const allFields = [
            'email_host', 'email_port', 'mailgun_domain', 'mailgun_secret',
            'sendgrid_api_key', 'ses_access_key', 'ses_secret_key', 'ses_region'
        ];
        allFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) field.removeAttribute('required');
        });

        // Show and set required fields based on driver
        switch(driver) {
            case 'smtp':
                if (smtpConfig) smtpConfig.style.display = 'block';
                const emailHost = document.getElementById('email_host');
                const emailPort = document.getElementById('email_port');
                if (emailHost) emailHost.setAttribute('required', 'required');
                if (emailPort) emailPort.setAttribute('required', 'required');
                // Sync "From Name" value to visible field
                const smtpFromName = document.getElementById('email_from_name');
                if (smtpFromName && currentFromNameValue) {
                    smtpFromName.value = currentFromNameValue;
                }
                break;
            case 'mailgun':
                if (mailgunConfig) mailgunConfig.style.display = 'block';
                const mailgunDomain = document.getElementById('mailgun_domain');
                const mailgunSecret = document.getElementById('mailgun_secret');
                if (mailgunDomain) mailgunDomain.setAttribute('required', 'required');
                if (mailgunSecret) mailgunSecret.setAttribute('required', 'required');
                // Sync "From Name" value to visible field
                const mailgunFromName = document.getElementById('email_from_name_mailgun');
                if (mailgunFromName && currentFromNameValue) {
                    mailgunFromName.value = currentFromNameValue;
                }
                break;
            case 'sendgrid':
                if (sendgridConfig) sendgridConfig.style.display = 'block';
                const sendgridApiKey = document.getElementById('sendgrid_api_key');
                if (sendgridApiKey) sendgridApiKey.setAttribute('required', 'required');
                // Sync "From Name" value to visible field
                const sendgridFromName = document.getElementById('email_from_name_sendgrid');
                if (sendgridFromName && currentFromNameValue) {
                    sendgridFromName.value = currentFromNameValue;
                }
                break;
            case 'ses':
                if (sesConfig) sesConfig.style.display = 'block';
                const sesAccessKey = document.getElementById('ses_access_key');
                const sesSecretKey = document.getElementById('ses_secret_key');
                const sesRegion = document.getElementById('ses_region');
                if (sesAccessKey) sesAccessKey.setAttribute('required', 'required');
                if (sesSecretKey) sesSecretKey.setAttribute('required', 'required');
                if (sesRegion) sesRegion.setAttribute('required', 'required');
                // Sync "From Name" value to visible field
                const sesFromName = document.getElementById('email_from_name_ses');
                if (sesFromName && currentFromNameValue) {
                    sesFromName.value = currentFromNameValue;
                }
                break;
        }

        // Sync all "From Name" fields after showing the active section
        const visibleFromNameField = fromNameFields.map(id => document.getElementById(id)).find(field => field && field.offsetParent !== null);
        if (visibleFromNameField) {
            syncFromNameFields(visibleFromNameField);
        }
    }

    if (emailDriver) {
        emailDriver.addEventListener('change', toggleEmailConfig);
        // Initialize on page load
        toggleEmailConfig();
    }

    // Test Email functionality
    const testEmailBtn = document.getElementById('testEmailBtn');
    if (testEmailBtn) {
        testEmailBtn.addEventListener('click', function() {
            const testEmail = prompt('Enter email address to send test email:');
            if (!testEmail) {
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(testEmail)) {
                bootbox.alert({
                    message: 'Please enter a valid email address.',
                    buttons: {
                        ok: {
                            label: 'OK',
                            className: 'btn-primary'
                        }
                    }
                });
                return;
            }

            // Show loading state
            const originalText = testEmailBtn.innerHTML;
            testEmailBtn.innerHTML = '<i class="ti ti-loader-2 me-1"></i>Sending...';
            testEmailBtn.disabled = true;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                bootbox.alert({
                    message: 'CSRF token not found. Please refresh the page and try again.',
                    buttons: {
                        ok: {
                            label: 'OK',
                            className: 'btn-primary'
                        }
                    }
                });
                testEmailBtn.innerHTML = originalText;
                testEmailBtn.disabled = false;
                return;
            }

            fetch('{{ route("admin.app-settings.test-email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    test_email: testEmail
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootbox.alert({
                        message: data.message || 'Test email sent successfully! Please check your inbox.',
                        buttons: {
                            ok: {
                                label: 'OK',
                                className: 'btn-primary'
                            }
                        }
                    });
                } else {
                    bootbox.alert({
                        message: data.error || 'Failed to send test email. Please check your configuration.',
                        buttons: {
                            ok: {
                                label: 'OK',
                                className: 'btn-primary'
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                bootbox.alert({
                    message: 'An error occurred while sending test email. Please try again.',
                    buttons: {
                        ok: {
                            label: 'OK',
                            className: 'btn-primary'
                        }
                    }
                });
            })
            .finally(() => {
                testEmailBtn.innerHTML = originalText;
                testEmailBtn.disabled = false;
            });
        });
    }
});
</script>
@endsection

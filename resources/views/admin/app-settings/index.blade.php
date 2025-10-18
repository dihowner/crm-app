@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title mb-1">App Settings</h4>
                    <p class="text-muted mb-0">Configure application settings and preferences</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning" onclick="resetSettings()">
                        <i class="ti ti-refresh me-2"></i>Reset to Default
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Categories -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Category Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4" role="tablist">
                        @foreach($categories as $categoryKey => $categoryName)
                            <li class="nav-item">
                                <a class="nav-link {{ $selectedCategory === $categoryKey ? 'active' : '' }}"
                                   href="{{ route('admin.app-settings.index', ['category' => $categoryKey]) }}"
                                   style="{{ $selectedCategory === $categoryKey ? 'background-color: var(--bs-primary) !important; color: white !important;' : 'color: #6c757d;' }}">
                                    @switch($categoryKey)
                                        @case('general')
                                            <i class="ti ti-settings me-2"></i>
                                            @break
                                        @case('inventory')
                                            <i class="ti ti-package me-2"></i>
                                            @break
                                        @case('orders')
                                            <i class="ti ti-shopping-cart me-2"></i>
                                            @break
                                        @case('integration')
                                            <i class="ti ti-plug me-2"></i>
                                            @break
                                        @case('ui')
                                            <i class="ti ti-palette me-2"></i>
                                            @break
                                    @endswitch
                                    {{ $categoryName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Settings Form -->
                    <form method="POST" action="{{ route('admin.app-settings.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            @forelse($settings->get($selectedCategory, collect()) as $setting)
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="{{ $setting->key }}" class="form-label">
                                                    {{ $setting->label }}
                                                    @if($setting->is_required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>

                                                @if($setting->description)
                                                    <div class="form-text">{{ $setting->description }}</div>
                                                @endif

                                                @switch($setting->type)
                                                    @case('text')
                                                    @case('email')
                                                    @case('url')
                                                        <input type="{{ $setting->type }}"
                                                               class="form-control"
                                                               id="{{ $setting->key }}"
                                                               name="settings[{{ $setting->key }}]"
                                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                               {{ $setting->is_required ? 'required' : '' }}>
                                                        @break

                                                    @case('number')
                                                        <input type="number"
                                                               class="form-control"
                                                               id="{{ $setting->key }}"
                                                               name="settings[{{ $setting->key }}]"
                                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                               {{ $setting->is_required ? 'required' : '' }}>
                                                        @break

                                                    @case('textarea')
                                                        <textarea class="form-control"
                                                                  id="{{ $setting->key }}"
                                                                  name="settings[{{ $setting->key }}]"
                                                                  rows="3"
                                                                  {{ $setting->is_required ? 'required' : '' }}>{{ old("settings.{$setting->key}", $setting->value) }}</textarea>
                                                        @break

                                                    @case('boolean')
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   id="{{ $setting->key }}"
                                                                   name="settings[{{ $setting->key }}]"
                                                                   value="1"
                                                                   {{ old("settings.{$setting->key}", $setting->value) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="{{ $setting->key }}">
                                                                Enable {{ $setting->label }}
                                                            </label>
                                                        </div>
                                                        @break

                                                    @case('select')
                                                        <select class="form-select"
                                                                id="{{ $setting->key }}"
                                                                name="settings[{{ $setting->key }}]"
                                                                {{ $setting->is_required ? 'required' : '' }}>
                                                            <option value="">Select {{ $setting->label }}</option>
                                                            @if($setting->options)
                                                                @foreach($setting->options as $optionValue => $optionLabel)
                                                                    <option value="{{ $optionValue }}"
                                                                            {{ old("settings.{$setting->key}", $setting->value) == $optionValue ? 'selected' : '' }}>
                                                                        {{ $optionLabel }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @break

                                                    @case('file')
                                                        <input type="file"
                                                               class="form-control"
                                                               id="{{ $setting->key }}"
                                                               name="settings[{{ $setting->key }}]"
                                                               accept="image/*">
                                                        @if($setting->value)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Current file:</small><br>
                                                                <img src="{{ asset('storage/' . $setting->value) }}"
                                                                     alt="Current file"
                                                                     style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                            </div>
                                                        @endif
                                                        @break
                                                @endswitch

                                                @error("settings.{$setting->key}")
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-settings" style="font-size: 3rem;"></i>
                                            <h5 class="mt-2">No settings found</h5>
                                            <p>No settings are configured for this category yet.</p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if($settings->get($selectedCategory, collect())->isNotEmpty())
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-2"></i>Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Settings Form -->
<form id="reset-form" method="POST" action="{{ route('admin.app-settings.reset') }}" style="display: none;">
    @csrf
    <input type="hidden" name="category" value="{{ $selectedCategory }}">
</form>

<script>
function resetSettings() {
    bootbox.confirm({
        message: 'Are you sure you want to reset all settings in this category to their default values? This action cannot be undone.',
        buttons: {
            confirm: {
                label: 'Yes, Reset',
                className: 'btn-warning'
            },
            cancel: {
                label: 'Cancel',
                className: 'btn-secondary'
            }
        },
        callback: function (result) {
            if (result) {
                document.getElementById('reset-form').submit();
            }
        }
    });
}
</script>
@endsection

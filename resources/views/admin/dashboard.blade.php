@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')

@section('content')

<!-- Welcome Section -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <h2 class="text-primary mb-3">Welcome, {{ auth()->user()->name }}!</h2>
                    <p class="text-muted fs-16 mb-4">You have full control to change everything on this App.</p>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Super Admin Access:</strong> You can manage users, system settings, and all aspects of the CRM application.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Total Agents</p>
                        <h4 class="mb-0">{{ \App\Models\Agent::count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-primary-subtle">
                            <span class="avatar-title rounded font-size-20 bg-primary text-white">
                                <i class="ti ti-truck"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Total Customers</p>
                        <h4 class="mb-0">{{ \App\Models\Customer::count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-success-subtle">
                            <span class="avatar-title rounded font-size-20 bg-success text-white">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Total Products</p>
                        <h4 class="mb-0">{{ \App\Models\Product::count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-info-subtle">
                            <span class="avatar-title rounded font-size-20 bg-info text-white">
                                <i class="ti ti-package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Total Orders</p>
                        <h4 class="mb-0">{{ \App\Models\Order::count() }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-warning-subtle">
                            <span class="avatar-title rounded font-size-20 bg-warning text-white">
                                <i class="ti ti-shopping-cart"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-lg w-100">
                            <i class="ti ti-users me-2"></i>
                            Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-info btn-lg w-100">
                            <i class="ti ti-shopping-cart me-2"></i>
                            Manage Products
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.agent-inventory.index') }}" class="btn btn-success btn-lg w-100">
                            <i class="ti ti-package me-2"></i>
                            Agent Inventory
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.app-settings.index') }}" class="btn btn-warning btn-lg w-100">
                            <i class="ti ti-settings me-2"></i>
                            App Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

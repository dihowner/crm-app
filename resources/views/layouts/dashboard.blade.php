<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') - CRM App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A secure e-commerce CRM system for managing orders, customers, and inventory." name="description" />
    <meta content="CRM App" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Universal Theme System -->
    <link href="{{ asset('assets/css/universal-theme.css') }}" rel="stylesheet" type="text/css" />
    <!-- Theme Examples -->
    <link href="{{ asset('assets/css/themes-examples.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Tabler Icons Fix -->
    <link href="{{ asset('assets/css/tabler-icons-fix.css') }}" rel="stylesheet" type="text/css" />
    <!-- Dynamic Sidebar Theming -->
    <link href="{{ asset('assets/css/dynamic-sidebar.css') }}" rel="stylesheet" type="text/css" />
    <!-- Desktop Layout Fix -->
    <link href="{{ asset('assets/css/desktop-layout-fix.css') }}" rel="stylesheet" type="text/css" />
    <!-- Font Improvements -->
    <link href="{{ asset('assets/css/font-improvements.css') }}" rel="stylesheet" type="text/css" />
    <!-- Status Badge Colors -->
    <link href="{{ asset('assets/css/status-badges.css') }}" rel="stylesheet" type="text/css" />
    <!-- CRM Logo Text Styling -->
    <link href="{{ asset('assets/css/crm-logo-text.css') }}" rel="stylesheet" type="text/css" />
    <!-- Mobile Navigation -->
    <link href="{{ asset('assets/css/mobile-nav.css') }}" rel="stylesheet" type="text/css" />
    <!-- Order Status Badges -->
    <link href="{{ asset('assets/css/order-status-badges.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/button-theming.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/alert-theming.css') }}" rel="stylesheet" type="text/css" />
    <!-- Pagination Fix -->
    <link href="{{ asset('assets/css/pagination-fix.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
</head>

<body class="{{ config('theme.default_theme', 'theme-blue') }}">
    <!-- Begin page -->
    <div class="wrapper">

        <!-- Sidenav Menu Start -->
        <div class="sidenav-menu">

            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="logo">
                <span class="logo-light">
                    <span class="logo-lg">
                        <h2 class="text-white mb-0 fw-bold">CRM</h2>
                    </span>
                    <span class="logo-sm">
                        <h4 class="text-white mb-0 fw-bold">CRM</h4>
                    </span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg">
                        <h2 class="text-white mb-0 fw-bold">CRM</h2>
                    </span>
                    <span class="logo-sm">
                        <h4 class="text-white mb-0 fw-bold">CRM</h4>
                    </span>
                </span>
            </a>

            <!-- Sidebar Hover Menu Toggle Button -->
            <button class="button-sm-hover">
                <i class="ti ti-circle align-middle"></i>
            </button>

            <!-- Full Sidebar Menu Close Button -->
            <button class="button-close-fullsidebar">
                <i class="ti ti-x align-middle"></i>
            </button>

            <div data-simplebar>

                <!--- Sidenav Menu -->
                <ul class="side-nav">
                    <li class="side-nav-title">Navigation</li>

                    <li class="side-nav-item">
                        <a href="{{ route('dashboard') }}" class="side-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>

                    @if(auth()->user()->hasPermission('orders'))
                    <li class="side-nav-item">
                        <a href="{{ route('orders.index') }}" class="side-nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-shopping-cart"></i></span>
                            <span class="menu-text">Orders</span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="{{ route('orders.todays') }}" class="side-nav-link {{ request()->routeIs('orders.todays') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-calendar-check"></i></span>
                            <span class="menu-text">Today's Orders</span>
                        </a>
                    </li>

                    <li class="side-nav-item">
                        <a href="{{ route('orders.overdue') }}" class="side-nav-link {{ request()->routeIs('orders.overdue') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-alert-triangle"></i></span>
                            <span class="menu-text">Overdue Deliveries</span>
                        </a>
                    </li>
                    @if(auth()->user()->isCSR())
                    <li class="side-nav-item">
                        <a href="{{ route('orders.create') }}" class="side-nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-plus"></i></span>
                            <span class="menu-text">Add Order</span>
                        </a>
                    </li>
                    @endif
                    @endif

                    @if(auth()->user()->hasPermission('customers'))
                    <li class="side-nav-item">
                        <a href="{{ route('customers.index') }}" class="side-nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-users"></i></span>
                            <span class="menu-text">Customers</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('payments'))
                    <li class="side-nav-item">
                        <a href="{{ route('payments.index') }}" class="side-nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-credit-card"></i></span>
                            <span class="menu-text">Payment Records</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('inventory'))
                    <li class="side-nav-item">
                        <a href="{{ route('inventory.index') }}" class="side-nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-package"></i></span>
                            <span class="menu-text">Inventory</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('sms_marketing'))
                    <li class="side-nav-item">
                        <a href="{{ route('sms-marketing.index') }}" class="side-nav-link {{ request()->routeIs('sms-marketing.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-message-circle"></i></span>
                            <span class="menu-text">SMS Marketing</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('stats'))
                    <li class="side-nav-item">
                        <a href="{{ route('stats.index') }}" class="side-nav-link {{ request()->routeIs('stats.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-chart-bar"></i></span>
                            <span class="menu-text">Stats</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('staff_performance'))
                    <li class="side-nav-item">
                        <a href="{{ route('staff-performance.index') }}" class="side-nav-link {{ request()->routeIs('staff-performance.*') ? 'active' : '' }}">
                            <span class="menu-icon"><i class="ti ti-trophy"></i></span>
                            <span class="menu-text">Staff Performance</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->hasPermission('admin_panel'))
                    <li class="side-nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="side-nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" target="_blank">
                            <span class="menu-icon"><i class="ti ti-settings"></i></span>
                            <span class="menu-text">Admin Panel</span>
                        </a>
                    </li>
                    @endif

                    <li class="side-nav-item">
                        <a href="{{ route('logout') }}"
                           class="side-nav-link"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="menu-icon"><i class="ti ti-logout"></i></span>
                            <span class="menu-text">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>

            </div>
        </div>
        <!-- Sidenav Menu End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                        @yield('breadcrumbs')
                                    </ol>
                                </div>
                                <h4 class="page-title">@yield('page-title', 'Dashboard')</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Header -->
                    <div class="mobile-header d-lg-none">
                        <button class="sidenav-toggle-button btn-icon rounded-circle btn btn-light" type="button">
                            <i class="ti ti-menu-2 fs-22"></i>
                        </button>
                        <h4 class="mobile-title">CRM</h4>
                    </div>
                    <!-- end page title -->

                    @yield('content')

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <script>document.write(new Date().getFullYear())</script> © CRM App
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-end footer-links d-none d-md-block">
                                <a href="{{ route('security.dashboard') }}">Security</a>
                                <a href="#">Help</a>
                                <a href="#">Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Bootbox.js for better alerts and confirmations -->
    <script src="https://cdn.jsdelivr.net/npm/bootbox@5.5.2/bootbox.min.js"></script>

    <!-- Mobile Navigation is handled by the original template's app.js -->

    @stack('scripts')
</body>

</html>

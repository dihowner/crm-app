<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') - {{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A secure e-commerce system for managing orders, customers, and inventory." name="description" />
    <meta content="{{ App\Models\AppSetting::getValue('app_name', 'AfroWellness') }}" name="author" />

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
    <!-- Dynamic Theme CSS -->
    <link href="{{ url('/dynamic-theme.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Tabler Icons Fix -->
    <link href="{{ asset('assets/css/tabler-icons-fix.css') }}" rel="stylesheet" type="text/css" />
    <!-- Login Page Enhancements -->
    <link href="{{ asset('assets/css/login-enhancements.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')
</head>

<body class="{{ config('theme.default_theme', 'theme-blue') }}">
    @yield('content')

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>

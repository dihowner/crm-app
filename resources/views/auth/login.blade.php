@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
    <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <div class="card overflow-hidden text-center p-xxl-4 p-3 mb-0">
                <a href="{{ route('login') }}" class="auth-brand mb-4">
                    <img src="{{ asset('assets/images/crm_logo.jpg') }}" alt="CRM Logo" height="40">
                </a>

                <h4 class="fw-semibold mb-2 fs-18">Log In to your account</h4>


                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ti ti-alert-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="text-start">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="Enter your email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="position-relative">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Enter your password"
                                   required
                                   autocomplete="current-password">
                            <button type="button"
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3"
                                    id="togglePassword">
                                <i class="ti ti-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="remember"
                                   name="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary fw-semibold" type="submit">Log In</button>
                    </div>
                </form>


            </div>

            <div class="text-center mt-3">
                <p class="mt-auto mb-0">
                    {{ date('Y') }} © CRM App
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle icon
        if (type === 'text') {
            eyeIcon.classList.remove('ti-eye');
            eyeIcon.classList.add('ti-eye-off');
        } else {
            eyeIcon.classList.remove('ti-eye-off');
            eyeIcon.classList.add('ti-eye');
        }
    });
});
</script>
@endpush
@endsection

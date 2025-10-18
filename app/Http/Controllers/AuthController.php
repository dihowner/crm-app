<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with enhanced security
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:1'], // We'll validate strength in AuthService
            'remember' => ['boolean'],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password'))
                ->with('error', 'Please correct the errors below.');
        }

        $email = $request->email;
        $password = $request->password;

        // Attempt login with enhanced security
        if ($this->authService->attemptLogin($request, $email, $password)) {
            $user = Auth::user();

            // Check for suspicious activity
            if ($this->authService->checkSuspiciousActivity($user, $request->ip())) {
                // Log suspicious activity but don't block login
                // Admin will be notified through logs
            }

            // Check if user must change password
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'You must change your password before continuing.');
            }

            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        return back()
            ->withInput($request->except('password'))
            ->with('error', 'Invalid credentials. Please try again.');
    }

    /**
     * Handle logout with security measures
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        if (!$user->role) {
            return redirect()->route('dashboard')
                ->with('warning', 'No role assigned. Please contact administrator.');
        }

        switch ($user->role->slug) {
            case 'admin':
                return redirect()->route('dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');

            case 'csr':
                return redirect()->route('dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');

            case 'logistic_manager':
                return redirect()->route('dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '!');

            default:
                return redirect()->route('dashboard')
                    ->with('warning', 'Unknown role. Please contact administrator.');
        }
    }

    /**
     * Show password change form
     */
    public function showPasswordChangeForm()
    {
        if (!Auth::user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-password');
    }

    /**
     * Handle password change
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user->must_change_password) {
            return redirect()->route('dashboard');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'Password must be at least 12 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error', 'Please correct the errors below.');
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->with('error', 'Current password is incorrect.');
        }

        // Check if new password is different from current
        if (Hash::check($request->password, $user->password)) {
            return back()
                ->with('error', 'New password must be different from current password.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show security dashboard
     */
    public function showSecurityDashboard()
    {
        $user = Auth::user();

        $recentLogins = collect($user->login_history ?? [])->take(5);
        $failedAttempts = \App\Models\LoginAttempt::where('email', $user->email)
            ->where('status', 'failed')
            ->where('attempted_at', '>=', now()->subDays(30))
            ->count();

        return view('auth.security-dashboard', compact('user', 'recentLogins', 'failedAttempts'));
    }
}

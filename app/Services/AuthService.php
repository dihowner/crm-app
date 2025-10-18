<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutes
    const PASSWORD_MIN_LENGTH = 12;
    const PASSWORD_REQUIREMENTS = [
        'uppercase' => true,
        'lowercase' => true,
        'numbers' => true,
        'symbols' => true,
    ];

    /**
     * Attempt to authenticate a user with enhanced security
     */
    public function attemptLogin(Request $request, string $email, string $password): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Check rate limiting
        if ($this->isRateLimited($email, $ip)) {
            $this->logLoginAttempt($email, $ip, $userAgent, 'blocked', 'Rate limit exceeded');
            return false;
        }

        // Check if account is locked
        $user = User::where('email', $email)->first();
        if ($user && $this->isAccountLocked($user)) {
            $this->logLoginAttempt($email, $ip, $userAgent, 'blocked', 'Account locked');
            return false;
        }

        // Check if user is active
        if ($user && !$user->is_active) {
            $this->logLoginAttempt($email, $ip, $userAgent, 'blocked', 'Account deactivated');
            return false;
        }

        // Attempt authentication
        $credentials = ['email' => $email, 'password' => $password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Reset failed attempts
            $this->resetFailedAttempts($user);

            // Update login tracking
            $this->updateLoginTracking($user, $ip);

            // Log successful login
            $this->logLoginAttempt($email, $ip, $userAgent, 'success');

            // Regenerate session ID for security
            $request->session()->regenerate();

            return true;
        } else {
            // Increment failed attempts
            if ($user) {
                $this->incrementFailedAttempts($user);
            }

            // Log failed attempt
            $this->logLoginAttempt($email, $ip, $userAgent, 'failed', 'Invalid credentials');

            // Increment rate limiter
            RateLimiter::hit($this->getRateLimitKey($email, $ip), self::LOCKOUT_TIME);

            return false;
        }
    }

    /**
     * Check if login is rate limited
     */
    private function isRateLimited(string $email, string $ip): bool
    {
        return RateLimiter::tooManyAttempts($this->getRateLimitKey($email, $ip), self::MAX_LOGIN_ATTEMPTS);
    }

    /**
     * Get rate limit key
     */
    private function getRateLimitKey(string $email, string $ip): string
    {
        return 'login_attempts:' . Str::slug($email) . '|' . $ip;
    }

    /**
     * Check if account is locked
     */
    private function isAccountLocked(User $user): bool
    {
        return $user->locked_until && $user->locked_until->isFuture();
    }

    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts(User $user): void
    {
        $user->increment('failed_login_attempts');

        // Lock account after max attempts
        if ($user->failed_login_attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $user->update([
                'locked_until' => now()->addMinutes(15),
                'failed_login_attempts' => 0
            ]);

            Log::warning('Account locked due to failed login attempts', [
                'user_id' => $user->id,
                'email' => $user->email,
                'locked_until' => $user->locked_until
            ]);
        }
    }

    /**
     * Reset failed login attempts
     */
    private function resetFailedAttempts(User $user): void
    {
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Update login tracking
     */
    private function updateLoginTracking(User $user, string $ip): void
    {
        $loginHistory = $user->login_history ?? [];

        // Add current login to history (keep last 10)
        $loginHistory[] = [
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
            'user_agent' => request()->userAgent()
        ];

        // Keep only last 10 logins
        $loginHistory = array_slice($loginHistory, -10);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'login_history' => $loginHistory
        ]);
    }

    /**
     * Log login attempt
     */
    private function logLoginAttempt(string $email, string $ip, string $userAgent, string $status, string $reason = null): void
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'status' => $status,
            'failure_reason' => $reason,
            'attempted_at' => now()
        ]);
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Password must be at least " . self::PASSWORD_MIN_LENGTH . " characters long.";
        }

        if (self::PASSWORD_REQUIREMENTS['uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }

        if (self::PASSWORD_REQUIREMENTS['lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }

        if (self::PASSWORD_REQUIREMENTS['numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        }

        if (self::PASSWORD_REQUIREMENTS['symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }

        return $errors;
    }

    /**
     * Log user out with security measures
     */
    public function logout(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Check for suspicious login activity
     */
    public function checkSuspiciousActivity(User $user, string $ip): bool
    {
        $recentLogins = LoginAttempt::where('email', $user->email)
            ->where('status', 'success')
            ->where('attempted_at', '>=', now()->subHours(24))
            ->get();

        // Check for login from new IP
        $knownIPs = $recentLogins->pluck('ip_address')->unique();
        if (!$knownIPs->contains($ip)) {
            Log::warning('Suspicious login from new IP', [
                'user_id' => $user->id,
                'email' => $user->email,
                'new_ip' => $ip,
                'known_ips' => $knownIPs->toArray()
            ]);
            return true;
        }

        return false;
    }
}

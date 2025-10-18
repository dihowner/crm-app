<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        $user = auth()->user();

        // Check if user has the required role
        if (!$user->role || $user->role->slug !== $role) {
            // Log unauthorized access attempt
            \Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_role' => $role,
                'user_role' => $user->role?->slug,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}

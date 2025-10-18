<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        $user = auth()->user();

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            // Log unauthorized permission access attempt
            \Log::warning('Unauthorized permission access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'required_permission' => $permission,
                'user_role' => $user->role?->slug,
                'user_permissions' => $user->role?->permissions,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Access denied. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

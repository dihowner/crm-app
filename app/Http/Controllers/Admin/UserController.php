<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $query = User::with('role');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->whereHas('role', function ($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            }

            // Filter by active status
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active === 'true');
            }

            $users = $query->orderBy('name')->paginate(15);

            // Get roles for filter dropdown
            $roles = Role::orderBy('name')->get();

            return view('admin.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            // Log the error and return a simple response
            \Log::error('Admin users index error: ' . $e->getMessage());
            return view('admin.users.index', [
                'users' => collect(),
                'roles' => collect()
            ]);
        }
    }

    public function create()
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'max_orders_per_day' => 'nullable|integer|min:1|max:1000',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => $request->has('is_active'),
            'max_orders_per_day' => $request->max_orders_per_day ?? 50,
            'phone' => $request->phone,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
            'max_orders_per_day' => 'nullable|integer|min:1|max:1000',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_active' => $request->has('is_active'),
            'max_orders_per_day' => $request->max_orders_per_day ?? 50,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['password_changed_at'] = now();
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        // Prevent deleting the current user
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        // Check permissions
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        // Prevent deactivating the current user
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }
}

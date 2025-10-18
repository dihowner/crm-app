<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryAgentController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $query = Agent::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $agents = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.delivery-agents.index', compact('agents'));
    }

    public function create()
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        return view('admin.delivery-agents.create');
    }

    public function store(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            Agent::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.delivery-agents.index')
                ->with('success', 'Delivery agent created successfully.');

        } catch (\Exception $e) {
            \Log::error('Delivery agent creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create delivery agent. Please try again.']);
        }
    }

    public function show(Agent $deliveryAgent)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $deliveryAgent->load(['inventories.product', 'orders']);

        return view('admin.delivery-agents.show', compact('deliveryAgent'));
    }

    public function edit(Agent $deliveryAgent)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        return view('admin.delivery-agents.edit', compact('deliveryAgent'));
    }

    public function update(Request $request, Agent $deliveryAgent)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $deliveryAgent->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.delivery-agents.index')
                ->with('success', 'Delivery agent updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Delivery agent update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update delivery agent. Please try again.']);
        }
    }

    public function destroy(Agent $deliveryAgent)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            // Check if agent has any inventory or orders
            if ($deliveryAgent->inventories()->count() > 0 || $deliveryAgent->orders()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete delivery agent with existing inventory or orders.']);
            }

            $deliveryAgent->delete();

            return redirect()->route('admin.delivery-agents.index')
                ->with('success', 'Delivery agent deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Delivery agent deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete delivery agent. Please try again.']);
        }
    }

    public function toggleStatus(Agent $deliveryAgent)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $newStatus = $deliveryAgent->status === 'active' ? 'inactive' : 'active';
            $deliveryAgent->update(['status' => $newStatus]);

            $status = $newStatus === 'active' ? 'activated' : 'deactivated';
            return back()->with('success', "Delivery agent {$status} successfully.");

        } catch (\Exception $e) {
            \Log::error('Delivery agent status toggle error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to toggle delivery agent status. Please try again.']);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentInventoryController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $query = Inventory::with(['agent', 'product']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('agent', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Filter by agent
            if ($request->filled('agent_id')) {
                $query->where('agent_id', $request->agent_id);
            }

            // Filter by product
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by low stock
            if ($request->filled('low_stock') && $request->low_stock == 'true') {
                $query->whereRaw('quantity <= low_stock_threshold');
            }

            $inventories = $query->orderBy('updated_at', 'desc')->paginate(15);

            // Get agents and products for filter dropdowns
            $agents = Agent::orderBy('name')->get();
            $products = Product::orderBy('name')->get();

            return view('admin.agent-inventory.index', compact('inventories', 'agents', 'products'));
        } catch (\Exception $e) {
            \Log::error('Admin agent inventory index error: ' . $e->getMessage());
            return view('admin.agent-inventory.index', [
                'inventories' => collect(),
                'agents' => collect(),
                'products' => collect()
            ]);
        }
    }

    public function create()
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $agents = Agent::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.agent-inventory.create', compact('agents', 'products'));
    }

    public function store(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check if inventory already exists for this agent-product combination
            $existingInventory = Inventory::where('agent_id', $request->agent_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingInventory) {
                return back()->withErrors(['error' => 'Inventory already exists for this agent and product combination.']);
            }

            $inventory = Inventory::create([
                'agent_id' => $request->agent_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'low_stock_threshold' => $request->low_stock_threshold,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
            ]);

            // Create stock movement record
            $inventory->stockMovements()->create([
                'type' => 'in',
                'quantity' => $request->quantity,
                'reason' => 'Initial stock setup',
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.agent-inventory.index')
                ->with('success', 'Agent inventory created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Agent inventory creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create agent inventory. Please try again.']);
        }
    }

    public function edit(Inventory $agentInventory)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $agents = Agent::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.agent-inventory.edit', compact('agentInventory', 'agents', 'products'));
    }

    public function update(Request $request, Inventory $agentInventory)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $oldStock = $agentInventory->quantity;
            $newStock = $request->quantity;
            $stockDifference = $newStock - $oldStock;

            $agentInventory->update([
                'agent_id' => $request->agent_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'low_stock_threshold' => $request->low_stock_threshold,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
            ]);

            // Create stock movement record if stock changed
            if ($stockDifference != 0) {
                $agentInventory->stockMovements()->create([
                    'type' => $stockDifference > 0 ? 'in' : 'out',
                    'quantity' => abs($stockDifference),
                    'reason' => 'Manual stock adjustment',
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.agent-inventory.index')
                ->with('success', 'Agent inventory updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Agent inventory update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update agent inventory. Please try again.']);
        }
    }

    public function destroy(Inventory $agentInventory)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $agentInventory->delete();
            return redirect()->route('admin.agent-inventory.index')
                ->with('success', 'Agent inventory deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Agent inventory deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete agent inventory. Please try again.']);
        }
    }

    public function showAddStock(Inventory $inventory)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $inventory->load(['agent', 'product']);

        return view('admin.agent-inventory.add-stock', compact('inventory'));
    }

    public function addStock(Request $request, Inventory $inventory)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $oldStock = $inventory->quantity;
            $inventory->increment('quantity', $request->quantity);

            // Create stock movement record
            $inventory->stockMovements()->create([
                'type' => 'in',
                'quantity' => $request->quantity,
                'reason' => $request->reason ?? 'Manual stock addition',
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.agent-inventory.index')
                ->with('success', 'Stock added successfully. New stock level: ' . $inventory->fresh()->quantity);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add stock error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add stock. Please try again.']);
        }
    }
}

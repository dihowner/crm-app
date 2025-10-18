<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Models\Product;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockLog::with(['product', 'agent', 'createdBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('agent', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('createdBy', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by agent
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stockLogs = $query->latest()->paginate(20);

        // Get filter options
        $products = Product::orderBy('name')->get();
        $agents = Agent::orderBy('name')->get();
        $actions = ['Add Stock', 'Manual Adjustment'];

        return view('admin.stock-logs.index', compact('stockLogs', 'products', 'agents', 'actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $agents = Agent::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $actions = ['Add Stock', 'Manual Adjustment'];

        return view('admin.stock-logs.create', compact('products', 'agents', 'users', 'actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'agent_id' => 'required|exists:agents,id',
            'quantity_changed' => 'required|integer',
            'action' => 'required|string|in:Add Stock,Manual Adjustment',
            'comment' => 'nullable|string|max:1000',
            'created_by' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Create the stock log
            $stockLog = StockLog::create([
                'product_id' => $request->product_id,
                'agent_id' => $request->agent_id,
                'quantity_changed' => $request->quantity_changed,
                'action' => $request->action,
                'comment' => $request->comment,
                'created_by' => $request->created_by,
            ]);

            // Update agent inventory
            $inventory = \App\Models\Inventory::where('product_id', $request->product_id)
                ->where('agent_id', $request->agent_id)
                ->first();

            if ($inventory) {
                $inventory->increment('quantity', $request->quantity_changed);
            } else {
                // Create new inventory record if it doesn't exist
                \App\Models\Inventory::create([
                    'product_id' => $request->product_id,
                    'agent_id' => $request->agent_id,
                    'quantity' => max(0, $request->quantity_changed), // Ensure quantity is not negative
                    'low_stock_threshold' => 10,
                    'cost_price' => 0,
                    'selling_price' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-logs.index')
                ->with('success', 'Stock log created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create stock log: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockLog $stockLog)
    {
        $stockLog->load(['product', 'agent', 'createdBy']);
        return view('admin.stock-logs.show', compact('stockLog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockLog $stockLog)
    {
        $products = Product::orderBy('name')->get();
        $agents = Agent::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $actions = ['Add Stock', 'Manual Adjustment'];

        return view('admin.stock-logs.edit', compact('stockLog', 'products', 'agents', 'users', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockLog $stockLog)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'agent_id' => 'required|exists:agents,id',
            'quantity_changed' => 'required|integer',
            'action' => 'required|string|in:Add Stock,Manual Adjustment',
            'comment' => 'nullable|string|max:1000',
            'created_by' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            // Store old values for inventory adjustment
            $oldQuantity = $stockLog->quantity_changed;
            $oldProductId = $stockLog->product_id;
            $oldAgentId = $stockLog->agent_id;

            // Update the stock log
            $stockLog->update([
                'product_id' => $request->product_id,
                'agent_id' => $request->agent_id,
                'quantity_changed' => $request->quantity_changed,
                'action' => $request->action,
                'comment' => $request->comment,
                'created_by' => $request->created_by,
            ]);

            // Adjust inventory based on changes
            if ($oldProductId != $request->product_id || $oldAgentId != $request->agent_id) {
                // Revert old inventory
                $oldInventory = \App\Models\Inventory::where('product_id', $oldProductId)
                    ->where('agent_id', $oldAgentId)
                    ->first();
                if ($oldInventory) {
                    $oldInventory->decrement('quantity', $oldQuantity);
                }

                // Apply new inventory
                $newInventory = \App\Models\Inventory::where('product_id', $request->product_id)
                    ->where('agent_id', $request->agent_id)
                    ->first();
                if ($newInventory) {
                    $newInventory->increment('quantity', $request->quantity_changed);
                } else {
                    \App\Models\Inventory::create([
                        'product_id' => $request->product_id,
                        'agent_id' => $request->agent_id,
                        'quantity' => max(0, $request->quantity_changed),
                        'low_stock_threshold' => 10,
                        'cost_price' => 0,
                        'selling_price' => 0,
                    ]);
                }
            } else {
                // Same product and agent, just adjust quantity difference
                $quantityDiff = $request->quantity_changed - $oldQuantity;
                if ($quantityDiff != 0) {
                    $inventory = \App\Models\Inventory::where('product_id', $request->product_id)
                        ->where('agent_id', $request->agent_id)
                        ->first();
                    if ($inventory) {
                        $inventory->increment('quantity', $quantityDiff);
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.stock-logs.index')
                ->with('success', 'Stock log updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update stock log: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockLog $stockLog)
    {
        try {
            DB::beginTransaction();

            // Revert inventory changes
            $inventory = \App\Models\Inventory::where('product_id', $stockLog->product_id)
                ->where('agent_id', $stockLog->agent_id)
                ->first();

            if ($inventory) {
                $inventory->decrement('quantity', $stockLog->quantity_changed);
            }

            $stockLog->delete();

            DB::commit();

            return redirect()->route('admin.stock-logs.index')
                ->with('success', 'Stock log deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to delete stock log: ' . $e->getMessage());
        }
    }
}

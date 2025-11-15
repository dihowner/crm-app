<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Agent;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions
        if (!Auth::user()->isLogisticManager() && !Auth::user()->isAdmin()) {
            abort(403, 'Only Logistic Managers and Admins can access inventory.');
        }

        // Get all agents with their inventory
        $query = Agent::with(['inventories.product']);

        // Filter by agent if specified
        if ($request->filled('agent_id')) {
            $query->where('id', $request->agent_id);
        }

        // Filter by product if specified
        if ($request->filled('product_id')) {
            $query->whereHas('inventories', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter for low stock only
        if ($request->filled('low_stock_only') && $request->low_stock_only) {
            $query->whereHas('inventories', function ($q) {
                $q->whereRaw('quantity <= low_stock_threshold');
            });
        }

        $agents = $query->where('status', 'active')->get();

        // Get all agents for filter dropdown
        $allAgents = Agent::where('status', 'active')->get();

        // Get all products for filter dropdown
        $allProducts = Product::where('is_active', true)->orderBy('name')->get();

        // If product filter is applied, filter the inventories for each agent
        if ($request->filled('product_id')) {
            $agents->each(function ($agent) use ($request) {
                $agent->inventories = $agent->inventories->filter(function ($inventory) use ($request) {
                    return $inventory->product_id == $request->product_id;
                });
            });
        }

        return view('inventory.index', compact('agents', 'allAgents', 'allProducts'));
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Check permissions
        $user = Auth::user();
        if (!$user || (!$user->isLogisticManager() && !$user->isAdmin())) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        try {
            DB::beginTransaction();

            // Find or create inventory record
            $inventory = Inventory::firstOrCreate(
                [
                    'agent_id' => $request->agent_id,
                    'product_id' => $request->product_id
                ],
                [
                    'quantity' => 0,
                    'low_stock_threshold' => 10, // Default low stock threshold
                ]
            );

            // Add stock
            $inventory->increment('quantity', $request->quantity);

            // Log the stock addition
            try {
                $inventory->stockMovements()->create([
                    'type' => 'in',
                    'quantity' => $request->quantity,
                    'reason' => 'Manual stock addition',
                    'performed_by' => Auth::id(),
                    'performed_at' => now()
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the transaction
                \Log::error('Failed to create stock movement: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully!',
                'new_stock' => $inventory->fresh()->quantity
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to add stock: ' . $e->getMessage()], 500);
        }
    }

    public function getAgentOrders(Agent $agent)
    {
        // Check permissions
        if (!Auth::user()->isLogisticManager() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $orders = Order::with(['customer', 'product', 'assignedUser'])
                      ->where('agent_id', $agent->id)
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        return view('inventory.agent-orders', compact('orders', 'agent'));
    }

    public function reduceStockOnDelivery(Order $order)
    {
        // This method is called when an order status changes to 'delivered'
        if ($order->status === 'delivered' && $order->agent_id) {
            $inventory = Inventory::where('agent_id', $order->agent_id)
                                 ->where('product_id', $order->product_id)
                                 ->first();

            if ($inventory && $inventory->quantity >= $order->quantity) {
                $inventory->decrement('quantity', $order->quantity);

                // Log the stock reduction
                $inventory->stockMovements()->create([
                    'type' => 'out',
                    'quantity' => $order->quantity,
                    'reason' => 'Order delivered - ' . $order->order_number,
                    'performed_by' => $order->assigned_to,
                    'performed_at' => now()
                ]);
            }
        }
    }
}

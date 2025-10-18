<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $query = Product::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get unique categories for filter dropdown
        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:200',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except('image');
            $data['is_active'] = $request->has('is_active');

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image_url'] = Storage::url($imagePath);
            }

            Product::create($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            \Log::error('Product creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create product. Please try again.']);
        }
    }

    public function show(Product $product)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $product->load(['orders', 'inventories.agent']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:200',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except('image');
            $data['is_active'] = $request->has('is_active');

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image_url) {
                    $oldImagePath = str_replace('/storage/', '', $product->image_url);
                    Storage::disk('public')->delete($oldImagePath);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $data['image_url'] = Storage::url($imagePath);
            }

            $product->update($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Product update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update product. Please try again.']);
        }
    }

    public function destroy(Product $product)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            // Delete associated image
            if ($product->image_url) {
                $imagePath = str_replace('/storage/', '', $product->image_url);
                Storage::disk('public')->delete($imagePath);
            }

            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Product deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete product. Please try again.']);
        }
    }

    public function toggleStatus(Product $product)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $product->update(['is_active' => !$product->is_active]);

            $status = $product->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Product {$status} successfully.");

        } catch (\Exception $e) {
            \Log::error('Product status toggle error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to toggle product status. Please try again.']);
        }
    }
}

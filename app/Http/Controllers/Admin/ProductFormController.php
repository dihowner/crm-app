<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductForm;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductFormController extends Controller
{
    public function index(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $query = ProductForm::with('product');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('form_name', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $productForms = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.product-forms.index', compact('productForms'));
    }

    public function create()
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $products = Product::orderBy('name')->get();
        return view('admin.product-forms.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'form_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'redirect_url' => 'required|url',
            'button_text' => 'required|string|max:100',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Process packages data
            $packages = [];
            foreach ($request->packages as $index => $package) {
                if (!empty($package['name']) && !empty($package['price'])) {
                    $packages[] = [
                        'id' => $index + 1,
                        'name' => $package['name'],
                        'price' => $package['price'],
                        'quantity' => $package['quantity'] ?? 1,
                    ];
                }
            }

            $productForm = ProductForm::create([
                'form_name' => $request->form_name,
                'product_id' => $request->product_id,
                'redirect_url' => $request->redirect_url,
                'button_text' => $request->button_text,
                'packages' => $packages,
                'is_active' => true,
            ]);

            // Generate the form HTML
            $generatedForm = $productForm->generateFormHtml();
            $productForm->update(['generated_form' => $generatedForm]);

            return redirect()->route('admin.product-forms.index')
                ->with('success', 'Product form created successfully.');

        } catch (\Exception $e) {
            \Log::error('Product form creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create product form. Please try again.']);
        }
    }

    public function show(ProductForm $productForm)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $productForm->load('product');
        return view('admin.product-forms.show', compact('productForm'));
    }

    public function edit(ProductForm $productForm)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $products = Product::orderBy('name')->get();
        return view('admin.product-forms.edit', compact('productForm', 'products'));
    }

    public function update(Request $request, ProductForm $productForm)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        $request->validate([
            'form_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'redirect_url' => 'required|url',
            'button_text' => 'required|string|max:100',
            'packages' => 'required|array|min:1',
            'packages.*.name' => 'required|string|max:255',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // Process packages data
            $packages = [];
            foreach ($request->packages as $index => $package) {
                if (!empty($package['name']) && !empty($package['price'])) {
                    $packages[] = [
                        'id' => $index + 1,
                        'name' => $package['name'],
                        'price' => $package['price'],
                        'quantity' => $package['quantity'] ?? 1,
                    ];
                }
            }

            $productForm->update([
                'form_name' => $request->form_name,
                'product_id' => $request->product_id,
                'redirect_url' => $request->redirect_url,
                'button_text' => $request->button_text,
                'packages' => $packages,
            ]);

            // Regenerate the form HTML
            $generatedForm = $productForm->generateFormHtml();
            $productForm->update(['generated_form' => $generatedForm]);

            return redirect()->route('admin.product-forms.index')
                ->with('success', 'Product form updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Product form update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update product form. Please try again.']);
        }
    }

    public function destroy(ProductForm $productForm)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $productForm->delete();

            return redirect()->route('admin.product-forms.index')
                ->with('success', 'Product form deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Product form deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete product form. Please try again.']);
        }
    }

    public function regenerateForm(ProductForm $productForm)
    {
        // Check permissions - Super Admin only
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Super Admin can access this panel.');
        }

        try {
            $generatedForm = $productForm->generateFormHtml();
            $productForm->update(['generated_form' => $generatedForm]);

            return back()->with('success', 'Form HTML regenerated successfully.');

        } catch (\Exception $e) {
            \Log::error('Form regeneration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to regenerate form. Please try again.']);
        }
    }
}

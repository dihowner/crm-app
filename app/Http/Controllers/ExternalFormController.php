<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExternalFormController extends Controller
{
    /**
     * Handle external form submission from embedded product forms
     */
    public function handleFormSubmission(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('External form submission received', [
            'form_id' => $request->form_id,
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'package' => $request->package,
            'all_data' => $request->except(['_token'])
        ]);

        try {
            // Validate the form data
            $request->validate([
                'form_id' => 'required|exists:product_forms,id',
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'whatsapp_number' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'package' => 'required|string',
                'state' => 'required|string|max:100',
                'address' => 'required|string|max:500',
            ]);

            // Get the product form
            $productForm = ProductForm::with('product')->findOrFail($request->form_id);

            // Check if form is active
            if (!$productForm->is_active) {
                return redirect($productForm->redirect_url)
                    ->with('error', 'This form is no longer active.');
            }

            DB::beginTransaction();

            // Find or create customer
            $customer = Customer::where('name', $request->full_name)
                ->where('phone', $request->phone_number)
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $request->full_name,
                    'phone' => $request->phone_number,
                    'email' => $request->email,
                    'whatsapp_number' => $request->whatsapp_number,
                    'address' => $request->address,
                    'state' => $request->state,
                ]);
            } else {
                // Update customer info if it exists
                $customer->update([
                    'whatsapp_number' => $request->whatsapp_number ?? $customer->whatsapp_number,
                    'address' => $request->address ?? $customer->address,
                    'state' => $request->state ?? $customer->state,
                ]);
                if ($request->email && !$customer->email) {
                    $customer->update(['email' => $request->email]);
                }
            }

            // Get package details from form
            $packages = $productForm->packages ?? [];
            // Convert package ID to integer for comparison (form sends as string)
            $packageId = (int) $request->package;
            $selectedPackage = collect($packages)->firstWhere('id', $packageId);
            
            if (!$selectedPackage) {
                Log::error('Package not found', [
                    'form_id' => $request->form_id,
                    'package_id' => $packageId,
                    'available_packages' => $packages
                ]);
                DB::rollBack();
                return redirect($productForm->redirect_url)
                    ->with('error', 'Invalid package selected.');
            }

            // Calculate order total
            $quantity = (int) ($selectedPackage['quantity'] ?? 1);
            $unitPrice = (float) ($selectedPackage['price'] ?? 0); // Convert to float (price might be stored as string)
            $totalPrice = $quantity * $unitPrice;

            // Get available CSR for assignment
            $assignedCsr = $this->getAvailableCSR();

            if (!$assignedCsr) {
                // Still create the order even if no CSR is available
                Log::warning('No available CSR found when processing external form submission. Order created without assignment.');
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'product_id' => $productForm->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'status' => 'new',
                'source' => 'Website purchase',
                'assigned_to' => $assignedCsr?->id,
                'assigned_at' => $assignedCsr ? now() : null,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
            ]);

            // Send order confirmation email
            if ($customer->email) {
                try {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendOrderConfirmation($order->fresh(['customer', 'product']));
                } catch (\Exception $e) {
                    Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                }
            }

            DB::commit();

            // Redirect to the configured redirect URL
            return redirect($productForm->redirect_url)
                ->with('success', 'Your order has been received! Order #' . $order->order_number);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Form validation error: ' . json_encode($e->errors()));
            
            // Get redirect URL from form_id if possible
            $redirectUrl = 'https://domain.com';
            if ($request->form_id) {
                $form = ProductForm::find($request->form_id);
                if ($form) {
                    $redirectUrl = $form->redirect_url;
                }
            }

            return redirect($redirectUrl)
                ->with('error', 'Please fill all required fields correctly.')
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('External form submission error: ' . $e->getMessage());

            // Get redirect URL from form_id if possible
            $redirectUrl = 'https://domain.com';
            if ($request->form_id) {
                $form = ProductForm::find($request->form_id);
                if ($form) {
                    $redirectUrl = $form->redirect_url;
                }
            }

            return redirect($redirectUrl)
                ->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    /**
     * Get an available CSR for order assignment
     */
    private function getAvailableCSR()
    {
        $activeCSRs = User::whereHas('role', function($query) {
            $query->where('slug', 'csr');
        })
        ->where('is_active', true)
        ->get();

        if ($activeCSRs->isEmpty()) {
            return null;
        }

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $availableCSRs = $activeCSRs->filter(function($csr) use ($todayStart, $todayEnd) {
            $todayOrdersCount = Order::where('assigned_to', $csr->id)
                ->whereBetween('assigned_at', [$todayStart, $todayEnd])
                ->count();

            $maxOrders = $csr->max_orders_per_day ?? 50;
            return $todayOrdersCount < $maxOrders;
        });

        if ($availableCSRs->isEmpty()) {
            return null;
        }

        return $availableCSRs->random();
    }
}


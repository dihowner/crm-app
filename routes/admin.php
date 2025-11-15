<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AgentInventoryController;

// Super Admin Panel Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Admin Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Agent Inventory Management
    Route::resource('agent-inventory', AgentInventoryController::class);
    Route::get('agent-inventory/{inventory}/add-stock', [AgentInventoryController::class, 'showAddStock'])->name('agent-inventory.show-add-stock');
    Route::post('agent-inventory/{inventory}/add-stock', [AgentInventoryController::class, 'addStock'])->name('agent-inventory.add-stock');

    // Delivery Agents Management
    Route::resource('delivery-agents', \App\Http\Controllers\Admin\DeliveryAgentController::class);
    Route::post('delivery-agents/{deliveryAgent}/toggle-status', [\App\Http\Controllers\Admin\DeliveryAgentController::class, 'toggleStatus'])->name('delivery-agents.toggle-status');

    // Product Forms Management
    Route::resource('product-forms', \App\Http\Controllers\Admin\ProductFormController::class);
    Route::post('product-forms/{productForm}/regenerate', [\App\Http\Controllers\Admin\ProductFormController::class, 'regenerateForm'])->name('product-forms.regenerate');

    // Products Management
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Stock Logs Management
    Route::resource('stock-logs', \App\Http\Controllers\Admin\StockLogController::class);

    // App Settings Management
    Route::get('app-settings', [\App\Http\Controllers\Admin\AppSettingController::class, 'index'])->name('app-settings.index');
    Route::post('app-settings', [\App\Http\Controllers\Admin\AppSettingController::class, 'update'])->name('app-settings.update');
    Route::post('app-settings/test-email', [\App\Http\Controllers\Admin\AppSettingController::class, 'testEmail'])->name('app-settings.test-email');
    Route::post('app-settings/reset', [\App\Http\Controllers\Admin\AppSettingController::class, 'reset'])->name('app-settings.reset');
});

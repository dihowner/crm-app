<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SmsMarketingController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StaffPerformanceController;
use App\Http\Controllers\DynamicCssController;

// Dynamic CSS route for theme switching
Route::get('/dynamic-theme.css', [DynamicCssController::class, 'generate']);

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // 5 attempts per minute
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/password/change', [AuthController::class, 'showPasswordChangeForm'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword']);
    Route::get('/security', [AuthController::class, 'showSecurityDashboard'])->name('security.dashboard');
});

// Protected Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'security.headers'])->name('dashboard');

// Order Management Routes
Route::middleware(['auth', 'security.headers'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/todays', [OrderController::class, 'todaysOrders'])->name('orders.todays');
    Route::get('/orders/overdue', [OrderController::class, 'overdueDeliveries'])->name('orders.overdue');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/assign', [OrderController::class, 'assignOrder'])->name('orders.assign');

    // Customer Management Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/bulk-sms', [CustomerController::class, 'sendBulkSms'])->name('customers.bulk-sms');
    Route::get('/customers/bulk-sms/data', [CustomerController::class, 'getCustomersForBulkSms'])->name('customers.bulk-sms-data');

    // Payment Records Routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/order/{order}/payments', [PaymentController::class, 'getOrderPayments'])->name('payments.order-payments');

    // Inventory Management Routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/add-stock', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
    Route::get('/inventory/agent/{agent}/orders', [InventoryController::class, 'getAgentOrders'])->name('inventory.agent-orders');

    // SMS Marketing Routes
    Route::get('/sms-marketing', [SmsMarketingController::class, 'index'])->name('sms-marketing.index');
    Route::get('/sms-marketing/create', [SmsMarketingController::class, 'create'])->name('sms-marketing.create');
    Route::post('/sms-marketing', [SmsMarketingController::class, 'store'])->name('sms-marketing.store');
    Route::get('/sms-marketing/{smsRecord}', [SmsMarketingController::class, 'show'])->name('sms-marketing.show');
    Route::get('/sms-marketing/stats/campaign', [SmsMarketingController::class, 'getCampaignStats'])->name('sms-marketing.stats');

    // Stats Routes (Admin Only)
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
    Route::get('/stats/chart-data', [StatsController::class, 'getChartData'])->name('stats.chart-data');

    // Staff Performance Routes (Admin Only)
    Route::get('/staff-performance', [StaffPerformanceController::class, 'index'])->name('staff-performance.index');
    Route::get('/staff-performance/stats', [StaffPerformanceController::class, 'getStaffStats'])->name('staff-performance.stats');
});

// Include Admin Routes
require __DIR__.'/admin.php';

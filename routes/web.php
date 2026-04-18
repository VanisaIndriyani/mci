<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DocumentArchiveController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        if ($request->user()?->isManager()) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/manager/dashboard', ManagerDashboardController::class)
        ->middleware('role:manager')
        ->name('manager.dashboard');

    Route::get('/manager/download-insight', [ManagerDashboardController::class, 'downloadInsight'])
        ->middleware('role:manager')
        ->name('manager.download-insight');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('purchase-orders/ocr', [PurchaseOrderController::class, 'ocr'])->name('purchase-orders.ocr');
    Route::post('purchase-orders/ocr', [PurchaseOrderController::class, 'ocrStore'])->name('purchase-orders.ocr.store');
    Route::post('purchase-orders/{purchase_order}/complete', [PurchaseOrderController::class, 'complete'])->name('purchase-orders.complete');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::get('deliveries/ocr', [DeliveryController::class, 'ocr'])->name('deliveries.ocr');
    Route::post('deliveries/ocr', [DeliveryController::class, 'ocrStore'])->name('deliveries.ocr.store');
    Route::resource('deliveries', DeliveryController::class);

    Route::get('invoices/ocr', [InvoiceController::class, 'ocr'])->name('invoices.ocr');
    Route::post('invoices/ocr', [InvoiceController::class, 'ocrStore'])->name('invoices.ocr.store');
    Route::resource('invoices', InvoiceController::class);

    Route::get('archives', [DocumentArchiveController::class, 'index'])->name('archives.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';

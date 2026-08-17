<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware('role:Admin,Manager,Cashier')->group(function () {
        Route::get('/pos', [App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [App\Http\Controllers\PosController::class, 'store'])->name('pos.checkout');
        Route::get('/pos/history', [App\Http\Controllers\PosController::class, 'history'])->name('pos.history');
        Route::post('/pos/khqr/generate', [App\Http\Controllers\PosController::class, 'generateKhqr'])->name('pos.khqr.generate');
        Route::post('/pos/khqr/check', [App\Http\Controllers\PosController::class, 'checkBakongTransaction'])->name('pos.khqr.check');
    });

    Route::middleware('role:Admin,Manager')->group(function () {
        Route::resource('categories', App\Http\Controllers\Category\CategoryController::class);
        Route::resource('products', App\Http\Controllers\Product\ProductController::class);
        Route::resource('suppliers', App\Http\Controllers\Supplier\SupplierController::class)->except(['create', 'edit', 'show']);
        Route::resource('purchases', App\Http\Controllers\Purchase\PurchaseController::class)->except(['edit']);
        Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::patch('users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::resource('roles', App\Http\Controllers\Role\RoleController::class)->except(['create', 'show', 'edit']);
        
        // User Management
        Route::resource('users', App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);
        Route::patch('users/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');

        // Settings
        Route::get('/settings/general', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.general');
        Route::post('/settings/general', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.general.update');
        Route::get('/settings/about', [App\Http\Controllers\SettingController::class, 'about'])->name('settings.about');
        Route::get('/settings/tax', [App\Http\Controllers\SettingController::class, 'tax'])->name('settings.tax');
        Route::post('/settings/tax', [App\Http\Controllers\SettingController::class, 'updateTax'])->name('settings.tax.update');
    });
});

require __DIR__.'/auth.php';

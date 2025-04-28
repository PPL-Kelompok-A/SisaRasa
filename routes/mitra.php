<?php

use App\Http\Controllers\MitraController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', \App\Http\Middleware\MitraMiddleware::class])->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
    
    // Food Management
    Route::get('/foods', [MitraController::class, 'foods'])->name('foods.index');
    Route::get('/foods/create', [MitraController::class, 'createFood'])->name('foods.create');
    Route::post('/foods', [MitraController::class, 'storeFood'])->name('foods.store');
    Route::get('/foods/{food}/edit', [MitraController::class, 'editFood'])->name('foods.edit');
    Route::put('/foods/{food}', [MitraController::class, 'updateFood'])->name('foods.update');
    Route::delete('/foods/{food}', [MitraController::class, 'destroyFood'])->name('foods.destroy');
    
    // Flash Sale Management
    Route::get('/foods/flash-sale', [MitraController::class, 'flashSaleIndex'])->name('foods.flash-sale.index');
    Route::get('/foods/{food}/flash-sale', [MitraController::class, 'createFlashSale'])->name('foods.flash-sale.create');
    Route::post('/foods/{food}/flash-sale', [MitraController::class, 'storeFlashSale'])->name('foods.flash-sale.store');
    Route::delete('/foods/{food}/flash-sale', [MitraController::class, 'removeFlashSale'])->name('foods.flash-sale.remove');
    
    // Order Management
    Route::get('/orders', [MitraController::class, 'ordersIndex'])->name('orders.index');
    Route::patch('/orders/{order}/status', [MitraController::class, 'updateOrderStatus'])->name('orders.update-status');
    
    // Order History
    Route::get('/orders/history', [MitraController::class, 'orderHistoryIndex'])->name('orders.history.index');
    Route::get('/orders/history/{orderHistory}', [MitraController::class, 'orderHistoryShow'])->name('orders.history.show');
});


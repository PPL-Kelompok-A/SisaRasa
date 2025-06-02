<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\HistoryController;



Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Main dashboard route with role-based redirection
Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->role === 'mitra') {
        return redirect()->route('mitra.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Direct mitra dashboard route without middleware alias
Route::get('/mitra/dashboard', [MitraController::class, 'dashboard'])
    ->middleware('auth')
    ->name('mitra.dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

    // Cart
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{id}/quantity', [CartController::class, 'updateQuantity']);
    Route::post('/cart/{id}/select', [CartController::class, 'toggleSelect']);
    Route::delete('/cart/{id}', [CartController::class, 'removeItem']);
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

//history
Route::middleware(['auth'])->group(function () {
    Route::get('/riwayat/{id}', [HistoryController::class, 'show'])->name('riwayat.detail');
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat.index');
}); 

// Include additional route files
require __DIR__.'/auth.php';
require __DIR__.'/mitra.php';

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{id}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/start/{userId}', [ChatController::class, 'startChat'])->name('chat.start');
});

// Payment routes
// Route::middleware(['auth', 'verified'])->prefix('payment')->group(function () {
//     Route::get('/', [PaymentController::class, 'showPaymentPage'])->name('payment');
//     Route::get('/details/{methodId}', [PaymentController::class, 'getPaymentDetails'])->name('payment.details');
//     Route::post('/process', [PaymentController::class, 'processPayment'])->name('payment.process');
// });


Route::get('/daftarmenu/menu', function () {
    $menus = [
        [
            'name' => 'Egg vegi salad',
            'desc' => 'Description of the item',
            'price' => 'Rp.11k',
            'rating' => 5,
            'image' => 'salad.png',
            'repeat' => 3
        ],
        [
            'name' => 'Itally Pizza',
            'desc' => 'dengan saus tomat segar, mozzarella di bufala, dan basil segar',
            'price' => 'Rp.30k',
            'rating' => 5,
            'image' => 'pizza.png',
            'repeat' => 6
        ],
    ];

    return view('menu', compact('menus'));
});

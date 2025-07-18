<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UlasanController;



Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('foods.show');

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
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{id}/quantity', [CartController::class, 'updateQuantity']);
    Route::post('/cart/{id}/select', [CartController::class, 'toggleSelect']);
    Route::delete('/cart/{id}', [CartController::class, 'removeItem']);
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

//history
Route::middleware(['auth'])->group(function () {
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [HistoryController::class, 'show'])->name('riwayat.detail');
    
    // --- PERUBAHAN DI SINI ---
    // Route untuk ulasan yang lama dihapus karena URL-nya konflik dan digantikan dengan yang baru di bawah.
    // Route::get('/riwayat/{order}', [HistoryController::class, 'create'])->name('riwayat.ulasan');
});

require __DIR__.'/auth.php';
require __DIR__.'/mitra.php';

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{id}/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/start/{userId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
});


// --- PENAMBAHAN ROUTE ULASAN ANDA DI SINI ---
Route::middleware(['auth'])->group(function () {
    
    // 1. Route untuk MENAMPILKAN halaman form ulasan.
    //    URL diubah menjadi lebih unik ('/ulasan/buat') agar tidak bentrok dengan route detail riwayat.
    //    Nama 'riwayat.ulasan' tetap sama agar kode teman Anda tidak perlu diubah.
    //    Diarahkan ke UlasanController@create yang akan Anda kelola.
    Route::get('/riwayat/{order}/ulasan/buat', [UlasanController::class, 'create'])->name('riwayat.ulasan');

    // 2. Route untuk MENYIMPAN data saat form ulasan di-submit.
    Route::post('/ulasan/store', [UlasanController::class, 'store'])->name('ulasan.store');
});

// Ulasan Pembeli
    Route::get('/riwayat/{order}/ulasan/buat', [UlasanController::class, 'create'])->name('riwayat.ulasan');
    Route::post('/ulasan/store', [UlasanController::class, 'store'])->name('ulasan.store');

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

// Route untuk halaman lokasi
Route::get('/lokasi', function () {
    return view('mitra.lokasi');
})->name('lokasi');

// Payment routes
Route::middleware(['auth', 'verified'])->prefix('payment')->group(function () {
    Route::get('/', [PaymentController::class, 'showPaymentPage'])->name('payment.show');
    Route::get('/details/{methodId}', [PaymentController::class, 'getPaymentDetails'])->name('payment.details');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payment.process');
});

//notifications routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});
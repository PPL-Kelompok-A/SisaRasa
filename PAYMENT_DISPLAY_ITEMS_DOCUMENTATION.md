# Payment Page - Menampilkan Item dari Keranjang

## Masalah yang Diselesaikan

Sebelumnya, halaman payment menampilkan item secara manual/hardcoded. Sekarang payment page akan menampilkan item yang sudah di-add ke keranjang berdasarkan order yang dibuat saat checkout.

## Solusi yang Diimplementasikan

### 1. Enhanced PaymentController

**File:** `app/Http/Controllers/PaymentController.php`

**Method `showPaymentPage` yang diperbaiki:**
```php
public function showPaymentPage(Request $request)
{
    $order = null;
    $orderItems = collect();
    
    // Jika ada order_id dari checkout
    if ($request->has('order_id')) {
        $order = Order::with(['items.food', 'mitra'])->findOrFail($request->order_id);
        
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $orderItems = $order->items;
    }
    
    return view('payment.index', compact('order', 'orderItems'));
}
```

**Fitur yang ditambahkan:**
- ✅ **Menerima order_id dari checkout**
- ✅ **Load order dengan relasi items dan food**
- ✅ **Security check** - memastikan order milik user yang login
- ✅ **Pass data ke view** - order dan orderItems

### 2. Enhanced Payment View

**File:** `resources/views/payment/index.blade.php`

**Mengganti hardcoded product dengan dynamic content:**

```php
@if($order && $orderItems->count() > 0)
    <h3 class="text-xl font-semibold mb-4">Detail Pesanan</h3>
    
    {{-- Info Order --}}
    <div class="mb-4 p-3 bg-gray-50 rounded">
        <p class="text-sm text-gray-600">Order ID: <span class="font-medium">#{{ $order->id }}</span></p>
        <p class="text-sm text-gray-600">Mitra: <span class="font-medium">{{ $order->mitra->name ?? 'Unknown' }}</span></p>
        <p class="text-sm text-gray-600">Status: <span class="font-medium capitalize">{{ $order->status }}</span></p>
    </div>

    {{-- Daftar Item --}}
    <div class="space-y-3">
        @foreach($orderItems as $item)
            <div class="border rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    @if($item->food && $item->food->image)
                        <img src="{{ Storage::url($item->food->image) }}" 
                             class="w-16 h-16 object-cover rounded" 
                             alt="{{ $item->food->name }}">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-400 text-xs">No Image</span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h4 class="font-medium">{{ $item->food->name ?? 'Unknown Food' }}</h4>
                        <p class="text-sm text-gray-600">{{ $item->food->description ?? '' }}</p>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm">Qty: {{ $item->quantity }}</span>
                            <span class="font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-blue-600">
                                Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Total --}}
    <div class="mt-4 pt-3 border-t">
        <div class="flex justify-between items-center">
            <span class="text-lg font-semibold">Total Pembayaran:</span>
            <span class="text-xl font-bold text-green-600">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </span>
        </div>
    </div>
@else
    {{-- Fallback jika tidak ada order --}}
    <div class="text-center py-8">
        <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pesanan</h3>
        <p class="text-gray-600">Silakan lakukan checkout terlebih dahulu untuk melanjutkan pembayaran.</p>
        <a href="{{ route('cart.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Kembali ke Keranjang
        </a>
    </div>
@endif
```

**Fitur yang ditampilkan:**
- ✅ **Order Information** - ID, Mitra, Status
- ✅ **Item Details** - Nama, deskripsi, gambar, quantity, harga
- ✅ **Subtotal per item** - Harga × quantity
- ✅ **Total pembayaran** - Total keseluruhan
- ✅ **Fallback UI** - Jika tidak ada order
- ✅ **Responsive design** - Mobile-friendly layout

### 3. Enhanced Form Processing

**Hidden input untuk order_id:**
```php
@if($order)
    <input type="hidden" name="order_id" value="{{ $order->id }}">
@endif
```

**Updated processPayment method:**
```php
public function processPayment(Request $request)
{
    $request->validate([
        'payment_method' => 'required|string|in:DANA,BCA,ShopeePay',
        'order_id' => 'nullable|integer|exists:orders,id',
    ]);
    
    // Jika ada order_id, update status order
    if ($request->has('order_id')) {
        $order = Order::findOrFail($request->order_id);
        
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        // Update status order
        $order->status = 'processing';
        $order->save();
    }
    
    return redirect('/chatify')->with('success', 'Pembayaran berhasil diproses! Silakan chat dengan mitra untuk konfirmasi.');
}
```

### 4. Database Enhancements

**Added payment_proof column to orders:**
```php
// Migration: add_payment_proof_to_orders_table.php
$table->string('payment_proof')->nullable()->after('total_amount');
```

**Updated Order model:**
```php
protected $fillable = [
    'user_id',
    'mitra_id', 
    'status',
    'total_amount',
    'delivery_address',
    'payment_proof'  // ← DITAMBAHKAN
];
```

### 5. Enhanced Security

**Authorization checks:**
- ✅ **Order ownership verification** - Hanya owner yang bisa akses
- ✅ **Input validation** - Validasi order_id dan payment_method
- ✅ **CSRF protection** - Token CSRF di form
- ✅ **Authentication required** - Middleware auth

## Flow yang Diperbaiki

### 1. **Checkout to Payment Flow**
```
Cart → Select Items → Checkout → Order Created → Redirect to Payment with order_id
```

### 2. **Payment Page Display Flow**
```
Payment Page → Receive order_id → Load Order & Items → Display Dynamic Content
```

### 3. **Payment Processing Flow**
```
Select Payment Method → Submit Form → Update Order Status → Redirect to Chatify
```

## Hasil Perbaikan

### ✅ **Dynamic Content Display**
- Payment page menampilkan item dari order yang sebenarnya
- Tidak lagi hardcoded "Cimol Original"
- Menampilkan semua item yang di-checkout

### ✅ **Complete Order Information**
- Order ID, Mitra name, Status
- Item details dengan gambar, nama, deskripsi
- Quantity, harga per item, subtotal
- Total pembayaran yang akurat

### ✅ **Better User Experience**
- Fallback UI jika tidak ada order
- Responsive design untuk mobile
- Clear visual hierarchy
- Professional layout

### ✅ **Security & Validation**
- Authorization checks
- Input validation
- CSRF protection
- Error handling

## Cara Test Manual

1. **Login sebagai customer**
2. **Tambahkan beberapa item ke cart**
3. **Pilih item untuk checkout**
4. **Klik checkout** → Akan redirect ke payment dengan order_id
5. **Verifikasi payment page menampilkan:**
   - Order information yang benar
   - Semua item yang di-checkout
   - Quantity dan harga yang sesuai
   - Total yang akurat
6. **Pilih payment method dan submit**
7. **Verifikasi redirect ke chatify**

## Catatan Penting

- ✅ **Backward compatible** - Masih bisa akses payment tanpa order_id
- ✅ **Error handling** - Graceful fallback untuk edge cases
- ✅ **Performance optimized** - Eager loading dengan `with(['items.food', 'mitra'])`
- ✅ **Mobile responsive** - Layout yang baik di semua device

Sekarang payment page menampilkan item yang sebenarnya dari keranjang, bukan lagi hardcoded manual!

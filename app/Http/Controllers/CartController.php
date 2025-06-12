<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Food;
use App\Models\Order; // Pastikan ini diimpor
use App\Models\OrderItem; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $food = Food::findOrFail($request->food_id);

        // Tentukan mitra_id - gunakan mitra_id jika ada, jika tidak gunakan user_id sebagai fallback
        $mitraId = $food->mitra_id ?? $food->user_id;

        // Validasi untuk memastikan ada informasi mitra
        if (is_null($mitraId)) {
            return back()->with('error', 'Gagal menambahkan item: Informasi mitra untuk makanan ini tidak ditemukan.');
        }

        // Cek apakah sudah ada di cart berdasarkan food_id
        $cartItem = CartItem::where('food_id', $food->id)->first();
        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            CartItem::create([
                'food_id' => $food->id,
                'name' => $food->name,
                'desc' => $food->description ?? '',
                'price' => $food->price,
                'img' => $food->image ? Storage::url($food->image) : asset('images/default-food.png'),
                'quantity' => 1,
                'selected' => false,
                'mitra_id' => $mitraId,
            ]);
        }

        return back()->with('success', 'Berhasil ditambahkan ke keranjang!');
    }

    public function index()
    {
        $cartItems = CartItem::all();
        return view('cart.index', compact('cartItems'));
    }

    public function updateQuantity(Request $request, $id)
    {
        $item = CartItem::find($id);
        $item->quantity = max(1, $item->quantity + $request->delta);
        $item->save();

        return back();
    }

    public function toggleSelect($id)
    {
        $item = CartItem::find($id);
        $item->selected = !$item->selected;
        $item->save();

        return back();
    }

    public function removeItem($id)
    {
        CartItem::destroy($id);
        return back();
    }

    public function checkout()
    {
        $selectedItems = CartItem::where('selected', true)->get();
        $total = $selectedItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih item yang akan di-checkout!');
        }

        // Ambil mitra_id dari item pertama (jika semua item dari satu mitra)
        // Perbaikan kecil: Ambil user_id dari Auth::id() karena 'user_id' di tabel 'orders'
        $mitraId = $selectedItems->first()->mitra_id ?? null; // Tetap menggunakan ini

        // Simpan order ke database
        // Pastikan model Order dan OrderItem diimpor di bagian atas file
        $order = Order::create([ // Menggunakan Order::create()
            'user_id' => Auth::id(),
            'mitra_id' => $mitraId,
            'status' => 'pending', 
            'total_amount' => $total,
            'delivery_address' => 'alamat pengiriman', // Akan butuh inputan dari user
        ]);

        // Simpan item order
        foreach ($selectedItems as $item) {
            $subtotal = $item->price * $item->quantity;
            OrderItem::create([
                'order_id' => $order->id,
                'food_id' => $item->food_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $subtotal,
            ]);
        }

        // Create notifications for order created
        NotificationService::orderCreated($order);

        // (Opsional) Kosongkan cart setelah checkout
        CartItem::where('selected', true)->delete();

        // Redirect ke halaman payment
        return redirect()->route('payment.show', ['order_id' => $order->id]);
    }
}
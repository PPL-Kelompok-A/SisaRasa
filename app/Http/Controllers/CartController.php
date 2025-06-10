<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Food;
use App\Models\Order; // Pastikan ini diimpor
use App\Models\OrderItem; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $food = Food::findOrFail($request->food_id);

        // --- PENAMBAHAN BAGIAN ANDA UNTUK MENCEGAH ERROR mitra_id ---
        // Ini adalah validasi untuk memastikan Food yang ditambahkan memiliki mitra_id.
        // Jika tidak ada, ia akan mengembalikan error ke pengguna.
        if (is_null($food->mitra_id)) {
            return back()->with('error', 'Gagal menambahkan item: Informasi mitra untuk makanan ini tidak ditemukan.');
        }
        // --- AKHIR PENAMBAHAN BAGIAN ANDA ---

        // Cek apakah sudah ada di cart (bisa juga pakai user_id jika multi user)
        $cartItem = CartItem::where('name', $food->name)->first();
        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            CartItem::create([
                'name' => $food->name,
                'desc' => $food->description,
                'price' => $food->price,
                'img' => $food->image ? Storage::url($food->image) : asset('images/default-food.png'),
                'quantity' => 1,
                'selected' => false,
                'mitra_id' => $food->mitra_id, 
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
            OrderItem::create([ // Menggunakan OrderItem::create()
                'order_id' => $order->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                // tambahkan kolom lain jika perlu
            ]);
        }

        // (Opsional) Kosongkan cart setelah checkout
        CartItem::where('selected', true)->delete();

        // Redirect ke halaman payment
        return redirect()->route('payment', ['order_id' => $order->id]);
    }
}
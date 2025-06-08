<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $food = Food::findOrFail($request->food_id);

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

        // Proses checkout dan kirim data ke backend jika perlu
        return redirect()->route('cart.index')->with('success', 'Checkout sukses! Total: ' . number_format($total, 0, ',', '.'));
    }
}

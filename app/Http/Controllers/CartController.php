<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
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

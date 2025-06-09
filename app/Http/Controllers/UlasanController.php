<?php

namespace App\Http\Controllers;

use App\Models\Order;   // 1. Tambahkan ini untuk menggunakan model Order
use App\Models\Ulasan;  // 2. Tambahkan ini untuk menyimpan ulasan (pastikan modelnya ada)
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    /**
     * Method ini akan MENAMPILKAN halaman form ulasan.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View
     */
    public function create(Order $order) // 3. Terima variabel $order dari Route
    {
        // 4. Kirim data pesanan ($order) ke dalam view Anda.
        //    'ulasan.ulasan' berarti file ada di resources/views/ulasan/ulasan.blade.php
        return view('ulasan.ulasan', [
            'order' => $order
        ]);
    }

    /**
     * Method ini akan MENYIMPAN ulasan baru ke database.
     * Method ini harus berada DI DALAM class UlasanController.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) // 5. Method store() dipindahkan ke dalam class
    {
        // Validasi input dari form ulasan
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id', // Pastikan order_id yang dikirim valid
            'rating' => 'required|integer|min:1|max:5', // Contoh validasi untuk bintang rating
            'comment' => 'nullable|string|max:1000',    // Contoh validasi untuk komentar
            // Tambahkan validasi lain jika ada (misal: 'reasons')
        ]);

        // Buat dan simpan ulasan ke database
        // Pastikan Anda sudah membuat model `Ulasan`
        Ulasan::create([
            'user_id' => auth()->id(), // Menyimpan ID user yang sedang login
            'order_id' => $validatedData['order_id'],
            'rating' => $validatedData['rating'],
            'comment' => $validatedData['comment'],
            // tambahkan field lain jika ada
        ]);

        // Alihkan pengguna ke halaman riwayat dengan pesan sukses
        return redirect()->route('riwayat.index')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
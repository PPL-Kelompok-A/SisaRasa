<?php

namespace App\Http\Controllers;

use App\Models\Food; // Pastikan model Food Anda sudah di-import
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu dengan filter, pencarian, dan pengurutan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mulai query builder untuk model Food
        $query = Food::query();

        // 1. Fungsi "Cari makanan"
        // Akan mencari berdasarkan input dengan name="search" di frontend
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // Cari di kolom 'nama_foods' ATAU 'keterangan' (deskripsi)
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 2. Fungsi "Kategori"
        // Akan memfilter berdasarkan input dengan name="category" di frontend
        // Pastikan frontend mengirimkan nilai kategori yang valid yang ada di database Anda
        if ($request->filled('category')) {
            $categoryValue = $request->input('category');
            $query->where('category', $categoryValue);
        }

        // 3. Fungsi "Harga" (Pengurutan berdasarkan harga)
        // Akan mengurutkan berdasarkan input dengan name="sort" di frontend
        // Nilai yang diharapkan untuk 'sort' adalah 'asc' (termurah) atau 'desc' (termahal)
        if ($request->filled('sort')) {
            $sortDirection = $request->input('sort');
            if (in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                $query->orderBy('harga', strtolower($sortDirection));
            }
        } else {
            // Pengurutan default jika tidak ada parameter 'sort'
            // Misalnya, urutkan berdasarkan yang terbaru atau nama
            $query->orderBy('created_at', 'desc'); // atau 'nama_foods', 'asc'
        }

        // Ambil hasil query
        // Jika Anda ingin paginasi (halaman-halaman), gunakan paginate()
        // Contoh: $foods = $query->paginate(12); // Menampilkan 12 item per halaman
        $foods = $query->get();

        // Kirim data ke view Anda
        // Pastikan 'daftarmenu.menu' adalah path yang benar ke file Blade Anda
        // misalnya, resources/views/daftarmenu/menu.blade.php
        return view('daftarmenu.menu', compact('foods'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Food;
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
        // Memulai query builder untuk model Food
        $query = Food::query();

        // 1. Fungsi "Cari makanan"
        // Mencari berdasarkan input dengan name="search" dari frontend.
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            // Pencarian dilakukan pada kolom 'name' dan 'description'.
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 2. Fungsi "Filter Kategori"
        // Memfilter berdasarkan input dengan name="category" dari frontend.
        if ($request->filled('category')) {
            $categoryValue = $request->input('category');
            $query->where('category', $categoryValue);
        }

        // 3. Fungsi "Urutkan Harga"
        // Mengurutkan berdasarkan input dengan name="sort" dari frontend.
        // Nilai yang diharapkan adalah 'asc' (termurah) atau 'desc' (termahal).
        if ($request->filled('sort')) {
            $sortDirection = $request->input('sort');
            if (in_array(strtolower($sortDirection), ['asc', 'desc'])) {
                $query->orderBy('price', strtolower($sortDirection));
            }
        } else {
            // Pengurutan default jika tidak ada parameter 'sort'.
            // Di sini diurutkan berdasarkan data yang terbaru.
            $query->orderBy('created_at', 'desc');
        }

        // Ambil hasil query.
        // Gunakan get() untuk mengambil semua hasil.
        // Alternatif: gunakan paginate() untuk membagi hasil ke beberapa halaman.
        // Contoh: $foods = $query->paginate(12);
        $foods = $query->get();

        // Kirim data yang sudah difilter dan diurutkan ke view.
        return view('daftarmenu.menu', compact('foods'));
    }

    /**
     * Menampilkan detail satu item makanan.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $food = Food::findOrFail($id);
        return view('daftarmenu.show', compact('food'));
    }
}
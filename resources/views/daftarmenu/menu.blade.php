@extends('layouts.navbar')

@section('content')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .menu-filters {
            display: flex; /* Ini penting untuk alignment */
            gap: 16px;
            max-width: 1200px;
            margin: 32px auto 0 auto;
            padding: 0 16px;
            align-items: center; /* Agar semua item di dalamnya (select, input, tombol) sejajar secara vertikal */
        }
        .menu-filters select,
        .menu-filters input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
            min-width: 120px;
        }
        /* CSS untuk tombol Reset Filter */
        .btn-reset-filter {
            display: inline-block;
            padding: 8px 15px;
            background-color: #dc3545; /* Warna merah solid */
            color: white !important; /* Tulisan warna putih, !important untuk memastikan override jika ada style lain */
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            border: none; /* Hapus border default atau sesuaikan jika ingin border sewarna */
            /* border: 1px solid #dc3545; /* Jika ingin border sewarna dengan background */
            cursor: pointer;
            transition: background-color 0.2s;
            margin-left: auto; /* INI KUNCINYA untuk mendorong tombol ke kanan */
        }

        .btn-reset-filter:hover {
            background-color: #c82333; /* Warna merah lebih gelap saat hover */
            /* border-color: #bd2130; /* Jika menggunakan border */
            color: white !important;
        }
        /* Akhir CSS untuk tombol Reset Filter */

        /* ... sisa CSS Anda yang sudah ada ... */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            max-width: 1200px;
            margin: 32px auto 0 auto;
            padding: 0 16px 40px 16px;
        }
        .menu-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px 24px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s;
            position: relative;
        }
        .menu-card:hover {
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        .menu-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 24px;
            background: #f3f3f3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .menu-card h4 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-align: center;
        }
        .menu-card p {
            font-size: 14px;
            color: #666;
            margin: 0 0 20px 0;
            text-align: center;
            min-height: 36px;
        }
        .menu-card .icons {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }
        .menu-card .icons i {
            font-size: 18px;
            color: #444;
        }
        .menu-card .price-rating {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        .menu-card .price {
            font-size: 16px;
            font-weight: 600;
            color: #222;
        }
        .menu-card .rating {
            display: flex;
            align-items: center;
            font-size: 15px;
            font-weight: 500;
            color: #222;
        }
        .menu-card .rating .star {
            color: #FFD600;
            margin-right: 4px;
            font-size: 18px;
        }
    </style>

    <form method="GET" action="{{ route('menu.index') }}" class="menu-filters">
        {{-- Asumsi Anda memiliki route bernama 'menu.index' untuk halaman ini --}}
        {{-- Jika tidak, ganti action dengan {{ url('/menu') }} atau URL yang sesuai --}}
        <select name="category">
            <option value="">Kategori</option>
            <option value="vegetarian" {{ request('category')=='vegetarian' ? 'selected' : '' }}>Vegetarian</option>
            <option value="non-vegetarian" {{ request('category')=='non-vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
        </select>
        <select name="sort">
            <option value="">Harga</option>
            <option value="asc" {{ request('sort')=='asc' ? 'selected' : '' }}>Termurah</option>
            <option value="desc" {{ request('sort')=='desc' ? 'selected' : '' }}>Termahal</option>
        </select>
        <input type="text" name="search" placeholder="Cari menu..." value="{{ request('search') }}">
        <button type="submit" style="display:none"></button> {{-- Tombol submit filter utama (bisa disembunyikan) --}}

        {{-- Tombol Reset Semua Filter ditempatkan di sini, di dalam form agar sejajar dan bisa didorong ke kanan --}}
        <a href="{{ route('menu.index') }}" class="btn btn-reset-filter">Reset Semua Filter</a>
        {{-- Ganti 'menu.index' dengan nama route halaman menu Anda jika berbeda --}}
    </form>

    <div class="menu-grid">
        @foreach ($foods as $makanan)
            <div class="menu-card">
                <img src="{{ $makanan->image ? Storage::url($makanan->image) : asset('images/default-food.png') }}" alt="{{ $makanan->name }}">
                <h4>{{ $makanan->name }}</h4>
                <div class="icons">
                    @if($makanan->category == 'vegetarian')
                        <i class="fa fa-leaf" title="Vegetarian"></i>
                          <!-- Tombol ikon mata -->
        <a href="{{ route('foods.show', $makanan->id) }}" title="Lihat Detail" style="background: none; border: none; cursor: pointer; padding: 5px; display:inline-block;">
            <i class="fa-solid fa-eye" style="color: #12544e; font-size: 20px;"></i>
        </a>
        <!-- Tombol ikon keranjang -->
        <button onclick="alert('Tambah ke keranjang')" style="background: none; border: none; cursor: pointer; padding: 5px;">
            <i class="fa-solid fa-cart-plus" style="color: #12544e; font-size: 20px;"></i>
        </button>
                    @else
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
                   <div class="icons">
    @if($makanan->category == 'vegetarian')
        <i class="fa fa-leaf" title="Vegetarian"></i>
          <!-- Tombol ikon mata -->
        <a href="{{ route('foods.show', $makanan->id) }}" title="Lihat Detail" style="background: none; border: none; cursor: pointer; padding: 5px; display:inline-block;">
            <i class="fa-solid fa-eye" style="color: #12544e; font-size: 20px;"></i>
        </a>
        <!-- Tombol ikon keranjang -->
        <button onclick="alert('Tambah ke keranjang')" style="background: none; border: none; cursor: pointer; padding: 5px;">
            <i class="fa-solid fa-cart-plus" style="color: #12544e; font-size: 20px;"></i>
        </button>
    @else
        <!-- Tombol ikon mata -->
        <a href="{{ route('foods.show', $makanan->id) }}" title="Lihat Detail" style="background: none; border: none; cursor: pointer; padding: 5px; display:inline-block;">
            <i class="fa-solid fa-eye" style="color: #12544e; font-size: 20px;"></i>
        </a>
        <!-- Tombol ikon keranjang -->
        <button onclick="alert('Tambah ke keranjang')" style="background: none; border: none; cursor: pointer; padding: 5px;">
            <i class="fa-solid fa-cart-plus" style="color: #12544e; font-size: 20px;"></i>
        </button>
    @endif
</div>

                        <i class="fa-solid fa-utensils" title="Non-Vegetarian/Umum"></i>
                    @endif
                </div>
                <p>{{ Str::limit($makanan->description, 60) }}</p>
                <div class="price-rating">
                    <span class="price">Rp{{ number_format($makanan->price, 0, ',', '.') }}</span>
                    <span class="rating">
                        <span class="star">&#9733;</span>
                        {{ $makanan->rating ?? 5 }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection
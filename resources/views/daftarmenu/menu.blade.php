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
            display: flex;
            gap: 16px;
            max-width: 1200px;
            margin: 32px auto 0 auto;
            padding: 0 16px;
            align-items: center;
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
        .btn-reset-filter {
            display: inline-block;
            padding: 8px 15px;
            background-color: #dc3545;
            color: white !important;
            text-align: center;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-left: auto;
        }
        .btn-reset-filter:hover {
            background-color: #c82333;
            color: white !important;
        }
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
            gap: 12px;
            margin-bottom: 12px;
            align-items: center;
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
        
        /* Style untuk tombol aksi */
        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .action-btn:hover {
            background-color: rgba(18, 84, 78, 0.1);
        }
        .action-btn i {
            color: #12544e;
            font-size: 20px;
        }
    </style>

    <form method="GET" action="{{ route('menu.index') }}" class="menu-filters">
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
        <button type="submit" style="display:none"></button>

        <a href="{{ route('menu.index') }}" class="btn btn-reset-filter">Reset Semua Filter</a>
    </form>

    <div class="menu-grid">
        @foreach ($foods as $makanan)
            <div class="menu-card">
                <img src="{{ $makanan->image ? Storage::url($makanan->image) : asset('images/default-food.png') }}" alt="{{ $makanan->name }}">
                <h4>{{ $makanan->name }}</h4>
                
                <div class="icons">
                    {{-- Icon kategori --}}
                    @if($makanan->category == 'vegetarian')
                        <i class="fa fa-leaf" title="Vegetarian"></i>
                    @else
                        <i class="fa-solid fa-utensils" title="Non-Vegetarian/Umum"></i>
                    @endif
                    
                    {{-- Tombol lihat detail --}}
                    <a href="{{ route('foods.show', $makanan->id) }}" title="Lihat Detail" class="action-btn">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    
                    {{-- Form tambah ke keranjang --}}
                    <form action="{{ route('cart.add') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="food_id" value="{{ $makanan->id }}">
                        <button type="submit" title="Tambah ke Keranjang" class="action-btn">
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>
                    </form>
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

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 12px 16px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px;">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-success').style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 12px 16px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 6px;">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-error').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endsection
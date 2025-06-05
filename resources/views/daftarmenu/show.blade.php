@extends('layouts.navbar')

@section('content')
    <div style="max-width:500px;margin:40px auto;background:#fff;padding:32px 24px;border-radius:20px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
        <img src="{{ $food->image ? Storage::url($food->image) : asset('images/default-food.png') }}" alt="{{ $food->name }}" style="width:180px;height:180px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 24px auto;">
        <h2 style="text-align:center;font-weight:700;">{{ $food->name }}</h2>
        <p style="text-align:center;color:#666;">{{ $food->description }}</p>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:24px;">
            <span style="font-size:18px;font-weight:600;">Rp{{ number_format($food->price, 0, ',', '.') }}</span>
            <span style="color:#FFD600;font-size:18px;">
                &#9733; {{ $food->rating ?? 5 }}
            </span>
        </div>
        <a href="{{ route('menu.index') }}" style="display:block;text-align:center;margin-top:32px;color:#12544e;text-decoration:underline;">Kembali ke Daftar Menu</a>
    </div>
@endsection
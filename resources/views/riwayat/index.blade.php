@extends('layouts.navbar')
@section('content')

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
        background-color: #f8f9fa;
    }
    .container {
        max-width: 960px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .order-history {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .order-card {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        background-color: white;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        transition: 0.2s;
    }
    .order-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .order-info {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }
    .pizza-img {
        width: 72px;
        height: 72px;
        border-radius: 10px;
        object-fit: cover;
    }
    .order-details h3 {
        font-size: 16px;
        margin-bottom: 5px;
        color: #111;
    }
    .order-details p {
        font-size: 13px;
        color: #666;
        text-transform: lowercase;
    }
    .status-badge {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        border: 1px solid #c8e6c9;
        text-align: center;
        min-width: 100px;
    }
    .quantity,
    .price {
        min-width: 70px;
        text-align: center;
        font-size: 14px;
        color: #444;
        font-weight: 500;
    }
    .detail-btn {
        background-color: white;
        border: 1px solid #0a5c36;
        border-radius: 25px;
        padding: 6px 16px;
        cursor: pointer;
        font-size: 13px;
        color: #0a5c36;
        text-decoration: none;
        transition: 0.2s;
    }
    .detail-btn:hover {
        background-color: #0a5c36;
        color: white;
    }
</style>

<div class="container">
    <div class="order-history">
        @foreach ($orders as $order)
        <div class="order-card">
            <div class="order-info">
                <img src="{{ $order->image }}" class="pizza-img" alt="{{ $order->name }}" />
                <div class="order-details">
                    <h3>{{ $order->name }}</h3>
                    <p>Pesanan {{ strtolower($order->status) }}</p>
                </div>
            </div>
            <div class="status-badge">{{ $order->status }}</div>
            <div class="quantity">{{ $order->quantity }}</div>
            <div class="price">Rp.{{ number_format($order->price / 1000, 0) }}k</div>
            <a href="{{ route('riwayat.detail', $order->id) }}" class="detail-btn">Lihat Detail</a>
        </div>
        @endforeach
    </div>
</div>

@endsection
@extends('layouts.navbar')
@section('content')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .order-history {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .order-card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .order-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .pizza-img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
        }
        .order-details h3 {
            font-size: 18px;
            margin-bottom: 4px;
        }
        .order-details p {
            font-size: 14px;
            color: #666;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .info-label {
            color: #666;
            font-size: 14px;
        }
        .info-value {
            font-weight: bold;
            font-size: 16px;
        }
        .status-badge {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #c8e6c9;
            max-width: fit-content;
        }
        .payment-section,
        .details-section {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .payment-method {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .payment-logo {
            display: flex;
            align-items: center;
        }
        .payment-logo img {
            height: 30px;
            object-fit: contain;
        }
        .check-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #0a5c36;
        }
        .price-details .price-row,
        .total-row,
        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .price-details .total-row {
            color: #ff6b00;
            font-weight: bold;
        }
        .copy-btn {
            color: #0a5c36;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .copy-btn:hover {
            text-decoration: underline;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .back-button, .review-button {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .back-button {
            background-color: #6c757d;
            color: white;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .review-button {
            background-color: #ff6b00;
            color: white;
        }
        .review-button:hover {
            background-color: #e55a00;
        }
        @media (max-width: 600px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            .back-button, .review-button {
                width: 100%;
                max-width: 200px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="order-history">
        <div class="order-card">
            <div class="order-info">
                <img src="{{ $order->image }}" class="pizza-img" alt="{{ $order->name }}">
                <div class="order-details">
                    <h3>{{ $order->name }}</h3>
                    <p>Pesanan {{ strtolower($order->status) }}</p>
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="status-badge">{{ $order->status }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah</span>
                <span class="info-value">{{ $order->quantity }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Harga per item</span>
                <span class="info-value">Rp.{{ number_format($order->price / 1000, 0) }}k</span>
            </div>
        </div>

        <div class="payment-section">
            <div class="section-header">
                <h3>Metode Pembayaran</h3>
            </div>
            <div class="payment-method">
                <div class="payment-logo">
                    <img src="{{ asset('img/payments/' . strtolower($order->payment_method) . '.png') }}" alt="{{ $order->payment_method }}">
                </div>
                <div class="check-circle"></div>
            </div>
        </div>

        <div class="details-section">
            <div class="section-header">
                <h3>Detail Harga</h3>
            </div>
            <div class="price-details">
                <div class="price-row">
                    <span>Harga Asli</span>
                    <span>Rp. {{ number_format($order->price) }}</span>
                </div>
                <div class="price-row">
                    <span>Biaya Layanan</span>
                    <span>Rp. 2.500</span>
                </div>
                <div class="total-row">
                    <span>Total</span>
                    <span>Rp. {{ number_format($order->price + 2500) }}</span>
                </div>
                <div class="order-info-row">
                    <span>Order ID</span>
                    <div style="display: flex; gap: 5px;">
                        <span>{{ $order->id }}</span>
                        <button class="copy-btn" onclick="navigator.clipboard.writeText('{{ $order->id }}')">Copy</button>
                    </div>
                </div>
                <div class="order-info-row">
                    <span>Waktu Pembelian</span>
                    <span>{{ $order->created_at }}</span>
                </div>
            </div>
        </div>

        <div class="action-buttons" style="margin-top: 30px;">
            <a href="{{ route('riwayat.index') }}" class="back-button">← Kembali</a>
            <a href="{{ route('riwayat.ulasan', $order->id) }}" class="review-button">⭐ Buat Ulasan</a>
        </div>
    </div>
</div>
</body>
</html>

@endsection
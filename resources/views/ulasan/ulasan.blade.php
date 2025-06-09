{{-- Di dalam ulasan.blade.php --}}
@extends('layouts.navbar')

@section('content')
<style>
    /* CSS Anda tidak perlu diubah, sudah bagus. */
    body { 
        font-family: 'Inter', sans-serif;
        background-color: #f8f8f8;
    }
    .review-page-wrapper {
        padding: 20px;
    }
    .review-container {
        background-color: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 700px;
        text-align: center;
        margin: 40px auto;
    }
    .product-item-review {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 30px;
        text-align: left;
    }
    .product-item-review img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 20px;
    }
    .product-item-review .info h5 {
        margin: 0 0 5px 0;
        font-size: 18px;
        font-weight: 600;
    }
    .product-item-review .info p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }
    .product-item-review .details {
        margin-left: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .product-item-review .details .status-completed {
        background-color: #e6fffa;
        color: #00a372;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .product-item-review .details .quantity,
    .product-item-review .details .price {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }
    .product-item-review .details .quantity {
        margin-bottom: 5px;
    }
    .review-container h3 {
        font-size: 24px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 10px;
    }
    .review-container .subtitle {
        font-size: 16px;
        color: #555;
        margin-bottom: 30px;
    }
    .star-rating {
        margin-bottom: 20px;
    }
    .star-rating .star {
        font-size: 32px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
        margin: 0 5px;
    }
    .star-rating .star.hovered {
        color: #ffc107;
    }
    .rating-text {
        font-size: 16px;
        color: #333;
        font-weight: 600;
        margin-bottom: 30px;
        min-height: 24px;
    }
    .reason-tags {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-bottom: 30px;
    }
    .reason-tag {
        padding: 8px 15px;
        border: 1px solid #ccc;
        border-radius: 20px;
        font-size: 14px;
        color: #555;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s, border-color 0.2s;
    }
    .reason-tag.selected {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }
    .reason-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        min-height: 100px;
        font-size: 14px;
        margin-bottom: 30px;
        box-sizing: border-box;
    }
    .submit-btn {
        padding: 12px 30px;
        background-color: #00695c;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .submit-btn:hover {
        background-color: #004d40;
    }
</style>

<div class="review-page-wrapper">
    {{-- ======================= MODIFIKASI DIMULAI DI SINI ======================= --}}

    {{-- 1. BUNGKUS SEMUANYA DENGAN TAG <form> --}}
    <form class="review-container" method="POST" action="{{ route('ulasan.store') }}">
        @csrf {{-- Token keamanan Laravel, wajib ada --}}

        {{-- 2. INPUT TERSEMBUNYI UNTUK MENGIRIM ID PESANAN --}}
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="product-item-review">
            {{-- 3. GANTI DATA STATIS DENGAN DATA DINAMIS DARI $order --}}
            <img src="{{ $order->image }}" alt="{{ $order->name }}">
            <div class="info">
                <h5>{{ $order->name }}</h5>
                <p>Pesanan {{ strtolower($order->status) }}</p>
            </div>
            <div class="details">
                <span class="status-completed">{{ $order->status }}</span>
                <span class="quantity">{{ $order->quantity }}</span>
                <span class="price">Rp.{{ number_format($order->price / 1000, 0) }}k</span>
            </div>
        </div>

        <h3>Rate Your Order</h3>
        <p class="subtitle">Your feedback helps us improve!</p>

        {{-- 4. TAMBAHKAN INPUT TERSEMBUNYI UNTUK MENYIMPAN NILAI RATING --}}
        <input type="hidden" name="rating" id="rating-input" value="0">

        <div class="star-rating" data-rating="0">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <div class="rating-text" id="rating-text-label">Very Delicious</div>

        <div class="reason-tags">
            {{-- Tags Anda tetap sama --}}
            <span class="reason-tag" data-value="Missing Items">Missing Items</span>
            <span class="reason-tag" data-value="Poor Quality">Poor Quality</span>
            <span class="reason-tag" data-value="Late Delivery">Late Delivery</span>
            <span class="reason-tag" data-value="Incorrect Order">Incorrect Order</span>
            <span class="reason-tag" data-value="Bad Service">Bad Service</span>
            <span class="reason-tag" data-value="Damaged Packaging">Damaged Packaging</span>
        </div>
        
        {{-- 5. TAMBAHKAN name="comment" PADA TEXTAREA --}}
        <textarea name="comment" class="reason-textarea" placeholder="Write a reason (optional)"></textarea>

        {{-- 6. UBAH BUTTON MENJADI type="submit" --}}
        <button type="submit" class="submit-btn">Submit</button>

        {{-- 7. (Opsional) Tempat untuk menyimpan input tersembunyi untuk `reasons` jika diperlukan controller --}}
        <div id="reasons-hidden-inputs"></div>

    </form> {{-- Penutup tag form --}}

    {{-- ======================= MODIFIKASI SELESAI DI SINI ======================= --}}
</div>

{{-- Font Awesome jika diperlukan untuk ikon bintang atau lainnya --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
{{-- jQuery CDN (opsional, bisa pakai Vanilla JS) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    const ratingTexts = ["Poor", "Fair", "Good", "Very Good", "Very Delicious"];

    // Fungsi updateStars tidak berubah, sudah benar
    function updateStars(rating) {
        $('.star-rating .star').each(function() {
            if ($(this).data('value') <= rating) {
                $(this).css('color', '#ffc107');
            } else {
                $(this).css('color', '#ddd');
            }
        });
    }

    // Fungsi hover bintang tidak berubah, sudah benar
    $('.star-rating .star').hover(function() { /* ... kode Anda ... */ });

    // MODIFIKASI PADA CLICK BINTANG
    $('.star-rating .star').click(function() {
        let selectedValue = $(this).data('value');
        $('.star-rating').data('rating', selectedValue);
        updateStars(selectedValue);
        $('#rating-text-label').text(ratingTexts[selectedValue - 1] || " ");
        $('.star-rating .star').removeClass('hovered');

        // ==> Tambahkan baris ini untuk mengisi nilai ke input tersembunyi
        $('#rating-input').val(selectedValue);
    });

    // MODIFIKASI PADA CLICK TAG ALASAN
    $('.reason-tag').click(function() {
        $(this).toggleClass('selected');
        let value = $(this).data('value');

        // Menambah atau menghapus input tersembunyi untuk 'reasons[]'
        if ($(this).hasClass('selected')) {
            $('#reasons-hidden-inputs').append(`<input type="hidden" name="reasons[]" value="${value}">`);
        } else {
            $(`#reasons-hidden-inputs input[value="${value}"]`).remove();
        }
    });

    // MODIFIKASI PADA CLICK TOMBOL SUBMIT
    $('form').submit(function(e) { // Kita target form-nya langsung
        let rating = $('#rating-input').val(); // Ambil nilai dari input

        if (rating == 0) {
            e.preventDefault(); // Mencegah form untuk submit
            alert('Please select a star rating!');
            return;
        }
        // Jika rating sudah diisi, form akan tersubmit secara normal ke backend
    });
});
</script>
@endsection
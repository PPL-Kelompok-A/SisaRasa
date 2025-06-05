{{-- Di dalam ulasan.blade.php --}}
@extends('layouts.navbar')

@section('content')
<style>
    /* Hapus atau modifikasi styling body ini */
    /* body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px; // Padding ini mungkin masih oke atau bisa dipindahkan ke .review-container
    } */

    /* Styling umum untuk body halaman ini, bisa Anda sesuaikan atau hapus jika sudah diatur global */
    body { /* Kita biarkan font-family dan background, margin:0 dari layout utama */
        font-family: 'Inter', sans-serif;
        background-color: #f8f8f8; /* Pastikan background ini yang Anda inginkan untuk area konten */
    }

    .review-page-wrapper { /* Tambahkan wrapper jika perlu padding keseluruhan */
        padding: 20px; /* Atau padding yang Anda inginkan untuk halaman ini */
    }

    .review-container {
        background-color: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 700px; /* Batasi lebar container ulasan */
        text-align: center;
        margin: 40px auto; /* Memberi margin atas/bawah dan auto kiri/kanan untuk centering */
    }

    /* ... (CSS Anda yang lain untuk .product-item-review, .star-rating, .reason-tags, dll. tetap sama) ... */
    /* Pastikan tidak ada styling lain yang mengganggu layout utama dari layouts.navbar */

    /* Contoh: Pastikan CSS .product-item-review dan lainnya tetap seperti sebelumnya */
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

{{-- Tambahkan div wrapper ini jika Anda ingin padding di sekitar konten ulasan --}}
<div class="review-page-wrapper">
    <div class="review-container">
        <div class="product-item-review">
            <img src="{{-- URL gambar produk Anda --}}" alt="Nama Produk">
            <div class="info">
                <h5>Italy Pizza</h5> {{-- Ganti dengan nama produk asli --}}
                <p>Pesanan selesai</p>
            </div>
            <div class="details">
                <span class="status-completed">Completed</span>
                <span class="quantity">1</span>
                <span class="price">Rp.30k</span>
            </div>
        </div>

        <h3>Rate Your Order</h3>
        <p class="subtitle">Your feedback helps us improve!</p>

        <div class="star-rating" data-rating="0">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <div class="rating-text" id="rating-text-label">Very Delicious</div>

        <div class="reason-tags">
            <span class="reason-tag" data-value="Missing Items">Missing Items</span>
            <span class="reason-tag" data-value="Poor Quality">Poor Quality</span>
            <span class="reason-tag" data-value="Late Delivery">Late Delivery</span>
            <span class="reason-tag" data-value="Incorrect Order">Incorrect Order</span>
            <span class="reason-tag" data-value="Bad Service">Bad Service</span>
            <span class="reason-tag" data-value="Damaged Packaging">Damaged Packaging</span>
        </div>

        <textarea class="reason-textarea" placeholder="Write a reason (optional)"></textarea>

        <button class="submit-btn">Submit</button>
    </div>
</div>


{{-- ... sisa kode HTML dan JavaScript Anda ... --}}
{{-- Font Awesome jika diperlukan untuk ikon bintang atau lainnya --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
{{-- jQuery CDN (opsional, bisa pakai Vanilla JS) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    const ratingTexts = ["Poor", "Fair", "Good", "Very Good", "Very Delicious"];

    function updateStars(rating) {
        $('.star-rating .star').each(function() {
            if ($(this).data('value') <= rating) {
                $(this).css('color', '#ffc107');
            } else {
                $(this).css('color', '#ddd');
            }
        });
    }

    $('.star-rating .star').hover(function() {
        let hoverValue = $(this).data('value');
        $('.star-rating .star').each(function() {
            if ($(this).data('value') <= hoverValue) {
                $(this).addClass('hovered');
            } else {
                $(this).removeClass('hovered'); // Pastikan yang setelahnya tidak ikut hover
            }
        });
        if ($('.star-rating').data('rating') == 0) {
             $('#rating-text-label').text(ratingTexts[hoverValue - 1] || " ");
        }
    }, function() {
        $('.star-rating .star').removeClass('hovered');
        let currentRating = $('.star-rating').data('rating');
        if (currentRating > 0) {
            $('#rating-text-label').text(ratingTexts[currentRating - 1] || " ");
        } else {
             // Set teks default awal jika belum ada yang diklik, atau biarkan kosong
            $('#rating-text-label').text("Very Delicious"); // Sesuai UI awal
        }
        updateStars($('.star-rating').data('rating'));
    });

    $('.star-rating .star').click(function() {
        let selectedValue = $(this).data('value');
        $('.star-rating').data('rating', selectedValue);
        updateStars(selectedValue);
        $('#rating-text-label').text(ratingTexts[selectedValue - 1] || " ");
        $('.star-rating .star').removeClass('hovered'); // Hapus kelas hover dari semua bintang setelah klik
    });

    // Untuk set default text saat halaman load (jika tidak ada rating yang dipilih)
    if ($('.star-rating').data('rating') == 0) {
        $('#rating-text-label').text("Very Delicious"); // Teks default awal
    }


    $('.reason-tag').click(function() {
        $(this).toggleClass('selected');
    });

    $('.submit-btn').click(function() {
        let rating = $('.star-rating').data('rating');
        let reasons = [];
        $('.reason-tag.selected').each(function() {
            reasons.push($(this).data('value'));
        });
        let comment = $('.reason-textarea').val();

        if (rating === 0) {
            alert('Please select a star rating!');
            return;
        }
        console.log('Rating:', rating);
        console.log('Reasons:', reasons);
        console.log('Comment:', comment);
        alert('Review Submitted! (Frontend Only)');
    });
});
</script>
@endsection
@extends('layouts.navbar')

@section('content')
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-center mb-10">Payment Page</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Detail Pesanan --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                @if($order && $orderItems->count() > 0)
                    <h3 class="text-xl font-semibold mb-4">Detail Pesanan</h3>

                    {{-- Info Order --}}
                    <div class="mb-4 p-3 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Order ID: <span class="font-medium">#{{ $order->id }}</span></p>
                        <p class="text-sm text-gray-600">Mitra: <span class="font-medium">{{ $order->mitra->name ?? 'Unknown' }}</span></p>
                        <p class="text-sm text-gray-600">Status: <span class="font-medium capitalize">{{ $order->status }}</span></p>
                    </div>

                    {{-- Daftar Item --}}
                    <div class="space-y-3">
                        @foreach($orderItems as $item)
                            <div class="border rounded-lg p-3">
                                <div class="flex items-center space-x-3">
                                    @if($item->food && $item->food->image)
                                        <img src="{{ Storage::url($item->food->image) }}"
                                             class="w-16 h-16 object-cover rounded"
                                             alt="{{ $item->food->name }}">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No Image</span>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <h4 class="font-medium">{{ $item->food->name ?? 'Unknown Food' }}</h4>
                                        <p class="text-sm text-gray-600">{{ $item->food->description ?? '' }}</p>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-sm">Qty: {{ $item->quantity }}</span>
                                            <span class="font-medium">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-medium text-blue-600">
                                                Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Total --}}
                    <div class="mt-4 pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Pembayaran:</span>
                            <span class="text-xl font-bold text-green-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @else
                    {{-- Fallback jika tidak ada order --}}
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pesanan</h3>
                        <p class="text-gray-600">Silakan lakukan checkout terlebih dahulu untuk melanjutkan pembayaran.</p>
                        <a href="{{ route('cart.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Kembali ke Keranjang
                        </a>
                    </div>
                @endif
            </div>

            {{-- Form Pembayaran --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-xl font-semibold text-center mb-6">Pilih Metode Pembayaran</h3>
                <form action="{{ route('payment.process') }}" method="POST" class="space-y-4">
                    @csrf
                    @if($order)
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                    @endif

                    <div>
                        <label class="block font-medium mb-2">Payment Method</label>

                        <label class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="payment_method" value="DANA" onchange="handleMethodChange()" required>
                            <img src="{{ asset('images/dana.png') }}" alt="DANA" class="h-6">
                            <span>DANA</span>
                        </label>

                        <label class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="payment_method" value="BCA" onchange="handleMethodChange()" required>
                            <img src="{{ asset('images/bca.png') }}" alt="BCA" class="h-6">
                            <span>BCA</span>
                        </label>

                        <label class="flex items-center space-x-3">
                            <input type="radio" name="payment_method" value="ShopeePay" onchange="handleMethodChange()" required>
                            <img src="{{ asset('images/spay.png') }}" alt="ShopeePay" class="h-6">
                            <span>ShopeePay</span>
                        </label>
                    </div>

                    {{-- QRIS Universal --}}
                    <div id="qrisSection" class="hidden text-center mt-4 transition-opacity duration-300 ease-in-out">
                        <p class="font-semibold mb-2">Scan QR dengan <span id="qrisMethod"></span></p>
                        <img id="qrisImage" src="{{ asset('images/qris_universal.jpg') }}" alt="QRIS" class="mx-auto w-48 h-48 rounded border">
                    </div>

                    {{-- Tombol Kirim Bukti Pembayaran --}}
                    <button type="submit" id="submitPaymentProof"
                        class="block w-full text-center mt-6 bg-green-500 text-white font-semibold px-6 py-2 rounded hover:bg-green-600">
                        Kirim Bukti Pembayaran
                    </button>

                    {{-- Elemen untuk menampilkan peringatan --}}
                    <p id="warningMessage" class="text-red-500 text-center mt-2 hidden">
                        Silakan pilih metode pembayaran terlebih dahulu.
                    </p>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
    // Variabel untuk melacak apakah metode pembayaran sudah dipilih
    let paymentMethodSelected = false;

    // Ambil elemen tombol dan pesan peringatan
    const submitButton = document.getElementById('submitPaymentProof');
    const warningMessage = document.getElementById('warningMessage');

    // Fungsi untuk menampilkan QR dan menandai metode pembayaran telah dipilih
    function handleMethodChange() {
        // Mendapatkan nilai radio button yang terpilih
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        const qris = document.getElementById('qrisSection');
        const qrisImg = document.getElementById('qrisImage');
        const qrisText = document.getElementById('qrisMethod');

        // Tampilkan QR dan metode
        qris.classList.remove('hidden');
        qrisImg.src = "{{ asset('images/qris_universal.jpg') }}"; // QRIS universal
        qrisText.textContent = selectedMethod; // Gunakan selectedMethod

        // Tandai bahwa metode pembayaran sudah dipilih
        paymentMethodSelected = true;
        // Sembunyikan peringatan jika sudah ada
        warningMessage.classList.add('hidden');
    }

    // Tambahkan event listener ke form untuk validasi sebelum submit
    const paymentForm = document.querySelector('form[action="{{ route('payment.process') }}"]');

    paymentForm.addEventListener('submit', function(event) {
        if (!paymentMethodSelected) {
            event.preventDefault(); // Mencegah form submit
            warningMessage.classList.remove('hidden'); // Tampilkan pesan peringatan
        } else {
            // Jika metode pembayaran sudah dipilih, form akan di-submit ke PaymentController::processPayment
            // yang akan redirect ke chat dengan mitra
            warningMessage.classList.add('hidden'); // Sembunyikan peringatan
        }
    });

    // Opsional: Untuk memastikan QRIS tersembunyi saat halaman pertama dimuat
    document.addEventListener('DOMContentLoaded', () => {
        const qris = document.getElementById('qrisSection');
        qris.classList.add('hidden');
    });
</script>
@endsection
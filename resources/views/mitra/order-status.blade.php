<x-mitra-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan #{{ $order->id }}</h2>
                            <p class="text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <div class="mb-2">
                                @if($order->status === 'pending')
                                    <span class="px-3 py-1 text-sm font-semibold text-yellow-800 bg-yellow-200 rounded-full">Pending</span>
                                @elseif($order->status === 'processing')
                                    <span class="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-200 rounded-full">Processing</span>
                                @elseif($order->status === 'preparing')
                                    <span class="px-3 py-1 text-sm font-semibold text-purple-800 bg-purple-200 rounded-full">Preparing</span>
                                @elseif($order->status === 'ready')
                                    <span class="px-3 py-1 text-sm font-semibold text-indigo-800 bg-indigo-200 rounded-full">Ready</span>
                                @elseif($order->status === 'delivered')
                                    <span class="px-3 py-1 text-sm font-semibold text-orange-800 bg-orange-200 rounded-full">Delivered</span>
                                @elseif($order->status === 'completed')
                                    <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-200 rounded-full">Completed</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="px-3 py-1 text-sm font-semibold text-red-800 bg-red-200 rounded-full">Cancelled</span>
                                @endif
                            </div>
                            <p class="text-lg font-bold text-gray-800">Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold mb-2">Informasi Customer</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong>Nama:</strong> {{ $order->user->name }}</p>
                                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                            </div>
                            <div>
                                <p><strong>Alamat Pengiriman:</strong></p>
                                <p class="text-gray-700">{{ $order->delivery_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Item Pesanan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($item->food->image)
                                                    <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}">
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->food->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    @if($order->payment_proof)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Bukti Pembayaran</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" class="max-w-md h-auto rounded-lg shadow-md">
                        </div>
                    </div>
                    @endif

                    <!-- Update Status Form -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Update Status Pesanan</h3>
                        
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('mitra.order.updateStatus', $order->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending - Menunggu Konfirmasi</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing - Sedang Diproses</option>
                                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing - Sedang Disiapkan</option>
                                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready - Siap Diambil/Dikirim</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered - Dalam Perjalanan</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed - Selesai</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled - Dibatalkan</option>
                                </select>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tambahkan catatan untuk customer...">{{ $order->notes }}</textarea>
                            </div>

                            <div class="flex space-x-4">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Update Status
                                </button>
                                <a href="{{ route('mitra.dashboard') }}" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-semibold mb-3">Quick Actions</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($order->status === 'pending')
                                <button onclick="quickUpdateStatus('processing')" class="bg-blue-500 text-white px-4 py-2 rounded text-sm hover:bg-blue-600">
                                    Mulai Proses
                                </button>
                            @endif
                            
                            @if($order->status === 'processing')
                                <button onclick="quickUpdateStatus('preparing')" class="bg-purple-500 text-white px-4 py-2 rounded text-sm hover:bg-purple-600">
                                    Mulai Siapkan
                                </button>
                            @endif
                            
                            @if($order->status === 'preparing')
                                <button onclick="quickUpdateStatus('ready')" class="bg-indigo-500 text-white px-4 py-2 rounded text-sm hover:bg-indigo-600">
                                    Siap Dikirim
                                </button>
                            @endif
                            
                            @if($order->status === 'ready')
                                <button onclick="quickUpdateStatus('delivered')" class="bg-orange-500 text-white px-4 py-2 rounded text-sm hover:bg-orange-600">
                                    Sedang Dikirim
                                </button>
                            @endif
                            
                            @if($order->status === 'delivered')
                                <button onclick="quickUpdateStatus('completed')" class="bg-green-500 text-white px-4 py-2 rounded text-sm hover:bg-green-600">
                                    Selesaikan Pesanan
                                </button>
                            @endif
                            
                            @if(!in_array($order->status, ['completed', 'cancelled']))
                                <button onclick="quickUpdateStatus('cancelled')" class="bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">
                                    Batalkan Pesanan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function quickUpdateStatus(status) {
        if (confirm('Apakah Anda yakin ingin mengubah status pesanan menjadi: ' + status + '?')) {
            fetch(`{{ route('mitra.order.quickUpdate', $order->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }
    }
    </script>
</x-mitra-layout>

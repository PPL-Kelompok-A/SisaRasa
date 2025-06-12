<x-mitra-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Kelola Pesanan</h2>
                        <div class="text-sm text-gray-600">
                            Total: {{ $orders->total() }} pesanan
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('mitra.orders') }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Semua
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'pending']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Pending
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'processing']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'processing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Processing
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'preparing']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'preparing' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Preparing
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'ready']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'ready' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Ready
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'delivered']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'delivered' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Delivered
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'completed']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Completed
                            </a>
                            <a href="{{ route('mitra.orders', ['status' => 'cancelled']) }}" 
                               class="px-4 py-2 rounded-md text-sm {{ $status === 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Cancelled
                            </a>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $order->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $order->items->count() }} item(s)
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @foreach($order->items->take(2) as $item)
                                                    {{ $item->food->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                @if($order->items->count() > 2)
                                                    ...
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status === 'pending')
                                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Pending</span>
                                            @elseif($order->status === 'processing')
                                                <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full">Processing</span>
                                            @elseif($order->status === 'preparing')
                                                <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-200 rounded-full">Preparing</span>
                                            @elseif($order->status === 'ready')
                                                <span class="px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-200 rounded-full">Ready</span>
                                            @elseif($order->status === 'delivered')
                                                <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-200 rounded-full">Delivered</span>
                                            @elseif($order->status === 'completed')
                                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Completed</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('mitra.order.show', $order->id) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Detail
                                                </a>
                                                
                                                @if(!in_array($order->status, ['completed', 'cancelled']))
                                                <div class="relative inline-block text-left">
                                                    <button onclick="toggleDropdown({{ $order->id }})" class="text-green-600 hover:text-green-900">
                                                        Quick Update
                                                    </button>
                                                    <div id="dropdown-{{ $order->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                                        <div class="py-1">
                                                            @if($order->status === 'pending')
                                                                <button onclick="quickUpdate({{ $order->id }}, 'processing')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    → Processing
                                                                </button>
                                                            @endif
                                                            @if($order->status === 'processing')
                                                                <button onclick="quickUpdate({{ $order->id }}, 'preparing')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    → Preparing
                                                                </button>
                                                            @endif
                                                            @if($order->status === 'preparing')
                                                                <button onclick="quickUpdate({{ $order->id }}, 'ready')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    → Ready
                                                                </button>
                                                            @endif
                                                            @if($order->status === 'ready')
                                                                <button onclick="quickUpdate({{ $order->id }}, 'delivered')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    → Delivered
                                                                </button>
                                                            @endif
                                                            @if($order->status === 'delivered')
                                                                <button onclick="quickUpdate({{ $order->id }}, 'completed')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    → Completed
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 text-lg">
                                @if($status === 'all')
                                    Belum ada pesanan
                                @else
                                    Tidak ada pesanan dengan status: {{ ucfirst($status) }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleDropdown(orderId) {
        const dropdown = document.getElementById(`dropdown-${orderId}`);
        // Close all other dropdowns
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
            if (d.id !== `dropdown-${orderId}`) {
                d.classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }

    function quickUpdate(orderId, status) {
        if (confirm(`Ubah status pesanan #${orderId} menjadi: ${status}?`)) {
            fetch(`/mitra/orders/${orderId}/quick-update`, {
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

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[onclick^="toggleDropdown"]')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
                d.classList.add('hidden');
            });
        }
    });
    </script>
</x-mitra-layout>

@extends('layouts.navbar')

@section('content')
<style>
    .notification-card {
        transition: all 0.2s ease-in-out;
    }
    .notification-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .status-badge {
        font-weight: 600;
        letter-spacing: 0.025em;
    }
    .product-image {
        border: 2px solid #f3f4f6;
    }
    .unread-indicator {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-2xl font-bold">Notifikasi</h2>
            @if($notifications->where('status', 'unread')->count() > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <i class="fas fa-check-double mr-2"></i>
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if($notifications->count() > 0)
            @foreach ($notifications as $notification)
                @php
                    // Get order info from notification order_id or extract from message
                    $orderId = $notification->order_id;
                    if (!$orderId) {
                        preg_match('/Pesanan #(\d+)/', $notification->message, $orderMatches);
                        $orderId = $orderMatches[1] ?? null;
                    }
                    $order = $orderId ? \App\Models\Order::with(['items.food'])->find($orderId) : null;

                    // Determine status and color based on message content
                    $status = 'pending';
                    $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    $statusText = 'Pending';

                    if (str_contains($notification->message, 'berhasil dibuat')) {
                        $status = 'pending';
                        $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        $statusText = 'Pending';
                    } elseif (str_contains($notification->message, 'sedang diproses') || str_contains($notification->message, 'berhasil dikirim')) {
                        $status = 'in_progress';
                        $statusColor = 'bg-blue-100 text-blue-800 border-blue-200';
                        $statusText = 'In Progress';
                    } elseif (str_contains($notification->message, 'selesai') || str_contains($notification->message, 'completed')) {
                        $status = 'completed';
                        $statusColor = 'bg-green-100 text-green-800 border-green-200';
                        $statusText = 'Completed';
                    } elseif (str_contains($notification->message, 'dibatalkan') || str_contains($notification->message, 'cancelled')) {
                        $status = 'cancelled';
                        $statusColor = 'bg-red-100 text-red-800 border-red-200';
                        $statusText = 'Cancelled';
                    }
                @endphp

                <div class="notification-card bg-white shadow-sm rounded-lg p-4 mb-3 border {{ $notification->status === 'unread' ? 'border-blue-200 bg-blue-50' : 'border-gray-200' }}"
                     id="notification-{{ $notification->id }}"
                     data-notification-id="{{ $notification->id }}"
                     data-status="{{ $notification->status }}">
                    @if($order && $order->items->count() > 0)
                        {{-- Order-based notification with product info --}}
                        @foreach($order->items->take(1) as $item)
                            @if(auth()->user()->role === 'mitra')
                                {{-- TAMPILAN UNTUK MITRA --}}
                                <div class="flex items-center space-x-4">
                                    {{-- Mitra Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-store text-orange-600 text-xl"></i>
                                        </div>
                                    </div>

                                    {{-- Order Info for Mitra --}}
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Pesanan {{ $item->food->name ?? 'Unknown Food' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-1">
                                            Customer: {{ $order->user->name ?? 'Unknown Customer' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mb-2">
                                            {{ Str::limit($notification->message, 80) }}
                                        </p>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if($notification->status === 'unread')
                                                <span class="unread-indicator inline-block w-2 h-2 bg-orange-500 rounded-full"></span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Status Badge --}}
                                    <div class="flex-shrink-0">
                                        <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $statusColor }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>

                                    {{-- Order Details --}}
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-lg font-semibold text-gray-900">{{ $item->quantity }}x</div>
                                        <div class="text-sm text-orange-600 font-medium">
                                            @if($item->price >= 1000)
                                                Rp.{{ number_format($item->price / 1000, 0, ',', '.') }}k
                                            @else
                                                Rp.{{ number_format($item->price, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Action Button --}}
                                    <div class="flex-shrink-0">
                                        @if($notification->status !== 'read')
                                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-orange-600 hover:text-orange-800 p-2 rounded-full hover:bg-orange-100 mark-read-btn"
                                                        id="mark-read-{{ $notification->id }}"
                                                        data-notification-id="{{ $notification->id }}">
                                                    <i class="fas fa-check text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-green-500 p-2">
                                                <i class="fas fa-check-circle text-sm"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- TAMPILAN UNTUK CUSTOMER --}}
                                <div class="flex items-center space-x-4">
                                {{-- Product Image --}}
                                <div class="flex-shrink-0">
                                    @if($item->food && $item->food->image)
                                        <img src="{{ \Storage::url($item->food->image) }}"
                                             alt="{{ $item->food->name }}"
                                             class="product-image w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="product-image w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $item->food->name ?? 'Unknown Food' }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-1">
                                        {{ $item->food->description ?? 'Pesanan selesai' }}
                                    </p>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if($notification->status === 'unread')
                                            <span class="unread-indicator inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                {{-- Quantity & Price --}}
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-lg font-semibold text-gray-900">{{ $item->quantity }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($item->price >= 1000)
                                            Rp.{{ number_format($item->price / 1000, 0, ',', '.') }}k
                                        @else
                                            Rp.{{ number_format($item->price, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <div class="flex-shrink-0">
                                    @if($notification->status !== 'read')
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-100 mark-read-btn"
                                                    id="mark-read-{{ $notification->id }}"
                                                    data-notification-id="{{ $notification->id }}">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-green-500 p-2">
                                            <i class="fas fa-check-circle text-sm"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($order->items->count() > 1)
                                <div class="mt-2 text-xs text-gray-500 text-center">
                                    +{{ $order->items->count() - 1 }} item lainnya
                                </div>
                            @endif
                            @endif
                        @endforeach
                    @else
                        {{-- General notification without order info --}}
                        <div class="flex items-center space-x-4">
                            {{-- Default Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-bell text-gray-400 text-xl"></i>
                                </div>
                            </div>

                            {{-- Notification Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900">Notifikasi</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $notification->message }}</p>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if($notification->status === 'unread')
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            {{-- Action Button --}}
                            <div class="flex-shrink-0">
                                @if($notification->status !== 'read')
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-100 mark-read-btn"
                                                id="mark-read-{{ $notification->id }}"
                                                data-notification-id="{{ $notification->id }}">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-green-500 p-2">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-bell-slash text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada notifikasi</h3>
                <p class="text-gray-600">Notifikasi akan muncul di sini ketika ada update pesanan atau pesan baru.</p>
            </div>
        @endif
    </div>
</div>
@endsection

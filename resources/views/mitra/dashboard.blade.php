<x-mitra-layout>
    <div class="space-y-6">
        <!-- Analytics Cards -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-5">
            <div class="p-3 sm:p-6 bg-white rounded-lg shadow">
                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Your Balance</dt>
                <dd class="mt-1 text-lg sm:text-3xl font-semibold text-green-600">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</dd>
            </div>
            
            <div class="p-3 sm:p-6 bg-white rounded-lg shadow">
                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Daily Sales</dt>
                <dd class="mt-1 text-lg sm:text-3xl font-semibold text-gray-900">Rp {{ number_format($dailySales, 0, ',', '.') }}</dd>
            </div>

            <div class="p-3 sm:p-6 bg-white rounded-lg shadow">
                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Weekly Sales</dt>
                <dd class="mt-1 text-lg sm:text-3xl font-semibold text-gray-900">Rp {{ number_format($weeklySales, 0, ',', '.') }}</dd>
            </div>

            <div class="p-3 sm:p-6 bg-white rounded-lg shadow">
                <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Yearly Sales</dt>
                <dd class="mt-1 text-lg sm:text-3xl font-semibold text-gray-900">Rp {{ number_format($yearlySales, 0, ',', '.') }}</dd>
            </div>
        </div>

        <!-- Flash Sale Items -->
        @if(count($flashSaleItems) > 0)
        <div class="p-6 bg-white rounded-lg shadow border-2 border-red-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <h3 class="text-lg font-medium text-red-600">Active Flash Sales</h3>
                    <span class="animate-pulse inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                </div>
                <a href="{{ route('mitra.foods.flash-sale.index') }}" class="text-sm text-secondary hover:text-secondary/80">Manage Flash Sales</a>
            </div>
            <div class="mt-4">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @foreach($flashSaleItems as $food)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                @if($food->image)
                                    <div class="flex-shrink-0">
                                        <img class="w-12 h-12 rounded-lg object-cover" src="{{ Storage::url($food->image) }}" alt="{{ $food->name }}">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $food->name }}</p>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-bold text-red-600">Rp {{ number_format($food->getCurrentPrice(), 0, ',', '.') }}</p>
                                        <p class="text-sm text-gray-500 line-through">Rp {{ number_format($food->price, 0, ',', '.') }}</p>
                                        @if($food->discount_percentage)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $food->discount_percentage }}% Off
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($food->flash_sale_ends_at)
                                        <p class="text-xs text-gray-500">Ends: {{ $food->flash_sale_ends_at->format('d M Y') }}</p>
                                    @endif
                                    <form action="{{ route('mitra.foods.flash-sale.remove', $food) }}" method="POST" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to end this flash sale?')">End Sale</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Popular Foods -->
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Popular Foods</h3>
            <div class="mt-4">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @foreach($popularFoods as $food)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                @if($food->image)
                                    <div class="flex-shrink-0">
                                        <img class="w-12 h-12 rounded-lg object-cover" src="{{ Storage::url($food->image) }}" alt="{{ $food->name }}">
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $food->name }}</p>
                                    <p class="text-sm text-gray-500">Sold: {{ $food->total_sold ?? 0 }} items</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $food->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $food->is_available ? 'Available' : 'Not Available' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            <div class="mt-4">
                <div class="flow-root">
                    <ul role="list" class="-my-5 divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">Order #{{ $order->id }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->user->name }} - Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="{{ route('mitra.orders.index') }}" class="flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        View active orders
                    </a>
                    <a href="{{ route('mitra.orders.history.index') }}" class="flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        View order history
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-mitra-layout>

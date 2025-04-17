<x-mitra-layout>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Orders</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all orders for your food items.</p>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Order ID</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Customer</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Items</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($orders as $order)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">#{{ $order->id }}</div>
                                    <div class="text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="text-gray-900">{{ $order->user->name }}</div>
                                    <div class="text-gray-500">{{ Str::limit($order->delivery_address, 50) }}</div>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                    <ul class="list-disc list-inside">
                                        @foreach($order->items as $item)
                                            <li>{{ $item->food->name }} (x{{ $item->quantity }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <form action="{{ route('mitra.orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md {{ $order->status === 'completed' ? 'text-green-700' : ($order->status === 'cancelled' ? 'text-red-700' : 'text-yellow-700') }}">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" onclick="alert('Order details: ' + '{{ $order->delivery_address }}')">View Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-mitra-layout>

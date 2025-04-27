<x-mitra-layout>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Order History</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all completed and cancelled orders.</p>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Order Number</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($orderHistories as $history)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $history->order_number }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $history->completed_at->format('M d, Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Rp {{ number_format($history->total_amount, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $history->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <button data-modal-target="order-modal-{{ $history->id }}" data-modal-toggle="order-modal-{{ $history->id }}" class="inline-flex items-center justify-center px-4 py-2 bg-secondary hover:bg-secondary/80 text-white rounded-md shadow-sm text-sm font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">No order history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $orderHistories->links() }}
    </div>

    <!-- Order Modals -->
    @foreach ($orderHistories as $history)
    <div id="order-modal-{{ $history->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Order Details #{{ $history->order_number }}
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="order-modal-{{ $history->id }}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-semibold text-gray-700">Order Number:</p>
                            <p class="text-gray-600">#{{ $history->order_number }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Date:</p>
                            <p class="text-gray-600">{{ $history->completed_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-700">Status:</p>
                            <p class="text-gray-600 {{ $history->status === 'completed' ? 'text-green-700' : 'text-red-700' }}">
                                {{ ucfirst($history->status) }}
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="font-semibold text-gray-700">Items:</p>
                        <div class="mt-2">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if(isset($history->order_items) && is_array($history->order_items) && count($history->order_items) > 0)
                                    @foreach($history->order_items as $item)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $item['food_name'] ?? 'Unknown Item' }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item['quantity'] ?? 0 }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-sm text-gray-500 text-center">Tidak ada detail item tersedia</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between">
                            <h4 class="text-base font-medium text-gray-900">Total</h4>
                            <p class="text-base font-medium text-gray-900">Rp {{ number_format($history->total_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="order-modal-{{ $history->id }}" type="button" class="text-white bg-accent hover:bg-red-500 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalButtons = document.querySelectorAll('[data-modal-toggle]');
            const closeButtons = document.querySelectorAll('[data-modal-hide]');
            
            modalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-target');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                });
            });
            
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-hide');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            });
        });
    </script>
    @endpush
</x-mitra-layout>

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
                                        <button type="button" class="text-indigo-600 hover:text-indigo-900 view-details" data-history-id="{{ $history->id }}">
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

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Details</h3>
                <button type="button" class="close-modal absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <div id="orderDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                <button type="button" class="close-modal inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View order details
            const viewButtons = document.querySelectorAll('.view-details');
            const modal = document.getElementById('orderDetailsModal');
            const closeButtons = document.querySelectorAll('.close-modal');
            const orderDetailsContent = document.getElementById('orderDetailsContent');

            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const historyId = this.getAttribute('data-history-id');
                    
                    // Fetch order details via AJAX
                    fetch(`/mitra/orders/history/${historyId}`)
                        .then(response => response.json())
                        .then(data => {
                            let content = `
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Order Number</h4>
                                    <p class="mt-1 text-sm text-gray-900">${data.order_number}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Date</h4>
                                    <p class="mt-1 text-sm text-gray-900">${data.completed_at}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <p class="mt-1 text-sm text-gray-900">${data.status}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Items</h4>
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
                                            <tbody class="bg-white divide-y divide-gray-200">`;
                            
                            data.order_items.forEach(item => {
                                const subtotal = item.quantity * item.price;
                                content += `
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">${item.food_name}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${item.quantity}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Rp ${parseFloat(item.price).toLocaleString('id-ID')}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">Rp ${subtotal.toLocaleString('id-ID')}</td>
                                    </tr>`;
                            });
                            
                            content += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <h4 class="text-base font-medium text-gray-900">Total</h4>
                                        <p class="text-base font-medium text-gray-900">Rp ${parseFloat(data.total_amount).toLocaleString('id-ID')}</p>
                                    </div>
                                </div>`;
                            
                            orderDetailsContent.innerHTML = content;
                            modal.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error fetching order details:', error);
                            orderDetailsContent.innerHTML = '<p class="text-red-500">Error loading order details. Please try again.</p>';
                            modal.classList.remove('hidden');
                        });
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
</x-mitra-layout>

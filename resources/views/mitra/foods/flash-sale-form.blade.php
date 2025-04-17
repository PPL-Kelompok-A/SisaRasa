<x-mitra-layout>
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Create Flash Sale</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Set up a flash sale discount for "{{ $food->name }}".
                </p>
                <div class="mt-4">
                    <div class="flex items-center">
                        <div class="h-16 w-16 flex-shrink-0">
                            @if($food->image)
                                <img class="h-16 w-16 rounded-lg object-cover" src="{{ Storage::url($food->image) }}" alt="">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Regular Price</p>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($food->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('mitra.foods.flash-sale.store', $food) }}" method="POST">
                @csrf
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <div>
                            <label for="discount_type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                            <div class="mt-1">
                                <select id="discount_type" name="discount_type" class="shadow-sm focus:ring-secondary focus:border-secondary block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="fixed">Fixed Price (Rp)</option>
                                    <option value="percentage">Percentage (%)</option>
                                </select>
                            </div>
                            @error('discount_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_value" class="block text-sm font-medium text-gray-700">Discount Value</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="discount-prefix">Rp</span>
                                </div>
                                <input type="number" name="discount_value" id="discount_value" class="focus:ring-secondary focus:border-secondary block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0" min="0" step="0.01" required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="discount-suffix"></span>
                                </div>
                            </div>
                            @error('discount_value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-700">Price Preview:</p>
                                <div class="mt-1">
                                    <span class="text-sm text-gray-500">Regular Price: <span class="font-medium">Rp {{ number_format($food->price, 0, ',', '.') }}</span></span>
                                </div>
                                <div id="price-preview" class="mt-1">
                                    <span class="text-sm text-red-600">Sale Price: <span class="font-bold" id="sale-price">-</span></span>
                                    <span class="text-sm text-red-600 ml-2" id="discount-percentage-display"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="flash_sale_starts_at" class="block text-sm font-medium text-gray-700">Start Date (Optional)</label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="flash_sale_starts_at" id="flash_sale_starts_at" class="shadow-sm focus:ring-secondary focus:border-secondary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('flash_sale_starts_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="flash_sale_ends_at" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="flash_sale_ends_at" id="flash_sale_ends_at" class="shadow-sm focus:ring-secondary focus:border-secondary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('flash_sale_ends_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Flash Sale Information</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>If you don't set start/end dates, the flash sale will be active immediately and indefinitely.</li>
                                            <li>You can set just a start date (sale starts on that date and continues indefinitely) or just an end date (sale starts immediately and ends on that date).</li>
                                            <li>For percentage discounts, enter a value between 1 and 100.</li>
                                            <li>For fixed price discounts, enter the final sale price (must be lower than the regular price).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-2">
                        <a href="{{ route('mitra.foods.flash-sale.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-secondary hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary">
                            Create Flash Sale
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const discountTypeSelect = document.getElementById('discount_type');
            const discountValueInput = document.getElementById('discount_value');
            const discountPrefix = document.getElementById('discount-prefix');
            const discountSuffix = document.getElementById('discount-suffix');
            const salePriceElement = document.getElementById('sale-price');
            const discountPercentageDisplay = document.getElementById('discount-percentage-display');
            
            const regularPrice = {{ $food->price }};
            
            function updateDiscountLabels() {
                if (discountTypeSelect.value === 'fixed') {
                    discountPrefix.textContent = 'Rp';
                    discountSuffix.textContent = '';
                } else {
                    discountPrefix.textContent = '';
                    discountSuffix.textContent = '%';
                }
                updatePricePreview();
            }
            
            function updatePricePreview() {
                const discountValue = parseFloat(discountValueInput.value) || 0;
                
                if (discountValue <= 0) {
                    salePriceElement.textContent = '-';
                    discountPercentageDisplay.textContent = '';
                    return;
                }
                
                let salePrice = 0;
                let percentageOff = 0;
                
                if (discountTypeSelect.value === 'fixed') {
                    salePrice = discountValue;
                    percentageOff = Math.round(((regularPrice - salePrice) / regularPrice) * 100);
                    discountPercentageDisplay.textContent = `(${percentageOff}% off)`;
                } else {
                    percentageOff = discountValue;
                    salePrice = regularPrice - (regularPrice * (percentageOff / 100));
                    discountPercentageDisplay.textContent = '';
                }
                
                // Format the sale price with thousands separator
                const formattedSalePrice = new Intl.NumberFormat('id-ID').format(salePrice);
                salePriceElement.textContent = `Rp ${formattedSalePrice}`;
            }
            
            // Set initial state
            updateDiscountLabels();
            
            // Update on change
            discountTypeSelect.addEventListener('change', updateDiscountLabels);
            discountValueInput.addEventListener('input', updatePricePreview);
        });
    </script>
</x-mitra-layout>

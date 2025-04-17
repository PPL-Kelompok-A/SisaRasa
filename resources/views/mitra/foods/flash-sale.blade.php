<x-mitra-layout>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Flash Sale Management</h1>
            <p class="mt-2 text-sm text-gray-700">Set up flash sales for food items that aren't selling well.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('mitra.foods.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300">
                Back to Foods
            </a>
        </div>
    </div>

    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Food Item</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Regular Price</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Flash Sale Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sale Price</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Duration</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($foods as $food)
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="flex items-center">
                                        @if($food->image)
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($food->image) }}" alt="">
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900">{{ $food->name }}</div>
                                            <div class="text-gray-500">{{ Str::limit($food->description, 50) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <div class="text-gray-900">Rp {{ number_format($food->price, 0, ',', '.') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if($food->isOnActiveFlashSale())
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-red-100 text-red-800">
                                            Active
                                        </span>
                                    @elseif($food->on_flash_sale)
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-yellow-100 text-yellow-800">
                                            Scheduled
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 bg-gray-100 text-gray-800">
                                            No Flash Sale
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if($food->on_flash_sale)
                                        <div class="text-red-600 font-semibold">Rp {{ number_format($food->getCurrentPrice(), 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-500">
                                            @if($food->discount_price)
                                                Fixed Price
                                            @elseif($food->discount_percentage)
                                                <span class="font-medium">{{ $food->discount_percentage }}% Off</span>
                                                <span class="line-through">Rp {{ number_format($food->price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if($food->on_flash_sale)
                                        @if($food->flash_sale_starts_at && $food->flash_sale_ends_at)
                                            <div>{{ $food->flash_sale_starts_at->format('d M Y') }} - {{ $food->flash_sale_ends_at->format('d M Y') }}</div>
                                        @elseif($food->flash_sale_starts_at)
                                            <div>From {{ $food->flash_sale_starts_at->format('d M Y') }}</div>
                                        @elseif($food->flash_sale_ends_at)
                                            <div>Until {{ $food->flash_sale_ends_at->format('d M Y') }}</div>
                                        @else
                                            <div>Ongoing</div>
                                        @endif
                                    @else
                                        <div class="text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    @if($food->on_flash_sale)
                                        <form action="{{ route('mitra.foods.flash-sale.remove', $food) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this flash sale?')">Remove Sale</button>
                                        </form>
                                    @else
                                        <a href="{{ route('mitra.foods.flash-sale.create', $food) }}" class="text-secondary hover:text-secondary/80">Set Flash Sale</a>
                                    @endif
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
        {{ $foods->links() }}
    </div>
</x-mitra-layout>

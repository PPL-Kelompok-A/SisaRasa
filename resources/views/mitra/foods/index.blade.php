<x-mitra-layout>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Foods</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all your food items available for sale.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2">
            <a href="{{ route('mitra.foods.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-secondary hover:bg-secondary/90">
                Add food
            </a>
            <a href="{{ route('mitra.foods.flash-sale.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                Flash Sale
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
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
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
                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $food->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $food->is_available ? 'Available' : 'Not Available' }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('mitra.foods.edit', $food) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('mitra.foods.destroy', $food) }}" method="POST" class="inline ml-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this food item?')">Delete</button>
                                    </form>
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

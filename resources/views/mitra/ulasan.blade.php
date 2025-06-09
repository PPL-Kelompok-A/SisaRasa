<x-mitra-layout>
    <div class="max-w-4xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Ulasan Pelanggan</h1>
                <p class="mt-2 text-sm text-gray-700">Daftar semua ulasan yang masuk untuk produk Anda.</p>
            </div>
        </div>

        @forelse ($ulasan as $review)
            <div class="bg-white shadow-sm ring-1 ring-black ring-opacity-5 rounded-lg mb-6">
                {{-- Bagian Header Kartu --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-md font-semibold text-gray-800">Pesanan #{{ $review->order_id }}</h2>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($review->order->items as $item)
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-full">{{ $item->food->name ?? 'N/A' }}</span>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 whitespace-nowrap ml-4">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Bagian Isi Kartu --}}
                <div class="p-4 sm:p-6">
                    <div class="flex items-center mb-4">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                        <span class="ml-2 text-md font-semibold text-gray-700">({{ $review->rating }}/5)</span>
                    </div>

                    @if ($review->comment)
                        <blockquote class="mt-2 p-4 bg-gray-50 border-l-4 border-gray-300">
                            <p class="text-base italic text-gray-700">"{{ $review->comment }}"</p>
                        </blockquote>
                    @endif

                    @if ($review->reasons && count($review->reasons) > 0)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-800 mb-2">Alasan yang diberikan:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($review->reasons as $reason)
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">{{ $reason }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Bagian Footer Kartu --}}
                <div class="px-4 py-3 sm:px-6 bg-gray-50 rounded-b-lg">
                    <p class="text-sm text-right text-gray-600">Diulas oleh: <span class="font-medium">{{ $review->user->name ?? 'Pelanggan' }}</span></p>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg shadow-sm ring-1 ring-black ring-opacity-5">
                <p class="text-lg font-medium text-gray-500">Belum ada ulasan yang masuk.</p>
                <p class="text-sm text-gray-400 mt-1">Saat pelanggan memberikan ulasan, ulasan tersebut akan muncul di sini.</p>
            </div>
        @endforelse

        <div class="mt-8">
            {{ $ulasan->links() }}
        </div>
    </div>
</x-mitra-layout>
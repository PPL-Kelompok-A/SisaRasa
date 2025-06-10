@extends('dashboard')

@section('content')
    <h1 class="text-xl font-bold mb-4">Daftar Chat</h1>

    <ul class="space-y-2">
        @foreach($chats as $chat)
            <li>
                <a href="{{ route('chat.show', $chat->id) }}" class="text-blue-600 hover:underline">
                    Chat dengan: {{ $chat->userOne->id === auth()->id() ? $chat->userTwo->name : $chat->userOne->name }}
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Form upload bukti pembayaran jika ada order_id --}}
    @if(request()->has('order_id'))
        <form action="{{ route('chat.sendPaymentProof') }}" method="POST" enctype="multipart/form-data" class="mt-6 border-t pt-4">
            @csrf
            <input type="hidden" name="order_id" value="{{ request('order_id') }}">

            <label class="block font-medium mb-1">Upload Bukti Pembayaran</label>
            <input type="file" name="proof_image" accept="image/*" required class="mb-2">

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Kirim Bukti Pembayaran
            </button>
        </form>
    @endif
@endsection

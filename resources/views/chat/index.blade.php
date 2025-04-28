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
@endsection

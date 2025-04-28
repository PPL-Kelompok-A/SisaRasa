<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $chats = Chat::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo'])
            ->get();

        return view('chat.index', compact('chats'));
    }

    public function show($id)
    {
        $chat = Chat::findOrFail($id);
        $chat = Chat::with('messages.sender')->findOrFail($id);
        $chat = Chat::with('messages')->findOrFail($id);
        $chats = Chat::all(); // untuk sidebar list

        if (Auth::id() !== $chat->user_one_id && Auth::id() !== $chat->user_two_id) {
            abort(403, 'Unauthorized');
        }

        $chats = Chat::where('user_one_id', Auth::id())
            ->orWhere('user_two_id', Auth::id())
            ->with(['userOne', 'userTwo'])
            ->get();
        

        return view('chat.show', compact('chats', 'chat'));
    }

    public function send(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        $chat = Chat::findOrFail($id);

        if (Auth::id() !== $chat->user_one_id && Auth::id() !== $chat->user_two_id) {
            abort(403);
        }

        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json($message);
    }

    public function startChat($userId)
    {
        $target = User::findOrFail($userId);
        $currentUser = Auth::user();

        // Validasi agar role tidak boleh sama (kecuali admin)
        if ($currentUser->role !== 'admin' && $target->role === $currentUser->role) {
            abort(403, 'Tidak bisa memulai chat dengan role yang sama.');
        }

        $ids = [$currentUser->id, $target->id];
        sort($ids); // sort untuk menghindari duplikat

        $chat = Chat::firstOrCreate([
            'user_one_id' => $ids[0],
            'user_two_id' => $ids[1],
        ]);

        return redirect()->route('chat.show', $chat->id);
    }
}

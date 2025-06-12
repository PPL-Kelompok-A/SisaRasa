<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Tampilkan semua notifikasi untuk user yg login
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    // Tandai notifikasi tertentu sebagai sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update(['status' => 'read']);

        return redirect()->back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    // Tandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    // Get unread notification count (for AJAX)
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->count();

        return response()->json(['count' => $count]);
    }
}

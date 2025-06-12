<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class EnhancedNotificationController extends Controller
{
    /**
     * Show notification center with actions
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Notification::where('user_id', Auth::id())
                            ->with('order')
                            ->orderBy('created_at', 'desc');
        
        if ($status === 'unread') {
            $query->where('status', 'unread');
        } elseif ($status === 'read') {
            $query->where('status', 'read');
        }
        
        $notifications = $query->paginate(10);
        
        // Count unread notifications
        $unreadCount = Notification::where('user_id', Auth::id())
                                 ->where('status', 'unread')
                                 ->count();
        
        return view('notifications.index', compact('notifications', 'status', 'unreadCount'));
    }
    
    /**
     * Mark notification as read and show action options
     */
    public function markAsReadWithActions($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        
        // Mark as read
        $notification->status = 'read';
        $notification->read_at = now();
        $notification->save();
        
        // Get related order if exists
        $order = null;
        if ($notification->order_id) {
            $order = Order::with(['items.food', 'user', 'mitra'])->find($notification->order_id);
        }
        
        // Determine available actions based on notification type and order status
        $actions = $this->getAvailableActions($notification, $order);
        
        return view('notifications.actions', compact('notification', 'order', 'actions'));
    }
    
    /**
     * Get available actions based on notification and order
     */
    private function getAvailableActions($notification, $order)
    {
        $actions = [];
        $userRole = Auth::user()->role;
        
        if (!$order) {
            return [
                [
                    'label' => 'Tutup',
                    'action' => 'close',
                    'class' => 'bg-gray-500 hover:bg-gray-600',
                    'icon' => 'fas fa-times'
                ]
            ];
        }
        
        // Actions for Customer
        if ($userRole === 'customer') {
            switch ($order->status) {
                case 'pending':
                    $actions[] = [
                        'label' => 'Lihat Detail Pesanan',
                        'action' => 'view_order',
                        'url' => route('orders.show', $order->id),
                        'class' => 'bg-blue-500 hover:bg-blue-600',
                        'icon' => 'fas fa-eye'
                    ];
                    $actions[] = [
                        'label' => 'Chat dengan Mitra',
                        'action' => 'chat',
                        'url' => "/chatify/{$order->mitra_id}",
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-comment'
                    ];
                    break;
                    
                case 'processing':
                case 'preparing':
                    $actions[] = [
                        'label' => 'Track Pesanan',
                        'action' => 'track_order',
                        'url' => route('orders.track', $order->id),
                        'class' => 'bg-blue-500 hover:bg-blue-600',
                        'icon' => 'fas fa-map-marker-alt'
                    ];
                    $actions[] = [
                        'label' => 'Chat dengan Mitra',
                        'action' => 'chat',
                        'url' => "/chatify/{$order->mitra_id}",
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-comment'
                    ];
                    break;
                    
                case 'ready':
                    $actions[] = [
                        'label' => 'Konfirmasi Siap Diambil',
                        'action' => 'confirm_pickup',
                        'url' => route('orders.confirm-pickup', $order->id),
                        'class' => 'bg-orange-500 hover:bg-orange-600',
                        'icon' => 'fas fa-check-circle'
                    ];
                    $actions[] = [
                        'label' => 'Chat dengan Mitra',
                        'action' => 'chat',
                        'url' => "/chatify/{$order->mitra_id}",
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-comment'
                    ];
                    break;
                    
                case 'delivered':
                    $actions[] = [
                        'label' => 'Konfirmasi Diterima',
                        'action' => 'confirm_received',
                        'url' => route('orders.confirm-received', $order->id),
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-check-double'
                    ];
                    $actions[] = [
                        'label' => 'Laporkan Masalah',
                        'action' => 'report_issue',
                        'url' => route('orders.report-issue', $order->id),
                        'class' => 'bg-red-500 hover:bg-red-600',
                        'icon' => 'fas fa-exclamation-triangle'
                    ];
                    break;
                    
                case 'completed':
                    $actions[] = [
                        'label' => 'Beri Ulasan',
                        'action' => 'review',
                        'url' => route('orders.review', $order->id),
                        'class' => 'bg-yellow-500 hover:bg-yellow-600',
                        'icon' => 'fas fa-star'
                    ];
                    $actions[] = [
                        'label' => 'Pesan Lagi',
                        'action' => 'reorder',
                        'url' => route('orders.reorder', $order->id),
                        'class' => 'bg-purple-500 hover:bg-purple-600',
                        'icon' => 'fas fa-redo'
                    ];
                    break;
            }
        }
        
        // Actions for Mitra
        if ($userRole === 'mitra') {
            switch ($order->status) {
                case 'pending':
                    $actions[] = [
                        'label' => 'Proses Pesanan',
                        'action' => 'process_order',
                        'url' => route('mitra.order.show', $order->id),
                        'class' => 'bg-blue-500 hover:bg-blue-600',
                        'icon' => 'fas fa-play'
                    ];
                    $actions[] = [
                        'label' => 'Chat dengan Customer',
                        'action' => 'chat',
                        'url' => "/chatify/{$order->user_id}",
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-comment'
                    ];
                    break;
                    
                case 'processing':
                case 'preparing':
                case 'ready':
                case 'delivered':
                    $actions[] = [
                        'label' => 'Update Status',
                        'action' => 'update_status',
                        'url' => route('mitra.order.show', $order->id),
                        'class' => 'bg-blue-500 hover:bg-blue-600',
                        'icon' => 'fas fa-edit'
                    ];
                    $actions[] = [
                        'label' => 'Chat dengan Customer',
                        'action' => 'chat',
                        'url' => "/chatify/{$order->user_id}",
                        'class' => 'bg-green-500 hover:bg-green-600',
                        'icon' => 'fas fa-comment'
                    ];
                    break;
                    
                case 'completed':
                    $actions[] = [
                        'label' => 'Lihat Ulasan',
                        'action' => 'view_review',
                        'url' => route('mitra.orders.reviews', $order->id),
                        'class' => 'bg-yellow-500 hover:bg-yellow-600',
                        'icon' => 'fas fa-star'
                    ];
                    break;
            }
        }
        
        // Always add close action
        $actions[] = [
            'label' => 'Tutup',
            'action' => 'close',
            'class' => 'bg-gray-500 hover:bg-gray-600',
            'icon' => 'fas fa-times'
        ];
        
        return $actions;
    }
    
    /**
     * Archive notification (soft delete)
     */
    public function archive($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->archived_at = now();
        $notification->save();
        
        return response()->json(['success' => true, 'message' => 'Notifikasi diarsipkan']);
    }
    
    /**
     * Delete notification permanently
     */
    public function delete($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();
        
        return response()->json(['success' => true, 'message' => 'Notifikasi dihapus']);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                   ->where('status', 'unread')
                   ->update([
                       'status' => 'read',
                       'read_at' => now()
                   ]);
        
        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca']);
    }
    
    /**
     * Get notification count for navbar
     */
    public function getCount()
    {
        $count = Notification::where('user_id', Auth::id())
                            ->where('status', 'unread')
                            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    /**
     * Snooze notification (remind later)
     */
    public function snooze(Request $request, $id)
    {
        $request->validate([
            'snooze_until' => 'required|date|after:now'
        ]);
        
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->snoozed_until = $request->snooze_until;
        $notification->save();
        
        return response()->json(['success' => true, 'message' => 'Notifikasi akan diingatkan nanti']);
    }
}

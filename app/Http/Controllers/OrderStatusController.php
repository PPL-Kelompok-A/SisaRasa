<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class OrderStatusController extends Controller
{
    /**
     * Show order details for mitra to update status
     */
    public function show($orderId)
    {
        $order = Order::with(['user', 'items.food', 'mitra'])->findOrFail($orderId);
        
        // Pastikan order milik mitra yang login
        if ($order->mitra_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        return view('mitra.order-status', compact('order'));
    }
    
    /**
     * Update order status by mitra
     */
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,preparing,ready,delivered,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);
        
        $order = Order::findOrFail($orderId);
        
        // Pastikan order milik mitra yang login
        if ($order->mitra_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        // Update order status
        $order->status = $newStatus;
        if ($request->notes) {
            $order->notes = $request->notes;
        }
        $order->save();
        
        // Create notification for customer
        $this->createStatusNotification($order, $oldStatus, $newStatus);
        
        return redirect()->back()->with('success', "Status pesanan berhasil diubah menjadi: " . ucfirst($newStatus));
    }
    
    /**
     * Quick status update via AJAX
     */
    public function quickUpdate(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,preparing,ready,delivered,completed,cancelled'
        ]);
        
        $order = Order::findOrFail($orderId);
        
        // Pastikan order milik mitra yang login
        if ($order->mitra_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        $order->status = $newStatus;
        $order->save();
        
        // Create notification for customer
        $this->createStatusNotification($order, $oldStatus, $newStatus);
        
        return response()->json([
            'success' => true,
            'message' => "Status berhasil diubah menjadi: " . ucfirst($newStatus),
            'new_status' => $newStatus,
            'status_badge' => $this->getStatusBadge($newStatus)
        ]);
    }
    
    /**
     * Create notification when status changes
     */
    private function createStatusNotification($order, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'Pesanan Anda sedang menunggu konfirmasi',
            'processing' => 'Pesanan Anda sedang diproses',
            'preparing' => 'Pesanan Anda sedang disiapkan',
            'ready' => 'Pesanan Anda sudah siap untuk diambil/dikirim',
            'delivered' => 'Pesanan Anda sedang dalam perjalanan',
            'completed' => 'Pesanan Anda telah selesai. Terima kasih!',
            'cancelled' => 'Pesanan Anda dibatalkan'
        ];
        
        $message = $statusMessages[$newStatus] ?? "Status pesanan diubah menjadi: " . ucfirst($newStatus);
        
        NotificationService::create(
            $order->user_id,
            "Pesanan #{$order->id}: " . $message,
            'unread',
            $order->id
        );
        
        // Also create notification for mitra if status is completed
        if ($newStatus === 'completed') {
            NotificationService::create(
                $order->mitra_id,
                "Pesanan #{$order->id} telah diselesaikan. Terima kasih!",
                'unread',
                $order->id
            );
        }
    }
    
    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Pending</span>',
            'processing' => '<span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full">Processing</span>',
            'preparing' => '<span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-200 rounded-full">Preparing</span>',
            'ready' => '<span class="px-2 py-1 text-xs font-semibold text-indigo-800 bg-indigo-200 rounded-full">Ready</span>',
            'delivered' => '<span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-200 rounded-full">Delivered</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Cancelled</span>'
        ];
        
        return $badges[$status] ?? '<span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">' . ucfirst($status) . '</span>';
    }
    
    /**
     * Get all orders for mitra with status filter
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Order::with(['user', 'items.food'])
                     ->where('mitra_id', Auth::id())
                     ->orderBy('created_at', 'desc');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $orders = $query->paginate(10);
        
        return view('mitra.orders', compact('orders', 'status'));
    }
}

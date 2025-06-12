<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create($userId, $message, $status = 'unread', $orderId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'status' => $status,
            'order_id' => $orderId
        ]);
    }

    /**
     * Create notification when order is created
     */
    public static function orderCreated(Order $order)
    {
        // Notification for customer
        self::create(
            $order->user_id,
            "Pesanan #{$order->id} berhasil dibuat. Total: Rp " . number_format($order->total_amount, 0, ',', '.') . ". Silakan lakukan pembayaran.",
            'unread',
            $order->id
        );

        // Notification for mitra
        self::create(
            $order->mitra_id,
            "Pesanan baru #{$order->id} diterima dari customer. Total: Rp " . number_format($order->total_amount, 0, ',', '.') . ".",
            'unread',
            $order->id
        );
    }

    /**
     * Create notification when payment is processed
     */
    public static function paymentProcessed(Order $order)
    {
        // Notification for customer
        self::create(
            $order->user_id,
            "Pembayaran untuk pesanan #{$order->id} sedang diproses. Silakan upload bukti pembayaran dan chat dengan mitra.",
            'unread',
            $order->id
        );

        // Notification for mitra
        self::create(
            $order->mitra_id,
            "Customer telah memproses pembayaran untuk pesanan #{$order->id}. Menunggu bukti pembayaran.",
            'unread',
            $order->id
        );
    }

    /**
     * Create notification when payment proof is uploaded
     */
    public static function paymentProofUploaded(Order $order)
    {
        // Notification for customer
        self::create(
            $order->user_id,
            "Bukti pembayaran untuk pesanan #{$order->id} berhasil dikirim. Menunggu konfirmasi dari mitra.",
            'unread',
            $order->id
        );

        // Notification for mitra
        self::create(
            $order->mitra_id,
            "Bukti pembayaran untuk pesanan #{$order->id} telah diterima. Silakan verifikasi pembayaran.",
            'unread',
            $order->id
        );
    }

    /**
     * Create notification when order status is updated
     */
    public static function orderStatusUpdated(Order $order, $oldStatus, $newStatus)
    {
        $statusMessages = [
            'pending' => 'menunggu pembayaran',
            'processing' => 'sedang diproses',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan'
        ];

        $oldStatusText = $statusMessages[$oldStatus] ?? $oldStatus;
        $newStatusText = $statusMessages[$newStatus] ?? $newStatus;

        // Notification for customer
        self::create(
            $order->user_id,
            "Status pesanan #{$order->id} diubah dari '{$oldStatusText}' menjadi '{$newStatusText}'."
        );

        // Notification for mitra (if status changed to completed or cancelled)
        if (in_array($newStatus, ['completed', 'cancelled'])) {
            self::create(
                $order->mitra_id,
                "Pesanan #{$order->id} telah '{$newStatusText}'."
            );
        }
    }

    /**
     * Create notification when new message is received
     */
    public static function newMessage($fromUserId, $toUserId, $message)
    {
        $fromUser = User::find($fromUserId);
        $truncatedMessage = strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message;
        
        self::create(
            $toUserId,
            "Pesan baru dari {$fromUser->name}: {$truncatedMessage}"
        );
    }

    /**
     * Create notification for order reminder
     */
    public static function orderReminder(Order $order, $type = 'payment')
    {
        if ($type === 'payment') {
            self::create(
                $order->user_id,
                "Reminder: Pesanan #{$order->id} menunggu pembayaran. Jangan sampai terlewat!"
            );
        } elseif ($type === 'delivery') {
            self::create(
                $order->user_id,
                "Pesanan #{$order->id} sedang dalam perjalanan. Estimasi tiba dalam 30 menit."
            );
        }
    }

    /**
     * Create notification for flash sale
     */
    public static function flashSaleAlert($userId, $foodName, $discountPercentage)
    {
        self::create(
            $userId,
            "🔥 Flash Sale! {$foodName} diskon {$discountPercentage}%! Buruan sebelum kehabisan!"
        );
    }

    /**
     * Create notification for order review reminder
     */
    public static function reviewReminder(Order $order)
    {
        self::create(
            $order->user_id,
            "Bagaimana pengalaman Anda dengan pesanan #{$order->id}? Berikan ulasan untuk membantu customer lain!"
        );
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['status' => 'read']);
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecent($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old notifications (older than 30 days)
     */
    public static function cleanupOldNotifications()
    {
        return Notification::where('created_at', '<', now()->subDays(30))
            ->delete();
    }
}

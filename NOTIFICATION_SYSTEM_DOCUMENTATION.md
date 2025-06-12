# Sistem Notifikasi SisaRasa - Dokumentasi Lengkap

## Overview

Sistem notifikasi yang telah diimplementasikan memberikan real-time updates kepada user tentang status pesanan, pembayaran, dan aktivitas lainnya. Sistem ini terintegrasi dengan semua fitur utama aplikasi.

## Fitur Utama

### 1. **Bell Icon dengan Badge di Navbar**
- ✅ **Real-time notification count** - Badge merah menampilkan jumlah notifikasi unread
- ✅ **Responsive design** - Tampil baik di desktop dan mobile
- ✅ **Click to navigate** - Klik bell icon langsung ke halaman notifikasi

### 2. **Automatic Notifications**
- ✅ **Order Created** - Notifikasi untuk customer dan mitra saat order dibuat
- ✅ **Payment Processed** - Notifikasi saat pembayaran diproses
- ✅ **Payment Proof Uploaded** - Notifikasi saat bukti pembayaran diupload
- ✅ **Order Status Updates** - Notifikasi saat status order berubah

### 3. **Enhanced Notification Page**
- ✅ **Visual indicators** - Unread notifications highlighted dengan warna berbeda
- ✅ **Timestamp** - Menampilkan waktu relatif (e.g., "2 minutes ago")
- ✅ **Bulk actions** - Mark all as read button
- ✅ **Individual actions** - Mark single notification as read

## Implementasi Teknis

### 1. **NotificationService Class**

**File:** `app/Services/NotificationService.php`

Service class yang menangani semua operasi notifikasi:

```php
class NotificationService
{
    // Create notification
    public static function create($userId, $message, $status = 'unread')
    
    // Order-related notifications
    public static function orderCreated(Order $order)
    public static function paymentProcessed(Order $order)
    public static function paymentProofUploaded(Order $order)
    public static function orderStatusUpdated(Order $order, $oldStatus, $newStatus)
    
    // Chat-related notifications
    public static function newMessage($fromUserId, $toUserId, $message)
    
    // Utility methods
    public static function markAsRead($notificationId, $userId)
    public static function markAllAsRead($userId)
    public static function getUnreadCount($userId)
    public static function getRecent($userId, $limit = 10)
}
```

### 2. **Enhanced Navbar with Badge**

**File:** `resources/views/layouts/navbar.blade.php`

```php
<a href="{{ route('notifications.index') }}" class="text-xl text-gray-500 hover:text-gray-700 relative">
    <i class="far fa-bell"></i>
    @auth
        @php
            $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('status', 'unread')->count();
        @endphp
        @if($unreadCount > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    @endauth
</a>
```

**Fitur Badge:**
- ✅ **Dynamic count** - Menampilkan jumlah unread notifications
- ✅ **Max display** - Menampilkan "9+" jika lebih dari 9
- ✅ **Auto hide** - Badge hilang jika tidak ada unread notifications
- ✅ **Responsive positioning** - Posisi badge yang tepat di desktop dan mobile

### 3. **Enhanced Notification Page**

**File:** `resources/views/notifications/index.blade.php`

```php
@foreach ($notifications as $notification)
    <div class="bg-white shadow-md rounded-lg p-6 mb-4 border-l-4 {{ $notification->status === 'unread' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    @if($notification->status === 'unread')
                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                    @else
                        <span class="inline-block w-2 h-2 bg-gray-300 rounded-full mr-3"></span>
                    @endif
                    <span class="text-sm text-gray-500">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-gray-800 mb-3">{{ $notification->message }}</p>
            </div>
            
            <div class="ml-4">
                @if($notification->status !== 'read')
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-check mr-1"></i>
                            Tandai Dibaca
                        </button>
                    </form>
                @else
                    <span class="text-gray-400 text-sm">
                        <i class="fas fa-check-circle mr-1"></i>
                        Sudah dibaca
                    </span>
                @endif
            </div>
        </div>
    </div>
@endforeach
```

**Fitur UI:**
- ✅ **Visual distinction** - Unread notifications dengan background biru
- ✅ **Status indicators** - Dot indicator untuk unread/read status
- ✅ **Relative timestamps** - "2 minutes ago", "1 hour ago", etc.
- ✅ **Action buttons** - Mark as read untuk individual notifications
- ✅ **Bulk actions** - Mark all as read button di header

### 4. **Controller Integration**

**Enhanced NotificationController:**

```php
class NotificationController extends Controller
{
    public function index()
    public function markAsRead($id)
    public function markAllAsRead()
    public function getUnreadCount() // For AJAX
}
```

**Integration in other controllers:**

```php
// PaymentController
NotificationService::paymentProcessed($order);

// ChatController  
NotificationService::paymentProofUploaded($order);

// CartController
NotificationService::orderCreated($order);
```

## Notification Types & Messages

### 1. **Order Notifications**

**Order Created:**
- Customer: "Pesanan #123 berhasil dibuat. Total: Rp 50.000. Silakan lakukan pembayaran."
- Mitra: "Pesanan baru #123 diterima dari customer. Total: Rp 50.000."

**Payment Processed:**
- Customer: "Pembayaran untuk pesanan #123 sedang diproses. Silakan upload bukti pembayaran dan chat dengan mitra."
- Mitra: "Customer telah memproses pembayaran untuk pesanan #123. Menunggu bukti pembayaran."

**Payment Proof Uploaded:**
- Customer: "Bukti pembayaran untuk pesanan #123 berhasil dikirim. Menunggu konfirmasi dari mitra."
- Mitra: "Bukti pembayaran untuk pesanan #123 telah diterima. Silakan verifikasi pembayaran."

### 2. **Status Update Notifications**

**Order Status Changed:**
- "Status pesanan #123 diubah dari 'menunggu pembayaran' menjadi 'sedang diproses'."

### 3. **Chat Notifications**

**New Message:**
- "Pesan baru dari John Doe: Halo, apakah pesanan saya sudah..."

## Routes

```php
// Notification routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
});
```

## Database Schema

**Notifications Table:**
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## User Experience Flow

### 1. **Complete Notification Flow:**

1. **User melakukan checkout** → Order created notifications sent
2. **User memproses payment** → Payment processed notifications sent  
3. **Bell icon menampilkan badge** → User aware ada notifikasi baru
4. **User klik bell icon** → Redirect ke halaman notifications
5. **User melihat notifikasi** → Visual distinction untuk unread
6. **User upload bukti pembayaran** → Payment proof notifications sent
7. **User mark notifications as read** → Badge count berkurang
8. **User mark all as read** → Badge hilang

### 2. **Real-time Updates:**

- ✅ **Instant badge updates** - Badge muncul segera setelah notifikasi dibuat
- ✅ **Dynamic count** - Count berkurang saat notifications di-mark as read
- ✅ **Visual feedback** - Success messages saat mark as read
- ✅ **Responsive design** - Bekerja baik di desktop dan mobile

## Benefits

### 1. **For Users:**
- ✅ **Stay informed** - Selalu tahu status pesanan terbaru
- ✅ **No missed updates** - Notifikasi penting tidak terlewat
- ✅ **Easy management** - Mark as read individual atau bulk
- ✅ **Clear visual cues** - Mudah membedakan read/unread

### 2. **For Business:**
- ✅ **Better engagement** - User lebih engaged dengan updates
- ✅ **Reduced support** - User tahu status tanpa perlu tanya
- ✅ **Improved UX** - Professional notification system
- ✅ **Scalable** - Mudah menambah notification types baru

## Future Enhancements

- 🔄 **Real-time WebSocket notifications** - Instant updates tanpa refresh
- 🔄 **Email notifications** - Backup via email untuk notifikasi penting
- 🔄 **Push notifications** - Browser push notifications
- 🔄 **Notification preferences** - User bisa customize notification types
- 🔄 **Notification categories** - Group notifications by type

Sistem notifikasi sekarang sudah lengkap dan terintegrasi dengan semua fitur utama aplikasi! 🎉

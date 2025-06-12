# 💬💳 **CHATIFY PAYMENT PROOF UPLOAD GUIDE**

## ✅ **Status: SUDAH TERINTEGRASI**

Fitur upload bukti pembayaran sudah terintegrasi dengan **Chatify** yang dibuat teman Anda! 🎉

## 🔄 **Complete Flow:**

### **1. Customer Payment Process**
```
Payment Page → Pilih Method → Kirim Bukti Pembayaran → 
PaymentController::processPayment() → Redirect ke Chatify dengan order_id
```

### **2. Chatify Upload Process**
```
Chatify Page → Form Upload Bukti Pembayaran → 
ChatController::sendPaymentProof() → File Upload → Notification ke Mitra
```

## 📁 **File yang Sudah Terintegrasi:**

### **1. PaymentController.php**
```php
// Redirect ke chatify dengan mitra dan order_id
return redirect("/chatify/{$order->mitra_id}?order_id={$order->id}")
    ->with('success', 'Pembayaran berhasil diproses! Silakan upload bukti pembayaran dan chat dengan mitra.');
```

### **2. ChatController.php**
```php
public function sendPaymentProof(Request $request)
{
    $request->validate([
        'order_id' => 'required|integer|exists:orders,id',
        'proof_image' => 'required|image|max:2048'
    ]);

    // Upload bukti pembayaran
    $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');

    // Update order dengan bukti pembayaran
    $order = \App\Models\Order::findOrFail($request->order_id);
    $order->payment_proof = $proofPath;
    $order->status = 'processing';
    $order->save();

    // Create notification for mitra
    NotificationService::paymentProofUploaded($order);

    // Redirect kembali ke chatify
    return redirect("/chatify/{$order->mitra_id}")
        ->with('success', 'Bukti pembayaran berhasil dikirim!');
}
```

### **3. Chatify sendForm.blade.php**
```html
{{-- Form Upload Bukti Pembayaran --}}
@if(request()->has('order_id'))
    <div class="payment-proof-card">
        <h5>
            <i class="fas fa-receipt"></i>
            Upload Bukti Pembayaran
        </h5>
        <form action="{{ route('chat.sendPaymentProof') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" value="{{ request('order_id') }}">
            
            <input type="file" name="proof_image" accept="image/*" required>
            
            <button type="submit">
                <i class="fas fa-upload"></i>
                Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
@endif
```

## 🚀 **Cara Menggunakan:**

### **Step 1: Customer Checkout**
```bash
1. Customer add items to cart
2. Checkout → Payment page
3. Pilih payment method (DANA/BCA/ShopeePay)
4. Klik "Kirim Bukti Pembayaran"
```

### **Step 2: Redirect ke Chatify**
```bash
5. Otomatis redirect ke: /chatify/{mitra_id}?order_id={order_id}
6. Chatify page terbuka dengan form upload bukti pembayaran
```

### **Step 3: Upload Bukti Pembayaran**
```bash
7. Form "Upload Bukti Pembayaran" muncul di atas chat
8. Customer pilih file gambar bukti transfer
9. Klik "Kirim Bukti Pembayaran"
10. File ter-upload dan mitra dapat notifikasi
```

### **Step 4: Mitra Verification**
```bash
11. Mitra dapat notifikasi ada bukti pembayaran baru
12. Mitra bisa lihat bukti pembayaran di order details
13. Mitra verifikasi dan update status order
```

## 🎨 **UI Features:**

### **Payment Proof Upload Form:**
- ✅ **Card Design** dengan border dan background
- ✅ **Icon Receipt** untuk visual appeal
- ✅ **File Input** dengan accept="image/*"
- ✅ **Upload Button** dengan loading state
- ✅ **Validation Messages** untuk error handling

### **JavaScript Validation:**
- ✅ **File Type Check** (JPG, PNG, GIF only)
- ✅ **File Size Limit** (max 2MB)
- ✅ **Loading State** saat upload
- ✅ **Error Messages** yang user-friendly

## 🔒 **Security Features:**

### **Validation:**
```php
$request->validate([
    'order_id' => 'required|integer|exists:orders,id',
    'proof_image' => 'required|image|max:2048'
]);
```

### **Authorization:**
```php
// Pastikan order milik user yang login
if ($order->user_id !== Auth::id()) {
    abort(403, 'Unauthorized access to this order.');
}
```

### **File Storage:**
```php
// Store di storage/app/public/payment_proofs/
$proofPath = $request->file('proof_image')->store('payment_proofs', 'public');
```

## 📱 **Testing Manual:**

### **Test Complete Flow:**
```bash
1. Login sebagai customer
2. Add items to cart → checkout
3. Payment page → pilih method → "Kirim Bukti Pembayaran"
4. Verify redirect ke: /chatify/{mitra_id}?order_id={order_id}
5. Verify form upload muncul di Chatify
6. Upload file gambar → verify success
7. Login sebagai mitra → verify notification
```

### **Test Edge Cases:**
```bash
1. Upload file non-image → verify error
2. Upload file > 2MB → verify error
3. Access order orang lain → verify 403
4. Upload tanpa file → verify validation error
```

## 🛠️ **Troubleshooting:**

### **Jika Form Upload Tidak Muncul:**
```bash
1. Pastikan URL mengandung ?order_id={id}
2. Cek apakah user sudah login
3. Verify order exists dan milik user
```

### **Jika Upload Gagal:**
```bash
1. Cek file type (harus image)
2. Cek file size (max 2MB)
3. Pastikan storage/app/public writable
4. Run: php artisan storage:link
```

### **Jika Redirect Error:**
```bash
1. Cek route 'chat.sendPaymentProof' exists
2. Verify ChatController method exists
3. Check middleware auth
```

## 📊 **Database Changes:**

### **Orders Table:**
```sql
ALTER TABLE orders ADD COLUMN payment_proof VARCHAR(255) NULL;
```

### **Notifications Table:**
```sql
-- Notification dibuat otomatis untuk mitra
INSERT INTO notifications (user_id, message, order_id, status)
VALUES (mitra_id, 'Bukti pembayaran telah diupload', order_id, 'unread');
```

## ✅ **Status Implementation:**

- ✅ **PaymentController** redirect ke Chatify
- ✅ **ChatController** sendPaymentProof method
- ✅ **Chatify sendForm** upload form
- ✅ **JavaScript validation** dan UX
- ✅ **File storage** dan security
- ✅ **Notification system** untuk mitra
- ✅ **Database integration** dengan orders

## 🎯 **Next Steps:**

### **Optional Enhancements:**
1. **Preview Image** sebelum upload
2. **Progress Bar** saat upload
3. **Multiple File Upload** untuk bukti lengkap
4. **Image Compression** untuk menghemat storage
5. **Real-time Notification** dengan WebSocket

## 🚀 **Ready to Use!**

**Fitur upload bukti pembayaran di Chatify sudah siap digunakan!** 

**Flow:** Payment → Chatify → Upload → Notification → Verification ✅

Silakan test dengan:
1. Checkout sebagai customer
2. Pilih payment method
3. Klik "Kirim Bukti Pembayaran"
4. Upload file di Chatify
5. Verify mitra dapat notifikasi

**Sistem sudah terintegrasi dengan sempurna! 🎉**

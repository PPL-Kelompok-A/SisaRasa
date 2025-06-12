# 🚀 **WEBSOCKET SETUP GUIDE untuk CHATIFY**

## ✅ **Status: REVERB SUDAH TERINSTALL & DIKONFIGURASI**

### 🔧 **Yang Sudah Dilakukan:**

1. **✅ Laravel Reverb Installed**
   ```bash
   composer require laravel/reverb
   ```

2. **✅ Environment Variables Configured**
   ```env
   BROADCAST_DRIVER=reverb
   
   PUSHER_APP_ID="${REVERB_APP_ID}"
   PUSHER_APP_KEY="${REVERB_APP_KEY}"
   PUSHER_APP_SECRET="${REVERB_APP_SECRET}"
   PUSHER_HOST="${REVERB_HOST}"
   PUSHER_PORT="${REVERB_PORT}"
   PUSHER_SCHEME="${REVERB_SCHEME}"
   PUSHER_APP_CLUSTER="mt1"
   
   REVERB_APP_ID=202239
   REVERB_APP_KEY=o9datmujlnmctn6bqqov
   REVERB_APP_SECRET=hexckzaivey1ryh729nd
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

3. **✅ Chatify Config Updated**
   ```php
   'pusher' => [
       'debug' => env('APP_DEBUG', false),
       'key' => env('PUSHER_APP_KEY'),
       'secret' => env('PUSHER_APP_SECRET'),
       'app_id' => env('PUSHER_APP_ID'),
       'options' => [
           'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
           'host' => env('PUSHER_HOST', 'localhost'),
           'port' => env('PUSHER_PORT', 8080),
           'scheme' => env('PUSHER_SCHEME', 'http'),
           'encrypted' => false,
           'useTLS' => false,
       ],
   ],
   ```

4. **✅ Reverb Server Started**
   ```bash
   php artisan reverb:start
   # Server running on localhost:8080
   ```

## 🚀 **Langkah Selanjutnya:**

### **Step 1: Start Both Servers**

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
# Running on http://127.0.0.1:8000
```

**Terminal 2 - Reverb WebSocket Server:**
```bash
php artisan reverb:start
# Running on localhost:8080
```

### **Step 2: Test Chatify Access**

1. **Buka browser:** `http://127.0.0.1:8000/chatify`
2. **Login** sebagai user (customer/mitra)
3. **Verify** halaman Chatify muncul tanpa error 419

### **Step 3: Test Real-time Chat**

1. **Buka 2 browser/tab** berbeda
2. **Login** sebagai user berbeda di masing-masing
3. **Kirim message** dari satu user
4. **Verify** message muncul real-time di user lain

## 🔍 **Troubleshooting:**

### **Jika Masih Error 419:**

1. **Clear All Caches:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

2. **Restart Both Servers:**
   ```bash
   # Stop servers (Ctrl+C)
   php artisan serve
   php artisan reverb:start
   ```

3. **Check Browser Console:**
   - Buka F12 → Console
   - Lihat ada error WebSocket connection?
   - Verify connection ke ws://localhost:8080

### **Jika WebSocket Connection Failed:**

1. **Check Reverb Server Status:**
   ```bash
   # Pastikan server running
   php artisan reverb:start
   ```

2. **Check Port 8080:**
   ```bash
   # Pastikan port 8080 tidak digunakan aplikasi lain
   netstat -an | findstr 8080
   ```

3. **Check Firewall:**
   - Pastikan port 8080 tidak diblokir firewall
   - Allow localhost connections

### **Jika Chatify Masih Blank:**

1. **Check JavaScript Errors:**
   - Buka F12 → Console
   - Lihat error JavaScript
   - Check network requests

2. **Check Assets:**
   ```bash
   # Pastikan Chatify assets ter-publish
   php artisan vendor:publish --tag=chatify-assets
   ```

3. **Check Storage Link:**
   ```bash
   php artisan storage:link
   ```

## 📱 **Testing Checklist:**

### **✅ Basic Functionality:**
- [ ] Chatify page loads without 419 error
- [ ] User can access chat interface
- [ ] Chat list shows available contacts
- [ ] Can open chat with specific user

### **✅ Real-time Features:**
- [ ] Messages appear instantly
- [ ] Typing indicators work
- [ ] Online status updates
- [ ] Message delivery status

### **✅ Payment Proof Upload:**
- [ ] Form upload muncul dengan order_id
- [ ] File upload berfungsi
- [ ] Redirect setelah upload
- [ ] Notification ke mitra

## 🔧 **Advanced Configuration:**

### **Production Setup:**
```env
# Untuk production, gunakan SSL
REVERB_SCHEME=https
REVERB_PORT=443

# Update Chatify config
'scheme' => 'https',
'port' => 443,
'encrypted' => true,
'useTLS' => true,
```

### **Performance Tuning:**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🎯 **Expected Results:**

### **After Setup:**
1. **✅ Chatify loads** without 419 error
2. **✅ Real-time messaging** works
3. **✅ Payment proof upload** functional
4. **✅ Notifications** delivered instantly

### **WebSocket Connection:**
```javascript
// Browser console should show:
WebSocket connection established to ws://localhost:8080
Pusher connection state: connected
```

## 🚀 **Quick Start Commands:**

```bash
# Terminal 1
php artisan serve

# Terminal 2  
php artisan reverb:start

# Browser
http://127.0.0.1:8000/chatify
```

## ✅ **Status Summary:**

- ✅ **Laravel Reverb** installed & configured
- ✅ **Environment variables** set correctly
- ✅ **Chatify config** updated for Reverb
- ✅ **WebSocket server** running on port 8080
- ✅ **Ready for testing** real-time chat

**Next: Start both servers dan test Chatify di browser!** 🎉

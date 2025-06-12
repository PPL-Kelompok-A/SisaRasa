# 🧪 **SELENIUM TEST CASES - PAYMENT SYSTEM SISARASA**

## **Website: www.sisarasa.com**
## **Page: Payment System**
## **Teknik Testing: Equivalence Partitioning & Boundary Value Analysis**

---

| **Scenario ID** | **Case ID** | **Test Scenario** | **Type** | **Test Case** | **Pre Condition** | **Steps** | **Steps Description** | **Expected Result** | **Status (Pass/Fail)** | **Evidence** |
|-----------------|-------------|-------------------|----------|---------------|-------------------|-----------|----------------------|-------------------|------------------------|--------------|
| **SRS.PAY.001** | **SRS.PAY.001.001** | **Customer dapat mengakses halaman pembayaran setelah checkout** | **Positive** | **User dapat melihat halaman pembayaran yang berisi detail pesanan** | **User login dan sudah melakukan checkout** | **1** | **Klik tombol "Checkout" pada halaman cart** | **Sistem menampilkan halaman pembayaran dengan detail pesanan, total harga, dan pilihan metode pembayaran** | **Pass** | **Screenshot halaman payment** |
| **SRS.PAY.002** | **SRS.PAY.002.001** | **Customer dapat memilih metode pembayaran DANA** | **Positive** | **User dapat memilih DANA sebagai metode pembayaran** | **User berada di halaman payment** | **1** | **Pilih radio button "DANA"** | **Radio button DANA terseleksi dan menampilkan informasi pembayaran DANA** | **Pass** | **Screenshot pilihan DANA** |
| **SRS.PAY.002** | **SRS.PAY.002.002** | **Customer dapat memilih metode pembayaran BCA** | **Positive** | **User dapat memilih BCA sebagai metode pembayaran** | **User berada di halaman payment** | **1** | **Pilih radio button "BCA"** | **Radio button BCA terseleksi dan menampilkan informasi rekening BCA** | **Pass** | **Screenshot pilihan BCA** |
| **SRS.PAY.002** | **SRS.PAY.002.003** | **Customer dapat memilih metode pembayaran ShopeePay** | **Positive** | **User dapat memilih ShopeePay sebagai metode pembayaran** | **User berada di halaman payment** | **1** | **Pilih radio button "ShopeePay"** | **Radio button ShopeePay terseleksi dan menampilkan informasi ShopeePay** | **Pass** | **Screenshot pilihan ShopeePay** |
| **SRS.PAY.003** | **SRS.PAY.003.001** | **Customer dapat memproses pembayaran dengan metode yang valid** | **Positive** | **User dapat submit pembayaran dengan metode yang dipilih** | **User sudah memilih metode pembayaran** | **1** | **Klik tombol "Kirim Bukti Pembayaran"** | **Sistem memproses pembayaran dan redirect ke halaman chat dengan mitra** | **Pass** | **Screenshot redirect ke chat** |
| **SRS.PAY.004** | **SRS.PAY.004.001** | **Sistem menampilkan error jika tidak memilih metode pembayaran** | **Negative** | **User submit tanpa memilih metode pembayaran** | **User berada di halaman payment** | **1** | **Klik tombol "Kirim Bukti Pembayaran" tanpa memilih metode** | **Sistem menampilkan pesan error "Please select a payment method"** | **Pass** | **Screenshot error message** |
| **SRS.PAY.005** | **SRS.PAY.005.001** | **Customer dapat melihat detail pesanan di halaman pembayaran** | **Positive** | **User dapat melihat item pesanan, quantity, dan total harga** | **User sudah checkout dan ada order** | **1** | **Scroll ke bagian "Detail Pesanan"** | **Sistem menampilkan daftar item, quantity, harga per item, dan total pembayaran** | **Pass** | **Screenshot detail pesanan** |
| **SRS.PAY.006** | **SRS.PAY.006.001** | **Customer tidak dapat mengakses halaman pembayaran tanpa order** | **Negative** | **User mengakses /payment tanpa order_id** | **User login tapi tidak ada order aktif** | **1** | **Akses URL /payment langsung** | **Sistem menampilkan halaman kosong atau redirect ke menu** | **Pass** | **Screenshot halaman kosong** |
| **SRS.PAY.007** | **SRS.PAY.007.001** | **Customer tidak dapat mengakses order milik user lain** | **Security** | **User mencoba akses order_id milik user lain** | **User login sebagai customer A** | **1** | **Akses /payment?order_id=[order_milik_user_B]** | **Sistem menampilkan error 403 Forbidden** | **Pass** | **Screenshot error 403** |
| **SRS.PAY.008** | **SRS.PAY.008.001** | **Sistem menampilkan informasi mitra dengan benar** | **Positive** | **User dapat melihat nama mitra dan informasi kontak** | **User di halaman payment dengan order valid** | **1** | **Lihat bagian "Informasi Mitra"** | **Sistem menampilkan nama mitra, foto, dan informasi kontak** | **Pass** | **Screenshot info mitra** |
| **SRS.PAY.009** | **SRS.PAY.009.001** | **Customer dapat kembali ke cart dari halaman payment** | **Positive** | **User dapat navigasi kembali ke cart** | **User berada di halaman payment** | **1** | **Klik tombol "Kembali ke Cart" atau browser back** | **Sistem menampilkan halaman cart dengan item yang sama** | **Pass** | **Screenshot kembali ke cart** |
| **SRS.PAY.010** | **SRS.PAY.010.001** | **Sistem memvalidasi order status sebelum payment** | **Positive** | **User hanya bisa bayar order dengan status pending** | **User memiliki order dengan status pending** | **1** | **Akses halaman payment untuk order tersebut** | **Sistem mengizinkan akses dan menampilkan form pembayaran** | **Pass** | **Screenshot form payment** |
| **SRS.PAY.010** | **SRS.PAY.010.002** | **Sistem menolak payment untuk order yang sudah diproses** | **Negative** | **User mencoba bayar order dengan status processing** | **User memiliki order dengan status processing** | **1** | **Akses halaman payment untuk order tersebut** | **Sistem menampilkan pesan "Order sudah diproses" dan tidak menampilkan form payment** | **Pass** | **Screenshot pesan error** |
| **SRS.PAY.011** | **SRS.PAY.011.001** | **Sistem menampilkan total pembayaran dengan benar** | **Positive** | **User dapat melihat kalkulasi total yang akurat** | **User memiliki order dengan multiple items** | **1** | **Lihat bagian "Total Pembayaran"** | **Sistem menampilkan subtotal, biaya layanan (jika ada), dan total akhir dengan benar** | **Pass** | **Screenshot total payment** |
| **SRS.PAY.012** | **SRS.PAY.012.001** | **Customer dapat mengakses chat setelah payment** | **Positive** | **User redirect ke chat setelah submit payment** | **User sudah submit payment dengan metode valid** | **1** | **Tunggu redirect setelah submit payment** | **Sistem redirect ke /chatify/{mitra_id}?order_id={order_id}** | **Pass** | **Screenshot chat page** |
| **SRS.PAY.013** | **SRS.PAY.013.001** | **Sistem menampilkan loading state saat memproses payment** | **Positive** | **User melihat indikator loading saat submit** | **User sudah pilih metode dan klik submit** | **1** | **Observe tombol submit setelah diklik** | **Tombol menampilkan loading spinner atau disabled state** | **Pass** | **Screenshot loading state** |
| **SRS.PAY.014** | **SRS.PAY.014.001** | **Halaman payment responsive di mobile device** | **Positive** | **User dapat menggunakan payment di mobile** | **User akses dari mobile browser** | **1** | **Akses halaman payment dari mobile** | **Layout responsive, tombol mudah diklik, text readable** | **Pass** | **Screenshot mobile view** |
| **SRS.PAY.015** | **SRS.PAY.015.001** | **Sistem menangani session timeout dengan baik** | **Negative** | **User session expired saat di halaman payment** | **User idle di halaman payment > session timeout** | **1** | **Tunggu hingga session expired, lalu coba submit** | **Sistem redirect ke login page dengan pesan session expired** | **Pass** | **Screenshot redirect login** |

---

## **📊 SUMMARY TEST CASES:**

- **Total Test Cases:** 17
- **Positive Test Cases:** 12
- **Negative Test Cases:** 4  
- **Security Test Cases:** 1
- **Coverage Areas:**
  - ✅ Payment Method Selection
  - ✅ Order Validation
  - ✅ Security & Authorization
  - ✅ UI/UX & Responsiveness
  - ✅ Error Handling
  - ✅ Integration with Chat System

## **🎯 PRIORITY TESTING:**

### **High Priority:**
- SRS.PAY.001.001 (Access payment page)
- SRS.PAY.002.001-003 (Payment method selection)
- SRS.PAY.003.001 (Process payment)
- SRS.PAY.007.001 (Security test)

### **Medium Priority:**
- SRS.PAY.004.001 (Validation error)
- SRS.PAY.010.001-002 (Order status validation)
- SRS.PAY.012.001 (Chat integration)

### **Low Priority:**
- SRS.PAY.014.001 (Mobile responsive)
- SRS.PAY.013.001 (Loading state)
- SRS.PAY.015.001 (Session timeout)

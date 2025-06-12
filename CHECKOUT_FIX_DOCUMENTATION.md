# Perbaikan Masalah Checkout - Error SQL Field 'food_id' doesn't have a default value

## Masalah yang Ditemukan

Saat pembeli klik checkout, muncul error SQL:
```
SQLSTATE[HY000]: General error: 1364 Field 'food_id' doesn't have a default value 
(Connection: mysql, SQL: insert into `order_items` (`order_id`, `price`, `quantity`, `updated_at`, `created_at`) values (21, 10000, 1, 2025-06-10 05:25:45, 2025-06-10 05:25:45))
```

### Analisis Root Cause:

1. **Tabel `order_items` memiliki kolom `food_id` yang required** (tidak nullable) berdasarkan migration
2. **CartController tidak menyimpan `food_id` saat membuat OrderItem** - hanya menyimpan `name`, `price`, dan `quantity`
3. **CartItem tidak menyimpan `food_id`** - hanya menyimpan nama makanan, bukan referensi ke food
4. **Model OrderItem memiliki `food_id` di `$fillable`** tapi tidak digunakan saat create
5. **Route payment salah** - menggunakan `payment` instead of `payment.show`

## Solusi yang Diimplementasikan

### 1. Menambahkan kolom `food_id` ke tabel `cart_items`

**Migration:** `2025_06_10_053208_add_food_id_to_cart_items_table.php`
```php
public function up(): void
{
    Schema::table('cart_items', function (Blueprint $table) {
        $table->foreignId('food_id')->nullable()->after('id')->constrained('foods')->onDelete('cascade');
    });
}
```

### 2. Update Model CartItem

**File:** `app/Models/CartItem.php`
```php
protected $fillable = ['food_id', 'name', 'desc', 'price', 'img', 'quantity', 'selected', 'mitra_id'];

public function food()
{
    return $this->belongsTo(Food::class);
}
```

### 3. Update CartController untuk menyimpan `food_id`

**File:** `app/Http/Controllers/CartController.php`

**Saat menambah ke cart:**
```php
// Cek apakah sudah ada di cart berdasarkan food_id
$cartItem = CartItem::where('food_id', $food->id)->first();
if ($cartItem) {
    $cartItem->quantity += 1;
    $cartItem->save();
} else {
    CartItem::create([
        'food_id' => $food->id,  // ← DITAMBAHKAN
        'name' => $food->name,
        'desc' => $food->description,
        'price' => $food->price,
        'img' => $food->image ? Storage::url($food->image) : asset('images/default-food.png'),
        'quantity' => 1,
        'selected' => false,
        'mitra_id' => $mitraId, 
    ]);
}
```

**Saat checkout:**
```php
// Simpan item order
foreach ($selectedItems as $item) {
    $subtotal = $item->price * $item->quantity;
    OrderItem::create([
        'order_id' => $order->id,
        'food_id' => $item->food_id,  // ← DITAMBAHKAN
        'quantity' => $item->quantity,
        'price' => $item->price,
        'subtotal' => $subtotal,      // ← DITAMBAHKAN
    ]);
}
```

### 4. Perbaiki Route Payment

**File:** `app/Http/Controllers/CartController.php`
```php
// Redirect ke halaman payment
return redirect()->route('payment.show', ['order_id' => $order->id]);
```

### 5. Command untuk Update Data Lama

**File:** `app/Console/Commands/UpdateCartItemsFoodId.php`

Command untuk mengupdate cart items yang sudah ada:
```bash
php artisan cart:update-food-id
```

Command ini akan:
- Mencari semua cart items dengan `food_id` null
- Mencocokkan dengan food berdasarkan nama
- Mengset `food_id` yang sesuai
- Memberikan laporan progress dan warning untuk data yang tidak cocok

### 6. Comprehensive Test Coverage

**File:** `tests/Feature/CheckoutTest.php`

Test yang mencakup:
- ✅ Test checkout berhasil dengan `food_id` yang valid
- ✅ Test checkout gagal tanpa item yang dipilih
- ✅ Verifikasi order dan order_items dibuat dengan benar
- ✅ Verifikasi cart items dihapus setelah checkout
- ✅ Verifikasi subtotal dihitung dengan benar

## Hasil Perbaikan

### ✅ **Checkout Berhasil**
- Tidak ada lagi error SQL saat checkout
- Order dan OrderItem dibuat dengan benar
- `food_id` tersimpan dengan benar di order_items

### ✅ **Data Integrity**
- Relasi antara CartItem dan Food terjaga
- Relasi antara OrderItem dan Food terjaga
- Subtotal dihitung dengan benar

### ✅ **Backward Compatibility**
- Data cart lama diupdate dengan command
- Tidak ada data yang hilang

### ✅ **Test Coverage**
```
✓ can checkout cart items successfully
✓ cannot checkout without selected items

Tests: 2 passed (11 assertions)
```

## Cara Menjalankan Perbaikan

1. **Jalankan migration:**
   ```bash
   php artisan migrate --path=database/migrations/2025_06_10_053208_add_food_id_to_cart_items_table.php
   ```

2. **Update existing cart data:**
   ```bash
   php artisan cart:update-food-id
   ```

3. **Jalankan test untuk verifikasi:**
   ```bash
   php artisan test --filter=CheckoutTest
   ```

4. **Test manual di browser:**
   - Login sebagai customer
   - Tambahkan item ke keranjang
   - Pilih item untuk checkout
   - Klik checkout
   - Verifikasi redirect ke halaman payment tanpa error

## Catatan Penting

- ✅ Perbaikan ini bersifat backward compatible
- ✅ Semua cart items baru akan otomatis memiliki `food_id`
- ✅ Data lama diupdate dengan command yang aman
- ✅ Test coverage memastikan fungsionalitas bekerja dengan benar
- ✅ Relasi database terjaga dengan foreign key constraints

## Flow Checkout yang Diperbaiki

1. **User menambah item ke cart** → `food_id` tersimpan
2. **User memilih item untuk checkout** → Validasi berdasarkan `food_id`
3. **User klik checkout** → Order dibuat dengan benar
4. **OrderItem dibuat** → Dengan `food_id`, `subtotal` yang benar
5. **Redirect ke payment** → Menggunakan route yang benar
6. **Cart items dihapus** → Setelah checkout berhasil

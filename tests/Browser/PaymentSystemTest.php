<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Food;
use App\Models\Order;
use App\Models\CartItem;

class PaymentSystemTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@test.com',
            'name' => 'Test Customer'
        ]);
        
        $this->mitra = User::factory()->create([
            'role' => 'mitra',
            'email' => 'mitra@test.com',
            'name' => 'Test Mitra'
        ]);
        
        // Create test food
        $this->food = Food::factory()->create([
            'user_id' => $this->mitra->id,
            'mitra_id' => $this->mitra->id,
            'name' => 'Test Food',
            'price' => 25000,
            'description' => 'Test food description'
        ]);
        
        // Create test order
        $this->order = Order::create([
            'user_id' => $this->customer->id,
            'mitra_id' => $this->mitra->id,
            'status' => 'pending',
            'total_amount' => 50000,
            'delivery_address' => 'Test Address'
        ]);
    }

    /**
     * SRS.PAY.001.001: Customer dapat mengakses halaman pembayaran setelah checkout
     */
    public function test_customer_can_access_payment_page_after_checkout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Pembayaran')
                    ->assertSee('Detail Pesanan')
                    ->assertSee('Metode Pembayaran')
                    ->screenshot('payment_page_access');
        });
    }

    /**
     * SRS.PAY.002.001: Customer dapat memilih metode pembayaran DANA
     */
    public function test_customer_can_select_dana_payment_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->radio('payment_method', 'DANA')
                    ->assertRadioSelected('payment_method', 'DANA')
                    ->assertSee('DANA')
                    ->screenshot('dana_payment_selected');
        });
    }

    /**
     * SRS.PAY.002.002: Customer dapat memilih metode pembayaran BCA
     */
    public function test_customer_can_select_bca_payment_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->radio('payment_method', 'BCA')
                    ->assertRadioSelected('payment_method', 'BCA')
                    ->assertSee('BCA')
                    ->screenshot('bca_payment_selected');
        });
    }

    /**
     * SRS.PAY.002.003: Customer dapat memilih metode pembayaran ShopeePay
     */
    public function test_customer_can_select_shopee_pay_payment_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->radio('payment_method', 'ShopeePay')
                    ->assertRadioSelected('payment_method', 'ShopeePay')
                    ->assertSee('ShopeePay')
                    ->screenshot('shopeepay_payment_selected');
        });
    }

    /**
     * SRS.PAY.003.001: Customer dapat memproses pembayaran dengan metode yang valid
     */
    public function test_customer_can_process_payment_with_valid_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->radio('payment_method', 'DANA')
                    ->press('Kirim Bukti Pembayaran')
                    ->waitForLocation('/chatify/' . $this->mitra->id)
                    ->assertUrlContains('order_id=' . $this->order->id)
                    ->screenshot('payment_processed_redirect_chat');
        });
    }

    /**
     * SRS.PAY.004.001: Sistem menampilkan error jika tidak memilih metode pembayaran
     */
    public function test_system_shows_error_when_no_payment_method_selected()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->press('Kirim Bukti Pembayaran')
                    ->waitForText('The payment method field is required')
                    ->assertSee('The payment method field is required')
                    ->screenshot('payment_method_required_error');
        });
    }

    /**
     * SRS.PAY.005.001: Customer dapat melihat detail pesanan di halaman pembayaran
     */
    public function test_customer_can_view_order_details_on_payment_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Detail Pesanan')
                    ->assertSee('Total: Rp ' . number_format($this->order->total_amount, 0, ',', '.'))
                    ->screenshot('order_details_display');
        });
    }

    /**
     * SRS.PAY.006.001: Customer tidak dapat mengakses halaman pembayaran tanpa order
     */
    public function test_customer_cannot_access_payment_page_without_order()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment')
                    ->assertDontSee('Kirim Bukti Pembayaran')
                    ->assertSee('Tidak ada pesanan')
                    ->screenshot('payment_page_without_order');
        });
    }

    /**
     * SRS.PAY.007.001: Customer tidak dapat mengakses order milik user lain
     */
    public function test_customer_cannot_access_other_user_order()
    {
        // Create another customer and order
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $otherOrder = Order::create([
            'user_id' => $otherCustomer->id,
            'mitra_id' => $this->mitra->id,
            'status' => 'pending',
            'total_amount' => 30000,
            'delivery_address' => 'Other Address'
        ]);

        $this->browse(function (Browser $browser) use ($otherOrder) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $otherOrder->id)
                    ->assertSee('403')
                    ->screenshot('unauthorized_order_access');
        });
    }

    /**
     * SRS.PAY.008.001: Sistem menampilkan informasi mitra dengan benar
     */
    public function test_system_displays_mitra_information_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee($this->mitra->name)
                    ->assertSee('Informasi Mitra')
                    ->screenshot('mitra_information_display');
        });
    }

    /**
     * SRS.PAY.010.001: Sistem memvalidasi order status sebelum payment
     */
    public function test_system_validates_order_status_before_payment()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Kirim Bukti Pembayaran')
                    ->assertDontSee('Order sudah diproses')
                    ->screenshot('valid_order_status_payment');
        });
    }

    /**
     * SRS.PAY.010.002: Sistem menolak payment untuk order yang sudah diproses
     */
    public function test_system_rejects_payment_for_processed_order()
    {
        // Update order status to processing
        $this->order->update(['status' => 'processing']);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Order sudah diproses')
                    ->assertDontSee('Kirim Bukti Pembayaran')
                    ->screenshot('processed_order_payment_rejected');
        });
    }

    /**
     * SRS.PAY.011.001: Sistem menampilkan total pembayaran dengan benar
     */
    public function test_system_displays_correct_payment_total()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Total Pembayaran')
                    ->assertSee('Rp ' . number_format($this->order->total_amount, 0, ',', '.'))
                    ->screenshot('correct_payment_total');
        });
    }

    /**
     * SRS.PAY.014.001: Halaman payment responsive di mobile device
     */
    public function test_payment_page_responsive_on_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->resize(375, 667) // iPhone 6/7/8 size
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->assertSee('Pembayaran')
                    ->assertSee('Kirim Bukti Pembayaran')
                    ->screenshot('payment_page_mobile_responsive');
        });
    }

    /**
     * SRS.PAY.015.001: Sistem menangani session timeout dengan baik
     */
    public function test_system_handles_session_timeout()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->customer)
                    ->visit('/payment?order_id=' . $this->order->id)
                    ->script('document.cookie = "laravel_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";');
            
            // Try to submit payment after session cleared
            $browser->radio('payment_method', 'DANA')
                    ->press('Kirim Bukti Pembayaran')
                    ->waitForLocation('/login')
                    ->assertSee('Login')
                    ->screenshot('session_timeout_redirect_login');
        });
    }
}

# 🧪 **SELENIUM TESTING GUIDE - PAYMENT SYSTEM**

## 📋 **TEST CASES YANG SUDAH DIBUAT:**

### **✅ 17 Test Cases Lengkap:**
1. **SRS.PAY.001.001** - Access payment page ✅
2. **SRS.PAY.002.001** - Select DANA payment ✅
3. **SRS.PAY.002.002** - Select BCA payment ✅
4. **SRS.PAY.002.003** - Select ShopeePay payment ✅
5. **SRS.PAY.003.001** - Process valid payment ✅
6. **SRS.PAY.004.001** - Error without payment method ✅
7. **SRS.PAY.005.001** - View order details ✅
8. **SRS.PAY.006.001** - Access without order ✅
9. **SRS.PAY.007.001** - Security test (other user order) ✅
10. **SRS.PAY.008.001** - Display mitra information ✅
11. **SRS.PAY.010.001** - Valid order status ✅
12. **SRS.PAY.010.002** - Processed order rejection ✅
13. **SRS.PAY.011.001** - Correct payment total ✅
14. **SRS.PAY.014.001** - Mobile responsive ✅
15. **SRS.PAY.015.001** - Session timeout handling ✅

## 🚀 **SETUP SELENIUM TESTING:**

### **Step 1: Install Laravel Dusk**
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

### **Step 2: Configure Environment**
```bash
# Copy .env untuk testing
cp .env .env.dusk.local

# Update .env.dusk.local
APP_URL=http://localhost:8000
DB_DATABASE=sisarasa_testing
```

### **Step 3: Install ChromeDriver**
```bash
php artisan dusk:chrome-driver
```

### **Step 4: Setup Database Testing**
```bash
# Create testing database
mysql -u root -p -e "CREATE DATABASE sisarasa_testing;"

# Run migrations for testing
php artisan migrate --env=dusk.local
```

## 🧪 **MENJALANKAN TESTS:**

### **Run All Payment Tests:**
```bash
php artisan dusk tests/Browser/PaymentSystemTest.php
```

### **Run Specific Test:**
```bash
# Test access payment page
php artisan dusk --filter=test_customer_can_access_payment_page_after_checkout

# Test payment method selection
php artisan dusk --filter=test_customer_can_select_dana_payment_method

# Test security
php artisan dusk --filter=test_customer_cannot_access_other_user_order
```

### **Run with Screenshots:**
```bash
php artisan dusk --browse
```

## 📊 **TEST EXECUTION MATRIX:**

| **Test Case ID** | **Test Name** | **Priority** | **Expected Duration** | **Dependencies** |
|------------------|---------------|--------------|----------------------|------------------|
| **SRS.PAY.001.001** | Access payment page | **High** | 30s | User login, Order exists |
| **SRS.PAY.002.001** | Select DANA | **High** | 15s | Payment page loaded |
| **SRS.PAY.002.002** | Select BCA | **High** | 15s | Payment page loaded |
| **SRS.PAY.002.003** | Select ShopeePay | **High** | 15s | Payment page loaded |
| **SRS.PAY.003.001** | Process payment | **High** | 45s | Payment method selected |
| **SRS.PAY.004.001** | Validation error | **Medium** | 20s | Payment page loaded |
| **SRS.PAY.005.001** | Order details | **Medium** | 25s | Order with items |
| **SRS.PAY.006.001** | No order access | **Medium** | 20s | User login only |
| **SRS.PAY.007.001** | Security test | **High** | 30s | Multiple users |
| **SRS.PAY.008.001** | Mitra info | **Low** | 20s | Order with mitra |
| **SRS.PAY.010.001** | Valid status | **Medium** | 25s | Pending order |
| **SRS.PAY.010.002** | Invalid status | **Medium** | 25s | Processed order |
| **SRS.PAY.011.001** | Payment total | **Medium** | 20s | Order with amount |
| **SRS.PAY.014.001** | Mobile responsive | **Low** | 30s | Mobile viewport |
| **SRS.PAY.015.001** | Session timeout | **Low** | 40s | Session manipulation |

## 🎯 **TEST EXECUTION PLAN:**

### **Phase 1: Core Functionality (High Priority)**
```bash
# Run core payment tests
php artisan dusk --filter="test_customer_can_access_payment_page_after_checkout|test_customer_can_select_.*_payment_method|test_customer_can_process_payment_with_valid_method|test_customer_cannot_access_other_user_order"
```

### **Phase 2: Validation & Error Handling (Medium Priority)**
```bash
# Run validation tests
php artisan dusk --filter="test_system_shows_error_when_no_payment_method_selected|test_customer_can_view_order_details|test_system_validates_order_status"
```

### **Phase 3: Edge Cases & UX (Low Priority)**
```bash
# Run UX and edge case tests
php artisan dusk --filter="test_payment_page_responsive_on_mobile|test_system_handles_session_timeout"
```

## 📸 **SCREENSHOT EVIDENCE:**

### **Screenshots akan tersimpan di:**
```
tests/Browser/screenshots/
├── payment_page_access.png
├── dana_payment_selected.png
├── bca_payment_selected.png
├── shopeepay_payment_selected.png
├── payment_processed_redirect_chat.png
├── payment_method_required_error.png
├── order_details_display.png
├── payment_page_without_order.png
├── unauthorized_order_access.png
├── mitra_information_display.png
├── valid_order_status_payment.png
├── processed_order_payment_rejected.png
├── correct_payment_total.png
├── payment_page_mobile_responsive.png
└── session_timeout_redirect_login.png
```

## 🔧 **TROUBLESHOOTING:**

### **Common Issues:**

#### **1. ChromeDriver Issues:**
```bash
# Update ChromeDriver
php artisan dusk:chrome-driver --detect

# Or specify version
php artisan dusk:chrome-driver 91
```

#### **2. Database Issues:**
```bash
# Reset testing database
php artisan migrate:fresh --env=dusk.local --seed
```

#### **3. Server Not Running:**
```bash
# Start Laravel server for testing
php artisan serve --port=8000
```

#### **4. Permission Issues:**
```bash
# Fix screenshot directory permissions
chmod -R 755 tests/Browser/screenshots/
```

## 📋 **TEST REPORT TEMPLATE:**

### **Execution Summary:**
- **Total Tests:** 15
- **Passed:** [X]
- **Failed:** [X]
- **Skipped:** [X]
- **Execution Time:** [X] minutes

### **Failed Tests Analysis:**
| **Test Case** | **Error** | **Root Cause** | **Fix Applied** |
|---------------|-----------|----------------|-----------------|
| [Test Name] | [Error Message] | [Analysis] | [Solution] |

### **Coverage Report:**
- **Payment Method Selection:** ✅ 100%
- **Order Validation:** ✅ 100%
- **Security Testing:** ✅ 100%
- **Error Handling:** ✅ 100%
- **UI/UX Testing:** ✅ 100%

## 🚀 **CONTINUOUS INTEGRATION:**

### **GitHub Actions Configuration:**
```yaml
name: Payment System Tests
on: [push, pull_request]
jobs:
  dusk-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install dependencies
        run: composer install
      - name: Run Dusk Tests
        run: php artisan dusk tests/Browser/PaymentSystemTest.php
```

## 🎯 **SUCCESS CRITERIA:**

### **✅ Test Passes If:**
- All 15 test cases execute successfully
- Screenshots captured for evidence
- No critical security vulnerabilities found
- Payment flow works end-to-end
- Error handling works properly
- Mobile responsiveness confirmed

### **❌ Test Fails If:**
- Any high-priority test fails
- Security test fails
- Payment processing doesn't work
- Critical UI elements not accessible
- Database errors occur

**Ready untuk testing! Semua test cases sudah siap dijalankan! 🧪✅**

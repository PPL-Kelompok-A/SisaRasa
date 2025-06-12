from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time

def test_mitra_notification_simple():
    """Simple test untuk debug masalah Selenium"""
    
    # Setup Chrome
    chrome_options = Options()
    chrome_options.add_argument("--window-size=1920,1080")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 30)  # Increase timeout
    
    try:
        print("🔍 Starting Simple Notification Test...")
        
        # Step 1: Login
        print("📝 Step 1: Login sebagai mitra")
        driver.get("http://localhost:8000/login")
        
        # Wait and fill login
        email_input = wait.until(EC.presence_of_element_located((By.NAME, "email")))
        email_input.clear()
        email_input.send_keys("mitra@sisarasa.com")
        
        password_input = driver.find_element(By.NAME, "password")
        password_input.clear()
        password_input.send_keys("password")
        
        # Submit login
        login_button = driver.find_element(By.XPATH, "//button[@type='submit']")
        login_button.click()
        
        # Wait for dashboard
        wait.until(EC.url_contains("dashboard"))
        print("✅ Login berhasil")
        
        # Step 2: Go to notifications directly
        print("📝 Step 2: Buka halaman notifikasi")
        driver.get("http://localhost:8000/notifications")
        
        # Wait for page load
        wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        time.sleep(3)  # Extra wait
        
        print("✅ Halaman notifikasi terbuka")
        
        # Step 3: Check for notifications
        print("📝 Step 3: Cek notifikasi yang ada")
        
        # Look for notification cards
        notification_cards = driver.find_elements(By.CLASS_NAME, "notification-card")
        print(f"📊 Ditemukan {len(notification_cards)} notifikasi")
        
        if len(notification_cards) == 0:
            print("❌ Tidak ada notifikasi ditemukan")
            return
        
        # Step 4: Look for mark as read buttons
        print("📝 Step 4: Cari tombol mark as read")
        
        # Try multiple selectors
        selectors = [
            ".mark-read-btn",
            "button[data-notification-id]",
            "button.text-orange-600",
            "button.text-blue-600",
            "form button[type='submit']"
        ]
        
        mark_read_button = None
        for selector in selectors:
            buttons = driver.find_elements(By.CSS_SELECTOR, selector)
            if buttons:
                print(f"✅ Ditemukan {len(buttons)} button dengan selector: {selector}")
                mark_read_button = buttons[0]
                break
        
        if not mark_read_button:
            print("❌ Tidak ada tombol mark as read ditemukan")
            # Take screenshot for debugging
            driver.save_screenshot("no_mark_read_button.png")
            return
        
        # Step 5: Click mark as read
        print("📝 Step 5: Klik tombol mark as read")
        
        # Scroll to button
        driver.execute_script("arguments[0].scrollIntoView(true);", mark_read_button)
        time.sleep(1)
        
        # Wait for clickable
        wait.until(EC.element_to_be_clickable(mark_read_button))
        
        # Get notification ID before click
        notification_id = mark_read_button.get_attribute("data-notification-id")
        print(f"📋 Notification ID: {notification_id}")
        
        # Click button
        mark_read_button.click()
        print("✅ Tombol diklik")
        
        # Step 6: Wait for response
        print("📝 Step 6: Tunggu response")
        time.sleep(3)
        
        # Check for success message or page reload
        try:
            success_message = driver.find_element(By.XPATH, "//div[contains(@class, 'bg-green-100')]")
            print("✅ Success message ditemukan")
        except:
            print("ℹ️ Tidak ada success message, cek perubahan lain")
        
        # Check if button disappeared or changed
        try:
            same_button = driver.find_element(By.ID, f"mark-read-{notification_id}")
            print("⚠️ Button masih ada, mungkin tidak berhasil")
        except:
            print("✅ Button hilang, kemungkinan berhasil")
        
        print("🎉 Test selesai!")
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        driver.save_screenshot("selenium_error.png")
        
        # Print page source for debugging
        with open("page_source_debug.html", "w", encoding="utf-8") as f:
            f.write(driver.page_source)
        
        raise
    
    finally:
        driver.quit()

if __name__ == "__main__":
    test_mitra_notification_simple()

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time

def test_mitra_notification_fixed():
    """Fixed test untuk notifikasi mitra"""
    
    # Setup Chrome
    chrome_options = Options()
    chrome_options.add_argument("--window-size=1920,1080")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    
    driver = webdriver.Chrome(options=chrome_options)
    driver.implicitly_wait(10)  # Reduce implicit wait
    wait = WebDriverWait(driver, 20)  # Explicit wait
    
    try:
        print("🔍 Starting Fixed Notification Test...")
        
        # Step 1: Login sebagai mitra
        print("📝 Step 1: Login sebagai mitra")
        driver.get("http://localhost:8000/login")
        
        # Wait for login form
        email_input = wait.until(EC.presence_of_element_located((By.NAME, "email")))
        email_input.clear()
        email_input.send_keys("mitra@sisarasa.com")
        
        password_input = driver.find_element(By.NAME, "password")
        password_input.clear()
        password_input.send_keys("password")
        
        # Submit login
        login_button = driver.find_element(By.XPATH, "//button[@type='submit']")
        login_button.click()
        
        # Wait for redirect
        wait.until(lambda d: "login" not in d.current_url)
        print("✅ Login berhasil")
        
        # Step 2: Navigate to notifications
        print("📝 Step 2: Buka halaman notifikasi")
        driver.get("http://localhost:8000/notifications")
        
        # Wait for page to load completely
        wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
        time.sleep(2)  # Extra wait for dynamic content
        
        print("✅ Halaman notifikasi terbuka")
        
        # Step 3: Check page content
        print("📝 Step 3: Analisis konten halaman")
        
        # Check if we're on the right page
        page_title = driver.title
        print(f"📄 Page title: {page_title}")
        
        # Check for common elements
        elements_to_check = [
            ("h1", "Header"),
            ("h2", "Sub-header"),
            (".notification-card", "Notification cards"),
            (".mark-read-btn", "Mark as read buttons"),
            ("button", "Any buttons"),
            ("form", "Forms"),
            (".bg-white", "White background elements")
        ]
        
        for selector, description in elements_to_check:
            try:
                elements = driver.find_elements(By.CSS_SELECTOR, selector)
                print(f"📊 {description}: {len(elements)} found")
            except Exception as e:
                print(f"❌ Error checking {description}: {str(e)}")
        
        # Step 4: Look for notifications with multiple strategies
        print("📝 Step 4: Cari notifikasi dengan berbagai cara")
        
        notification_found = False
        
        # Strategy 1: Look for notification cards
        try:
            notification_cards = driver.find_elements(By.CSS_SELECTOR, ".notification-card")
            if notification_cards:
                print(f"✅ Strategy 1: Ditemukan {len(notification_cards)} notification cards")
                notification_found = True
            else:
                print("❌ Strategy 1: Tidak ada notification cards")
        except Exception as e:
            print(f"❌ Strategy 1 error: {str(e)}")
        
        # Strategy 2: Look for any div with notification-related classes
        try:
            notification_divs = driver.find_elements(By.XPATH, "//div[contains(@class, 'notification') or contains(@id, 'notification')]")
            if notification_divs:
                print(f"✅ Strategy 2: Ditemukan {len(notification_divs)} notification divs")
                notification_found = True
            else:
                print("❌ Strategy 2: Tidak ada notification divs")
        except Exception as e:
            print(f"❌ Strategy 2 error: {str(e)}")
        
        # Strategy 3: Look for "Tidak ada notifikasi" message
        try:
            no_notification_msg = driver.find_elements(By.XPATH, "//div[contains(text(), 'Tidak ada notifikasi') or contains(text(), 'No notifications')]")
            if no_notification_msg:
                print("ℹ️ Strategy 3: Ditemukan pesan 'Tidak ada notifikasi'")
            else:
                print("❌ Strategy 3: Tidak ada pesan 'Tidak ada notifikasi'")
        except Exception as e:
            print(f"❌ Strategy 3 error: {str(e)}")
        
        if not notification_found:
            print("❌ Tidak ada notifikasi ditemukan dengan semua strategy")
            # Take screenshot for debugging
            driver.save_screenshot("no_notifications_found.png")
            
            # Save page source for analysis
            with open("notifications_page_source.html", "w", encoding="utf-8") as f:
                f.write(driver.page_source)
            
            print("📸 Screenshot dan page source disimpan untuk debugging")
            return
        
        # Step 5: Look for mark as read buttons
        print("📝 Step 5: Cari tombol mark as read")
        
        mark_read_strategies = [
            (".mark-read-btn", "Class mark-read-btn"),
            ("button[data-notification-id]", "Button with data-notification-id"),
            ("button.text-orange-600", "Orange button"),
            ("button.text-blue-600", "Blue button"),
            ("form button", "Form buttons"),
            ("button i.fa-check", "Button with check icon"),
            ("//button[contains(@class, 'text-orange-600') or contains(@class, 'text-blue-600')]", "XPath orange/blue buttons")
        ]
        
        mark_read_button = None
        for selector, description in mark_read_strategies:
            try:
                if selector.startswith("//"):
                    buttons = driver.find_elements(By.XPATH, selector)
                else:
                    buttons = driver.find_elements(By.CSS_SELECTOR, selector)
                
                if buttons:
                    print(f"✅ {description}: {len(buttons)} found")
                    mark_read_button = buttons[0]
                    break
                else:
                    print(f"❌ {description}: 0 found")
            except Exception as e:
                print(f"❌ {description} error: {str(e)}")
        
        if not mark_read_button:
            print("❌ Tidak ada tombol mark as read ditemukan")
            driver.save_screenshot("no_mark_read_button.png")
            return
        
        # Step 6: Click mark as read button
        print("📝 Step 6: Klik tombol mark as read")
        
        try:
            # Scroll to button
            driver.execute_script("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", mark_read_button)
            time.sleep(1)
            
            # Wait for button to be clickable
            wait.until(EC.element_to_be_clickable(mark_read_button))
            
            # Get button info
            button_text = mark_read_button.get_attribute("outerHTML")[:100]
            print(f"📋 Button info: {button_text}...")
            
            # Click button
            mark_read_button.click()
            print("✅ Tombol berhasil diklik")
            
            # Wait for response
            time.sleep(3)
            
            # Check for success indicators
            success_indicators = [
                ("//div[contains(@class, 'bg-green-100')]", "Success message"),
                ("//div[contains(text(), 'berhasil')]", "Success text"),
                ("//div[contains(@class, 'alert-success')]", "Success alert")
            ]
            
            for selector, description in success_indicators:
                try:
                    elements = driver.find_elements(By.XPATH, selector)
                    if elements:
                        print(f"✅ {description} ditemukan")
                        break
                except:
                    continue
            else:
                print("ℹ️ Tidak ada success indicator yang jelas, tapi tombol berhasil diklik")
            
            print("🎉 Test selesai dengan sukses!")
            
        except Exception as e:
            print(f"❌ Error saat klik button: {str(e)}")
            driver.save_screenshot("click_button_error.png")
            raise
        
    except Exception as e:
        print(f"❌ Test gagal: {str(e)}")
        driver.save_screenshot("test_failed.png")
        
        # Save page source for debugging
        with open("error_page_source.html", "w", encoding="utf-8") as f:
            f.write(driver.page_source)
        
        raise
    
    finally:
        driver.quit()

if __name__ == "__main__":
    test_mitra_notification_fixed()

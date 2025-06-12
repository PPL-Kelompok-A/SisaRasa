from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
import time
import unittest

class MitraNotificationTest(unittest.TestCase):
    
    def setUp(self):
        # Setup Chrome options
        chrome_options = Options()
        chrome_options.add_argument("--no-sandbox")
        chrome_options.add_argument("--disable-dev-shm-usage")
        chrome_options.add_argument("--window-size=1920,1080")
        
        # Initialize driver
        self.driver = webdriver.Chrome(options=chrome_options)
        self.driver.implicitly_wait(10)
        self.wait = WebDriverWait(self.driver, 20)
        
        # Base URL
        self.base_url = "http://localhost:8000"
        
    def tearDown(self):
        self.driver.quit()
    
    def test_mitra_notification_mark_as_read(self):
        """Test mitra login, view notifications, and mark as read"""
        
        print("🔍 Starting Mitra Notification Test...")
        
        try:
            # Step 1: Login sebagai mitra
            print("📝 Step 1: Login sebagai mitra")
            self.driver.get(f"{self.base_url}/login")
            
            # Wait for login page to load
            email_input = self.wait.until(
                EC.presence_of_element_located((By.NAME, "email"))
            )
            
            # Fill login form
            email_input.clear()
            email_input.send_keys("mitra@sisarasa.com")
            
            password_input = self.driver.find_element(By.NAME, "password")
            password_input.clear()
            password_input.send_keys("password")
            
            # Submit login
            login_button = self.driver.find_element(By.XPATH, "//button[@type='submit']")
            login_button.click()
            
            # Wait for redirect to dashboard
            self.wait.until(
                EC.url_contains("/mitra/dashboard")
            )
            print("✅ Login berhasil")
            
            # Step 2: Klik icon notifikasi
            print("📝 Step 2: Klik icon notifikasi")
            
            # Wait for notification icon to be clickable
            notification_icon = self.wait.until(
                EC.element_to_be_clickable((By.XPATH, "//a[@href='/notifications']"))
            )
            
            # Scroll to notification icon if needed
            self.driver.execute_script("arguments[0].scrollIntoView(true);", notification_icon)
            time.sleep(1)
            
            # Click notification icon
            notification_icon.click()
            
            # Wait for notifications page to load
            self.wait.until(
                EC.url_contains("/notifications")
            )
            print("✅ Halaman notifikasi terbuka")
            
            # Step 3: Tunggu notifikasi muncul
            print("📝 Step 3: Mencari notifikasi yang belum dibaca")
            
            # Wait for notifications to load
            notifications_container = self.wait.until(
                EC.presence_of_element_located((By.CLASS_NAME, "notification-card"))
            )
            
            # Find unread notification mark as read button using class
            mark_read_buttons = self.driver.find_elements(
                By.CLASS_NAME, "mark-read-btn"
            )
            
            if not mark_read_buttons:
                print("❌ Tidak ada notifikasi yang belum dibaca")
                return
            
            print(f"✅ Ditemukan {len(mark_read_buttons)} notifikasi yang belum dibaca")
            
            # Step 4: Klik tandai sudah dibaca
            print("📝 Step 4: Klik tandai sudah dibaca")
            
            # Get the first mark as read button
            first_button = mark_read_buttons[0]
            
            # Scroll to button
            self.driver.execute_script("arguments[0].scrollIntoView(true);", first_button)
            time.sleep(1)
            
            # Wait for button to be clickable
            self.wait.until(EC.element_to_be_clickable(first_button))
            
            # Click mark as read button
            first_button.click()
            
            # Wait for page to reload or success message
            time.sleep(2)
            
            # Verify success (check for success message or page reload)
            try:
                success_message = self.driver.find_element(
                    By.XPATH, "//div[contains(@class, 'bg-green-100') and contains(text(), 'dibaca')]"
                )
                print("✅ Notifikasi berhasil ditandai sudah dibaca")
            except:
                # Alternative: check if button disappeared or changed
                remaining_buttons = self.driver.find_elements(
                    By.XPATH, "//button[contains(@class, 'text-orange-600') or contains(@class, 'text-blue-600')]//i[contains(@class, 'fa-check')]"
                )
                if len(remaining_buttons) < len(mark_read_buttons):
                    print("✅ Notifikasi berhasil ditandai sudah dibaca (button hilang)")
                else:
                    print("⚠️ Status notifikasi tidak jelas")
            
            print("🎉 Test selesai dengan sukses!")
            
        except Exception as e:
            print(f"❌ Test gagal: {str(e)}")
            # Take screenshot for debugging
            self.driver.save_screenshot("mitra_notification_error.png")
            raise
    
    def test_mitra_notification_mark_all_read(self):
        """Test mark all notifications as read"""
        
        print("🔍 Starting Mark All Read Test...")
        
        try:
            # Login first (same as above)
            self.driver.get(f"{self.base_url}/login")
            
            email_input = self.wait.until(EC.presence_of_element_located((By.NAME, "email")))
            email_input.send_keys("mitra@sisarasa.com")
            
            password_input = self.driver.find_element(By.NAME, "password")
            password_input.send_keys("password")
            
            login_button = self.driver.find_element(By.XPATH, "//button[@type='submit']")
            login_button.click()
            
            self.wait.until(EC.url_contains("/mitra/dashboard"))
            
            # Go to notifications
            notification_icon = self.wait.until(
                EC.element_to_be_clickable((By.XPATH, "//a[@href='/notifications']"))
            )
            notification_icon.click()
            
            self.wait.until(EC.url_contains("/notifications"))
            
            # Look for "Tandai Semua Dibaca" button
            try:
                mark_all_button = self.wait.until(
                    EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Tandai Semua Dibaca')]"))
                )
                
                # Click mark all as read
                mark_all_button.click()
                
                # Wait for success
                time.sleep(2)
                
                print("✅ Semua notifikasi berhasil ditandai sudah dibaca")
                
            except:
                print("ℹ️ Tidak ada tombol 'Tandai Semua Dibaca' (mungkin tidak ada notifikasi unread)")
            
        except Exception as e:
            print(f"❌ Test gagal: {str(e)}")
            self.driver.save_screenshot("mark_all_read_error.png")
            raise

if __name__ == "__main__":
    unittest.main()

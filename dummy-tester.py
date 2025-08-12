# Impor library yang diperlukan dari Selenium
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
from webdriver_manager.chrome import ChromeDriverManager
import time
import os
import json

# --- Konfigurasi ---
BASE_URL = "http://localhost:8000"
VALID_EMAIL = "superadmin@gmail.com"
VALID_PASSWORD = "12345"
COMPANY_ID = "c34f666f-c29a-4403-9922-70a2c59a3f63"

class ProjectTester:
    def __init__(self):
        """Inisialisasi WebDriver dan setup folder screenshot."""
        if not os.path.exists("screenshots"):
            os.makedirs("screenshots")
            
        self.driver = webdriver.Chrome(service=ChromeService(ChromeDriverManager().install()))
        self.wait = WebDriverWait(self.driver, 15)
        self.session_data = {}
        
    def take_screenshot(self, test_name):
        """Mengambil screenshot dan menyimpannya dengan nama unik."""
        timestamp = time.strftime("%Y%m%d-%H%M%S")
        screenshot_name = f"screenshots/{test_name}_{timestamp}.png"
        self.driver.save_screenshot(screenshot_name)
        print(f"📸 Screenshot disimpan di: {screenshot_name}")
        return screenshot_name

    def login_once(self):
        """Login hanya sekali dan simpan session."""
        print("🔐 Melakukan login...")
        try:
            self.driver.get(f"{BASE_URL}/admin/login")
            print(f"Membuka halaman login: {self.driver.current_url}")

            # Tunggu dan isi form login
            email_input = self.wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "input[type='email']")))
            password_input = self.wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "input[type='password']")))
            
            email_input.send_keys(VALID_EMAIL)
            password_input.send_keys(VALID_PASSWORD)

            # Klik tombol login
            login_button = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
            login_button.click()

            # Tunggu redirect ke dashboard
            expected_url = f"{BASE_URL}/admin/{COMPANY_ID}"
            self.wait.until(EC.url_contains(expected_url))
            
            # Simpan cookies untuk session
            self.session_data['cookies'] = self.driver.get_cookies()
            print("✅ Login berhasil!")
            return True
            
        except Exception as e:
            print(f"❌ Login gagal: {e}")
            self.take_screenshot("login_failed")
            return False

    def navigate_to_projects(self):
        """Navigasi ke halaman projects."""
        print("📁 Navigasi ke halaman projects...")
        try:
            # Cari link Projects di sidebar
            projects_link = self.wait.until(
                EC.element_to_be_clickable((By.XPATH, "//a[contains(@href, 'projects') or contains(text(), 'Projects')]"))
            )
            projects_link.click()
            
            # Tunggu halaman projects load
            self.wait.until(EC.url_contains("projects"))
            print("✅ Berhasil masuk ke halaman projects")
            return True
            
        except Exception as e:
            print(f"❌ Gagal navigasi ke projects: {e}")
            self.take_screenshot("navigate_projects_failed")
            return False

    def create_new_project(self):
        """Membuat project baru."""
        print("➕ Membuat project baru...")
        try:
            # PASTIKAN KLIK TOMBOL TAMBAH TERLEBIH DAHULU
            # Mengubah pencarian dari <button> ke <a> yang lebih umum di Filament
            print("🎯 Mencari tombol/link 'Tambah Proyek Pemetaan Baru'...")
            create_button = self.wait.until(
                EC.element_to_be_clickable((By.XPATH, "//a[contains(., 'Tambah Proyek Pemetaan Baru')]"))
            )
            print("✅ Tombol ditemukan, mengklik...")
            create_button.click()

            # Tunggu hingga URL berubah ke halaman pembuatan project. Ini lebih andal daripada time.sleep().
            print("⏳ Menunggu halaman pembuatan project termuat...")
            self.wait.until(EC.url_contains("/projects/create"))
            print("✅ Halaman pembuatan project berhasil dimuat!")

            # Pengecekan form secara eksplisit tidak lagi diperlukan,
            # karena baris berikutnya akan menunggu elemen form pertama (nama_input).
            # Isi form project
            project_data = {
                "nama_project": f"Test Project {int(time.time())}",
                "status": "Prospect",
                "kategori_id": None,  # Will select first option
                "sales_id": None,     # Will select first option
                "tanggal_informasi_masuk": time.strftime("%Y-%m-%d"),
                "sumber": "Online",
                "nilai_project_awal": "1000000",
            }
            
            # Isi nama project
            nama_input = self.wait.until(EC.visibility_of_element_located((By.NAME, "nama_project")))
            nama_input.send_keys(project_data["nama_project"])
            
            # Isi status (non-native select)
            status_select = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//label[contains(text(),'Status Proyek')]/following-sibling::div//button")))
            status_select.click()
            status_option = self.wait.until(EC.element_to_be_clickable((By.XPATH, f"//ul[contains(@class,'p-1')]//li[contains(text(), '{project_data['status']}')]")))
            status_option.click()
            
            # Isi kategori_id (non-native select with relationship)
            kategori_select = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//label[contains(text(),'Kategori')]/following-sibling::div//button")))
            kategori_select.click()
            kategori_option = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//ul[contains(@class,'p-1')]//li[1]")))
            kategori_option.click()
            
            # Isi sales_id (non-native select with relationship)
            sales_select = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//label[contains(text(),'Sales')]/following-sibling::div//button")))
            sales_select.click()
            sales_option = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//ul[contains(@class,'p-1')]//li[1]")))
            sales_option.click()
            
            # Isi tanggal_informasi_masuk (date picker)
            tanggal_input = self.wait.until(EC.visibility_of_element_located((By.NAME, "tanggal_informasi_masuk")))
            tanggal_input.clear()
            tanggal_input.send_keys(project_data["tanggal_informasi_masuk"])
            tanggal_input.send_keys(Keys.RETURN)
            
            # Isi sumber (non-native select)
            sumber_select = self.wait.until(EC.element_to_be_clickable((By.XPATH, "//label[contains(text(),'Sumber')]/following-sibling::div//button")))
            sumber_select.click()
            sumber_option = self.wait.until(EC.element_to_be_clickable((By.XPATH, f"//ul[contains(@class,'p-1')]//li[contains(text(), '{project_data['sumber']}')]")))
            sumber_option.click()
            
            # Isi nilai_project_awal (numeric input)
            nilai_input = self.wait.until(EC.visibility_of_element_located((By.NAME, "nilai_project_awal")))
            nilai_input.send_keys(project_data["nilai_project_awal"])
            
            # Submit form
            save_button = self.wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "button[type='submit']")))
            save_button.click()
            
            # Tunggu redirect atau konfirmasi
            try:
                self.wait.until(EC.url_contains("projects"))
                print(f"✅ Project berhasil dibuat: {project_data['nama_project']}")
                
                # Verifikasi project muncul di list
                project_list = self.wait.until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, ".fi-ta-content"))
                )
                assert project_data["nama_project"] in project_list.text
                print("✅ Project muncul di daftar")
                
                return True
                
            except Exception as e:
                print(f"❌ Gagal menyimpan project: {e}")
                self.take_screenshot("create_project_save_failed")
                return False
                
        except Exception as e:
            print(f"❌ Gagal membuat project: {e}")
            self.take_screenshot("create_project_failed")
            return False

    def test_project_crud(self):
        """Test lengkap CRUD untuk project."""
        print("\n🚀 Memulai test CRUD untuk project...")
        
        # 1. Login (hanya sekali)
        if not self.login_once():
            return False
            
        # 2. Navigasi ke projects
        if not self.navigate_to_projects():
            return False
            
        # 3. Create project
        if not self.create_new_project():
            return False
            
        print("✅ Semua test CRUD project berhasil!")
        return True

    def run_all_tests(self):
        """Menjalankan semua test."""
        try:
            success = self.test_project_crud()
            if success:
                print("\n🎉 Semua test berhasil dijalankan!")
            else:
                print("\n⚠️ Beberapa test gagal, cek screenshot untuk detail")
                
        except Exception as e:
            print(f"\n❌ Error saat menjalankan test: {e}")
            self.take_screenshot("general_error")
            
        finally:
            time.sleep(3)  # Beri waktu untuk melihat hasil
            self.driver.quit()
            print("Browser ditutup.")

# --- Jalankan Pengujian Otomatis Saat File Dieksekusi ---
if __name__ == "__main__":
    tester = ProjectTester()
    tester.run_all_tests()

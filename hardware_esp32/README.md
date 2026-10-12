# Panduan Integrasi Hardware ESP32 & AS608 Fingerprint ke MyFin

Firmware ini digunakan pada modul IoT (ESP32 NodeMCU) yang terpasang di Kios Pemesanan Mandiri / Tablet POS Kantin MyFin.

## 1. Komponen yang Dibutuhkan
1. **ESP32 NodeMCU** (30 pin / 38 pin)
2. **Sensor Sidik Jari Optik AS608** (atau FPM10A / R307)
3. **Layar OLED 0.96" I2C SSD1306** (128x64)
4. **Buzzer Aktif 5V / 3.3V** & **Resistor 220 Ohm**
5. **Kabel Jumper** (Female to Female)

---

## 2. Skema Pengkabelan (Wiring Pinout)

| Komponen | Pin Komponen | Pin ESP32 | Keterangan |
|---|---|---|---|
| **AS608 Fingerprint** | VCC (Merah) | 3.3V atau 5V (VIN) | Daya Sensor |
| | GND (Hitam) | GND | Ground |
| | TX (Kuning) | GPIO 16 (RX2) | Serial2 Receive |
| | RX (Hijau) | GPIO 17 (TX2) | Serial2 Transmit |
| **OLED SSD1306** | VCC | 3.3V | Daya Display |
| | GND | GND | Ground |
| | SDA | GPIO 21 | I2C Data |
| | SCL | GPIO 22 | I2C Clock |
| **Buzzer** | VCC (+) | GPIO 4 | Indikator Suara |
| | GND (-) | GND | Ground |

---

## 3. Library Arduino IDE yang Diperlukan
Buka **Arduino IDE** -> Menu **Tools** -> **Manage Libraries...**, lalu install library berikut:
1. `Adafruit Fingerprint Sensor Library` by Adafruit
2. `ArduinoJson` by Benoit Blanchon (Versi 6.x atau 7.x)
3. `Adafruit SSD1306` by Adafruit
4. `Adafruit GFX Library` by Adafruit

---

## 4. Cara Menjalankan
1. Pastikan laptop yang menjalankan Laravel MyFin terhubung ke jaringan Wi-Fi yang sama dengan ESP32.
2. Cek IP lokal laptop Anda di terminal (`ipconfig` di Windows, contoh: `192.168.1.50`).
3. Buka file `myfin_esp32_fingerprint.ino`.
4. Sesuaikan konfigurasi berikut:
   ```cpp
   const char* WIFI_SSID     = "Nama_WiFi_Anda";
   const char* WIFI_PASSWORD = "Password_WiFi";
   const char* SERVER_URL    = "http://192.168.1.50:8000/api/biometric/verify";
   ```
5. Pilih Board: **ESP32 Dev Module**, pilih COM Port, lalu klik **Upload**.
6. Saat pesanan dibuat di Tablet Kios web (`http://localhost:8000`), pembeli cukup menempelkan jarinya ke sensor AS608.
7. ESP32 membaca ID sidik jari, mengirim request ke `/api/biometric/verify`, memicu pemotongan saldo Midtrans secara otomatis, dan menampilkan status sukses beserta nama mahasiswa di layar OLED!
# ==============================================================================
# SIAT POLINEMA — PENDAFTARAN SIDIK JARI (FINGERPRINT ENROLLMENT)
# Platform: ESP32-S3 (MicroPython di Thonny IDE)
# ==============================================================================

import time
import sys
from machine import Pin, UART, I2C
from i2c_lcd import I2cLcd
from as608 import AS608

ENROLL_ID = 1  # Daftarkan ke ID 1 (Chandra Oktavino)

# ─── PIN ESP32-S3 ─────────────────────────────────────────────────────────────
I2C_SDA_PIN = 8
I2C_SCL_PIN = 9
UART_TX_PIN = 17
UART_RX_PIN = 18

try:
    i2c = I2C(0, sda=Pin(I2C_SDA_PIN), scl=Pin(I2C_SCL_PIN), freq=400000)
    devices = i2c.scan()
    lcd = I2cLcd(i2c, devices[0], 2, 16) if devices else None
except:
    lcd = None

def show(l1, l2=""):
    print(f">>> {l1} | {l2}")
    if lcd:
        try:
            lcd.clear()
            lcd.move_to(0, 0)
            lcd.putstr(l1[:16])
            if l2:
                lcd.move_to(0, 1)
                lcd.putstr(l2[:16])
        except:
            pass

uart = UART(1, baudrate=57600, tx=Pin(UART_TX_PIN), rx=Pin(UART_RX_PIN))
finger = AS608(uart)

print("=" * 55)
print(f"   PENDAFTARAN SIDIK JARI KE ID: {ENROLL_ID}")
print("   TIPS: Gunakan JARI TELUNJUK (lebih mudah presisi)")
print("=" * 55)

show("DAFTAR SIDIK JARI", f"Ke ID: {ENROLL_ID}")
time.sleep(2)

def do_enroll():
    while True:
        # ─── LANGKAH 1 ──────────────────────────────────────────
        show("Tempel Jari...", "(Tekan Rata)")
        print("\n[Langkah 1] Letakkan jari TELUNJUK rata di tengah sensor...")
        
        # Tunggu sampai ada jari
        while not finger.get_image():
            time.sleep_ms(100)
            
        print("-> Foto 1 OK!")
        if not finger.image_2_tz(slot=1):
            show("Gambar Buram", "Coba Lagi...")
            time.sleep(1)
            continue
            
        show("OK! ANGKAT JARI", "Sebentar...")
        print("-> Angkat jarimu sekarang...")
        time.sleep(1)
        
        # Tunggu sampai jari diangkat
        while finger.get_image():
            time.sleep_ms(100)
            
        time.sleep_ms(500)
        
        # ─── LANGKAH 2 ──────────────────────────────────────────
        show("Tempel Lagi...", "Posisi Sama")
        print("[Langkah 2] Tempelkan jari yang SAMA dengan posisi & tekanan yang SAMA...")
        
        # Tunggu jari ditempelkan lagi
        while not finger.get_image():
            time.sleep_ms(100)
            
        print("-> Foto 2 OK!")
        if not finger.image_2_tz(slot=2):
            show("Gambar Buram", "Coba Lagi...")
            time.sleep(1)
            continue
            
        # ─── LANGKAH 3 ──────────────────────────────────────────
        show("Mencocokkan...", "Mohon tunggu")
        print("[Langkah 3] Mencocokkan kedua foto...")
        time.sleep_ms(300)
        
        if finger.create_model():
            print(f"-> COCOK! Menyimpan ke ID {ENROLL_ID}...")
            if finger.store_model(page_id=ENROLL_ID, slot=1):
                show("SUKSES TERDAFTAR!", f"ID: {ENROLL_ID} (Chandra)")
                print("\n" + "=" * 55)
                print(f"🎉 ALHAMDULILLAH! Sidik jari berhasil disimpan ke ID {ENROLL_ID}!")
                print("Sekarang buka dan klik RUN pada file main.py untuk transaksi!")
                print("=" * 55)
                break
            else:
                show("Gagal Simpan", "Coba Lagi")
                print("Gagal menyimpan ke flash sensor.")
        else:
            show("Posisi Meleset", "Ulangi Lagi...")
            print("⚠️ Sudut/posisi jari agak berbeda. Coba letakkan lebih rata di tengah sensor.")
            time.sleep(2)

do_enroll()

# ==============================================================================
# MYFIN - SISTEM KASIR KANTIN BIOMETRIK & MIDTRANS
# Platform : ESP32-S3 (MicroPython di Thonny IDE)
# Display  : Layar Tablet POS Kios (Layar LCD Fisik Ditiadakan Permanen)
# Fitur    :
# 1. Pendaftaran Sidik Jari Real-Time Dipicu dari Website Mahasiswa
# 2. Sinkronisasi Real-Time Dua Arah ke Layar POS Tablet Kios
# 3. Auto-Debit Resmi Berstandar PCI-DSS Midtrans Core API
# ==============================================================================

import time
import network
import json
import usocket as socket
from machine import Pin, UART
from as608 import AS608

# ── 1. KONFIGURASI WIFI & SERVER ──
WIFI_SSID     = "Farrel"                  # Nama Wi-Fi Anda
WIFI_FALLBACK = "Wifi Farrel"             # Cadangan jika SSID ada spasi
WIFI_PASSWORD = "12345678"                # Password Wi-Fi

SERVER_HOST   = "43.134.68.142"           # VPS (dulu: 10.244.111.225 laptop)
DEVICE_ID     = "esp32-kios-01"

# ── 2. KONFIGURASI PIN ESP32-S3 (HANYA SENSOR AS608) ──
# Device IoT murni: Hanya ESP32-S3 dan Sensor Sidik Jari AS608.
# Tidak ada LCD fisik (tampilan di Layar POS Tablet Kios).
# Tidak ada Buzzer fisik (efek suara transaksi diputar langsung dari Web/Tablet).
UART_TX_PIN   = 17    # Pin RX Sensor AS608
UART_RX_PIN   = 18    # Pin TX Sensor AS608

# ── 3. INISIALISASI PERANGKAT KERAS ──
def beep(duration_ms=80):
    pass

def beep_success():
    pass

def beep_error():
    pass

def log_status(line1, line2=""):
    """Terminal log ringkas (tampilan utama pelanggan ada di Layar POS Tablet Kios)."""
    if line1 not in ["MYFIN KIOSK", "Inisialisasi...", "Standby"]:
        if line2:
            print(f"[ESP32] {line1} - {line2}")
        else:
            print(f"[ESP32] {line1}")

lcd_show = log_status

# Inisialisasi Sensor AS608
uart = UART(1, baudrate=57600, tx=Pin(UART_TX_PIN), rx=Pin(UART_RX_PIN))
finger = AS608(uart)

# ── 4. KONEKSI WI-FI RINGKAS ──
def connect_wifi():
    wlan = network.WLAN(network.STA_IF)
    if not wlan.active():
        wlan.active(True)
        time.sleep_ms(100)

    if wlan.isconnected():
        return True

    for ssid in [WIFI_SSID, WIFI_FALLBACK]:
        if not ssid:
            continue
        try:
            wlan.disconnect()
            time.sleep_ms(50)
        except:
            pass
        wlan.connect(ssid, WIFI_PASSWORD)
        for _ in range(25):
            if wlan.isconnected():
                return True
            time.sleep_ms(200)

    return False

# ── 5. FUNGSI HTTP REQUEST HELPER ──
SERVER_ADDR = None

def get_server_addr():
    global SERVER_ADDR
    if SERVER_ADDR is None:
        try:
            SERVER_ADDR = socket.getaddrinfo(SERVER_HOST, SERVER_PORT)[0][-1]
        except Exception:
            SERVER_ADDR = (SERVER_HOST, SERVER_PORT)
    return SERVER_ADDR

def http_request(path, method="GET", body=None, timeout=2):
    s = None
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        s.settimeout(timeout)
        s.connect(get_server_addr())

        payload_bytes = json.dumps(body).encode("utf-8") if body else b""
        req = (
            f"{method} {path} HTTP/1.0\r\n"
            f"Host: {SERVER_HOST}:{SERVER_PORT}\r\n"
            "Content-Type: application/json\r\n"
            "Accept: application/json\r\n"
            f"Content-Length: {len(payload_bytes)}\r\n"
            "Connection: close\r\n\r\n"
        ).encode("utf-8") + payload_bytes

        s.send(req)

        response = b""
        content_len = None

        while True:
            chunk = s.recv(512)
            if not chunk:
                break
            response += chunk

            # Cek Content-Length agar selesai INSTAN tanpa nunggu socket timeout
            if content_len is None and b"\r\n\r\n" in response:
                parts = response.split(b"\r\n\r\n", 1)
                for h_line in parts[0].split(b"\r\n"):
                    if h_line.lower().startswith(b"content-length:"):
                        try:
                            content_len = int(h_line.split(b":", 1)[1].strip())
                        except Exception:
                            pass

            if content_len is not None and b"\r\n\r\n" in response:
                body_len = len(response.split(b"\r\n\r\n", 1)[1])
                if body_len >= content_len:
                    break

        s.close()
        s = None

        resp_str = response.decode("utf-8")
        if "\r\n\r\n" in resp_str:
            data_part = resp_str.split("\r\n\r\n", 1)[1]
            return json.loads(data_part)
        return None
    except Exception as e:
        if s:
            try:
                s.close()
            except Exception:
                pass
        return None

# ── 6. PENDAFTARAN SIDIK JARI (ENROLLMENT) ──
def report_step(step, attempt):
    """Lapor langkah ke web secara real-time. Return False jika dibatalkan."""
    res = http_request("/api/biometric/enroll-progress", "POST", {"step": step, "attempt": attempt}, timeout=2)
    if res and res.get("cancelled"):
        return False
    return True

def is_cancelled():
    """Cek ke server apakah tombol Batal ditekan di web."""
    res = http_request("/api/biometric/enroll-status", "GET", timeout=2)
    if res is None:
        return False
    return res.get("status") not in ("waiting_device", "scanning")

def wait_finger(timeout_s=20):
    """Tunggu jari menempel. Return 'ok' / 'timeout' / 'cancel'."""
    start = time.ticks_ms()
    last_check = start
    while time.ticks_diff(time.ticks_ms(), start) < timeout_s * 1000:
        if finger.get_image():
            return "ok"
        if time.ticks_diff(time.ticks_ms(), last_check) >= 1500:
            if is_cancelled():
                return "cancel"
            last_check = time.ticks_ms()
        time.sleep_ms(50)
    return "timeout"

def wait_lift(timeout_s=10):
    """Tunggu jari diangkat."""
    start = time.ticks_ms()
    while finger.get_image():
        if time.ticks_diff(time.ticks_ms(), start) > timeout_s * 1000:
            break
        time.sleep_ms(50)
    time.sleep_ms(300)

def capture(slot):
    """Konversi gambar ke template."""
    if finger.image_2_tz(slot=slot):
        return True
    time.sleep_ms(100)
    return finger.get_image() and finger.image_2_tz(slot=slot)

def show_cancelled():
    print("[ENROLL] Dibatalkan dari website")
    log_status("DIBATALKAN", "dari Website")
    beep(200)
    time.sleep(1.5)

def enroll_fail(user_id, slot_id, msg, l1, l2):
    log_status(l1, l2)
    beep_error()
    http_request("/api/biometric/enroll-finish", "POST", {"user_id": user_id, "slot_id": slot_id, "success": False, "message": msg})
    time.sleep(2)

def execute_enroll(session_data):
    """Dipicu dari Website Mahasiswa."""
    student_name = session_data.get("name", "Mahasiswa")
    slot_id = session_data.get("slot_id", 1)
    user_id = session_data.get("user_id", 1)
    first_name = (student_name.split()[0] if student_name else "Mhs")[:8]
    MAX_ATTEMPT = 3

    print(f"\n[ENROLL] {student_name} -> Slot #{slot_id}")
    beep(120)

    for attempt in range(1, MAX_ATTEMPT + 1):
        # 1. Tempel jari pertama
        log_status(f"DAFTAR: {first_name}", f"Tempel Jari ({attempt}/3)")
        if not report_step("scan1" if attempt == 1 else "retry", attempt):
            return show_cancelled()
        r = wait_finger()
        if r == "cancel":
            return show_cancelled()
        if r == "timeout":
            return enroll_fail(user_id, slot_id, "Waktu habis, jari tidak ditempelkan", "WAKTU HABIS!", "Dibatalkan")
        if not capture(1):
            log_status("Gambar Buram", "Angkat & Ulangi")
            beep_error()
            wait_lift()
            continue

        # 2. Minta angkat jari
        log_status("OK! ANGKAT JARI", "Terekam 1/2")
        beep(60)
        report_step("lift", attempt)
        wait_lift()

        # 3. Tempel jari kedua
        log_status("Tempel Lagi...", "Posisi Sama")
        if not report_step("scan2", attempt):
            return show_cancelled()
        r = wait_finger()
        if r == "cancel":
            return show_cancelled()
        if r == "timeout":
            return enroll_fail(user_id, slot_id, "Waktu habis, jari tidak ditempelkan", "WAKTU HABIS!", "Dibatalkan")
        if not capture(2):
            log_status("Gambar Buram", "Angkat & Ulangi")
            beep_error()
            wait_lift()
            continue

        # 4. Simpan model sidik jari
        log_status("Terekam 2/2", "Mencocokkan...")
        beep(60)
        if finger.create_model() and finger.store_model(page_id=slot_id, slot=1):
            print(f"[ENROLL SUKSES] Slot #{slot_id}")
            log_status("STATUS: BERHASIL", f"ID #{slot_id} Tersimpan")
            beep_success()
            http_request("/api/biometric/enroll-finish", "POST", {"user_id": user_id, "slot_id": slot_id, "success": True})
            time.sleep(2.5)
            return

        print(f"[ENROLL] Percobaan {attempt} gagal, ulangi...")
        log_status("Tidak Cocok", "Angkat & Ulangi")
        beep_error()
        wait_lift()
        time.sleep_ms(500)

    # Gagal setelah 3x percobaan
    enroll_fail(user_id, slot_id, "Sidik jari tidak cocok setelah 3x percobaan", "STATUS: GAGAL", "Coba dari Web")

# ── 7. MAIN LOOP ──
if connect_wifi():
    print(f"[{DEVICE_ID}] Online | Standby")
else:
    print(f"[{DEVICE_ID}] Offline")

current_mode = "idle"
current_order_id = ""
last_poll_time_ms = 0

while True:
    try:
        now_ms = time.ticks_ms()

        # Interval adaptif: saat idle 250ms (cepat sambut order), saat bayar 1500ms
        poll_interval = 250 if current_mode == "idle" else 1500

        if time.ticks_diff(now_ms, last_poll_time_ms) >= poll_interval:
            last_poll_time_ms = now_ms
            status_data = http_request(f"/api/esp32/status?device_id={DEVICE_ID}", "GET", timeout=2)

            if status_data:
                mode = status_data.get("mode", "idle")

                # KASUS A: Mahasiswa klik "Daftarkan Sidik Jari" di Website
                if mode == "enroll":
                    enroll_session = status_data.get("enroll", {})
                    execute_enroll(enroll_session)
                    current_mode = "idle"
                    current_order_id = ""
                    continue

                # KASUS B: Ada Transaksi Pesanan di Kios Kasir (Layar Tablet)
                elif mode == "payment":
                    total = status_data.get("total_amount", 0)
                    current_order_id = status_data.get("order_id", "")
                    if current_mode != "payment":
                        current_mode = "payment"
                        total_str = f"Rp {int(total):,}".replace(",", ".")
                        log_status(total_str, "Tempel Jari!")
                        beep(60)

                # KASUS C: Standby / Idle
                elif mode == "idle":
                    if current_mode != "idle":
                        current_mode = "idle"
                        current_order_id = ""
                        log_status("MYFIN KIOSK", "Standby")

        # Cek apakah ada jari menempel di sensor AS608 (HANYA saat mode pembayaran aktif)
        if current_mode == "payment" and finger.get_image():
            print("[AS608] Jari terdeteksi!")

            # 1. Laporkan langsung ke Layar Tablet POS agar status di web berubah 'MEMPROSES JARI..' instan (<0.1s)
            try:
                http_request("/api/biometric/payment-progress", "POST", {
                    "order_id": current_order_id,
                    "status": "processing",
                    "line1": "MEMPROSES JARI..",
                    "line2": "Mencocokkan sidik jari & menghubungi bank..."
                }, timeout=1)
            except:
                pass

            log_status("Memproses Jari..", "Harap Tunggu...")

            # 2. Validasi citra sidik jari
            if finger.image_2_tz(slot=1):
                finger_id, score = finger.search(start=0, count=50)

                if finger_id is not None and score > 25:
                    print(f"[AS608] Cocok di Slot #{finger_id} (Score: {score})")

                    # 3. Kirim ke Backend untuk Auto-Debit Midtrans Core API
                    res = http_request("/api/biometric/verify", "POST", {
                        "device_id": DEVICE_ID,
                        "fingerprint_id": finger_id,
                        "order_id": current_order_id
                    }, timeout=10)

                    status_res = res.get("status") if res else ""

                    if status_res == "success":
                        beep_success()
                        nama = res.get("student", {}).get("name", "Pelanggan")
                        total = res.get("transaction", {}).get("total_amount", 0)
                        print(f"[SUKSES] Terdebet Rp {total} untuk {nama}")
                        total_fmt = f"{int(total):,}".replace(",", ".")
                        log_status("PEMBAYARAN LUNAS", f"Rp {total_fmt}")
                        time.sleep(3.5)

                    elif status_res == "identified":
                        beep(50)
                        nama = res.get("student", {}).get("name", "Mahasiswa")[:16]
                        print(f"[IDENTIFIKASI] {nama} (Tidak ada order aktif)")
                        log_status(f"Halo, {nama[:10]}", "Pilih Menu Dulu")
                        time.sleep(2.5)

                    else:
                        beep_error()
                        pesan = res.get("message", "Ditolak") if res else "Server Timeout"
                        print(f"[GAGAL] {pesan}")
                        log_status("STATUS: DITOLAK", "Gagal Diproses")
                        time.sleep(2.5)

                else:
                    print("[AS608] Sidik jari belum terdaftar!")
                    # Lapor ke Layar POS Tablet: Jari tidak cocok
                    try:
                        http_request("/api/biometric/payment-progress", "POST", {
                            "order_id": current_order_id,
                            "status": "rejected",
                            "line1": "STATUS: DITOLAK",
                            "line2": "Sidik jari belum terdaftar di sistem"
                        }, timeout=1)
                    except:
                        pass

                    beep_error()
                    log_status("STATUS: DITOLAK", "Jari Tak Cocok")
                    time.sleep(2.5)

            else:
                # Posisi jari kurang pas
                try:
                    http_request("/api/biometric/payment-progress", "POST", {
                        "order_id": current_order_id,
                        "status": "processing",
                        "line1": "ULANGI TEMPEL",
                        "line2": "Jari kurang pas pada sensor pemindai"
                    }, timeout=1)
                except:
                    pass

                log_status("ULANGI TEMPEL", "Jari Kurang Pas")
                time.sleep(1)

            current_mode = "idle"
            current_order_id = ""

        time.sleep_ms(10)

    except KeyboardInterrupt:
        print("\n[STOP] Dihentikan pengguna.")
        break
    except Exception as e:
        print(f"[Loop Err]: {e}")
        time.sleep(1)

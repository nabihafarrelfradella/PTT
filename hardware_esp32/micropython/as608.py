# MicroPython AS608 / R307 Fingerprint Sensor Driver
import time

class AS608:
    START_CODE = b'\xef\x01'
    DEFAULT_ADDR = b'\xff\xff\xff\xff'
    
    CMD_GEN_IMG = 0x01
    CMD_IMG2TZ = 0x02
    CMD_SEARCH = 0x04

    def __init__(self, uart):
        self.uart = uart
        self.address = self.DEFAULT_ADDR

    def _send_packet(self, pid, data):
        length = len(data) + 2
        checksum = pid + (length >> 8) + (length & 0xFF) + sum(data)
        packet = (
            self.START_CODE +
            self.address +
            bytes([pid, length >> 8, length & 0xFF]) +
            bytes(data) +
            bytes([checksum >> 8, checksum & 0xFF])
        )
        # Clear buffer
        while self.uart.any():
            self.uart.read()
        self.uart.write(packet)

    def _read_packet(self, timeout_ms=1000):
        start = time.ticks_ms()
        # Read header (9 bytes min: start(2) + addr(4) + pid(1) + len(2))
        buf = bytearray()
        while len(buf) < 9:
            if time.ticks_diff(time.ticks_ms(), start) > timeout_ms:
                return None, None
            if self.uart.any():
                buf += self.uart.read(1)

        if buf[:2] != self.START_CODE:
            return None, None

        pid = buf[6]
        length = (buf[7] << 8) | buf[8]

        # Read data + checksum
        data_rem = bytearray()
        while len(data_rem) < length:
            if time.ticks_diff(time.ticks_ms(), start) > timeout_ms:
                return None, None
            if self.uart.any():
                data_rem += self.uart.read(1)

        payload = data_rem[:-2]
        return pid, payload

    def get_image(self):
        """Ambil gambar sidik jari (returns True jika ada jari menempel)"""
        self._send_packet(0x01, [self.CMD_GEN_IMG])
        _, res = self._read_packet(500)
        if res and len(res) >= 1:
            return res[0] == 0x00  # 0x00 = OK
        return False

    def image_2_tz(self, slot=1):
        """Konversi gambar ke template karakter di slot 1 atau 2"""
        self._send_packet(0x01, [self.CMD_IMG2TZ, slot])
        _, res = self._read_packet(500)
        if res and len(res) >= 1:
            return res[0] == 0x00
        return False

    def search(self, start=0, count=50):
        """Cari template sidik jari di memori AS608"""
        data = [
            self.CMD_SEARCH,
            0x01, # slot
            (start >> 8) & 0xFF, start & 0xFF,
            (count >> 8) & 0xFF, count & 0xFF
        ]
        self._send_packet(0x01, data)
        _, res = self._read_packet(1000)
        if res and len(res) >= 5 and res[0] == 0x00:
            finger_id = (res[1] << 8) | res[2]
            score = (res[3] << 8) | res[4]
            return finger_id, score
        return None, 0

    def create_model(self):
        """Gabungkan slot 1 dan slot 2 menjadi template model sidik jari"""
        self._send_packet(0x01, [0x05])
        _, res = self._read_packet(1000)
        if res and len(res) >= 1:
            return res[0] == 0x00
        return False

    def store_model(self, page_id=1, slot=1):
        """Simpan template dari slot ke memori flash sensor pada nomor ID tertentu"""
        data = [
            0x06,
            slot,
            (page_id >> 8) & 0xFF, page_id & 0xFF
        ]
        self._send_packet(0x01, data)
        _, res = self._read_packet(1000)
        if res and len(res) >= 1:
            return res[0] == 0x00
        return False
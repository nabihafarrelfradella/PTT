@extends('mahasiswa.layout')

@section('title', 'Dashboard')
@section('header_title', 'Selamat Datang, ' . $user->name . '!')
@section('header_subtitle', 'Sistem Pembayaran Biometrik Kantin PSDKU')

@section('styles')
<style>
    .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; align-items: stretch; }
    .status-card { background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; display: flex; flex-direction: column; box-shadow: 0 1px 3px rgba(0,0,0,0.05); height: 100%; }
    .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
    .card-header h3 { font-size: 0.95rem; font-weight: 600; color: var(--text-muted); }
    .card-content { flex: 1; display: flex; flex-direction: column; justify-content: flex-start; margin-bottom: 1.25rem; }
    .card-content h2 { font-size: 1.4rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem; }
    .card-content p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.45; }
    .status-card .action-btn { margin-top: auto; width: 100%; box-sizing: border-box; }
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
    .modal-overlay.active { opacity: 1; pointer-events: all; }
    .modal-content { background: var(--surface-color); padding: 1.25rem 1.4rem; border-radius: 20px; max-width: 420px; width: 92%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: auto; }
</style>
@endsection

@section('content')
<div class="cards-grid">
    <!-- Status Rekening Debit -->
    <div class="status-card">
        <div class="card-header">
            <h3>Rekening Debit (Midtrans Auto-Debit)</h3>
            @if($debit && $debit->is_active)
                <span class="status-badge active">Terhubung</span>
            @else
                <span class="status-badge warning">Belum Ada</span>
            @endif
        </div>
        <div class="card-content">
            @if($debit && $debit->is_active)
                <h2>{{ $debit->bank_name }}</h2>
                <p>{{ $debit->card_token_masked }}</p>
                <p style="font-size: 0.75rem; color: #16a34a; margin-top: 6px; display: flex; align-items: center; gap: 4px; font-weight: 500;">
                    <svg style="width: 14px; height: 14px; color: #16a34a; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Siap untuk One-Click Auto Debit
                </p>
            @else
                <h2>Belum Terhubung</h2>
                <p>Tautkan kartu debit/rekening untuk aktivasi checkout POS.</p>
            @endif
        </div>
        <a href="{{ route('mahasiswa.pembayaran') }}" class="action-btn {{ $debit ? 'secondary' : '' }}">
            {{ $debit ? 'Kelola Kartu Debit' : 'Tautkan Kartu Debit' }}
        </a>
    </div>

    <!-- Status Biometrik AS608 -->
    <div class="status-card">
        <div class="card-header">
            <h3>Autentikasi Biometrik</h3>
            @if($biometric)
                <span id="bioCardBadge" class="status-badge active">Aktif</span>
            @else
                <span id="bioCardBadge" class="status-badge warning">Belum Terhubung</span>
            @endif
        </div>
        <div class="card-content">
            @if($biometric)
                <h2 id="bioCardTitle">Sidik Jari Terhubung</h2>
                <p id="bioCardDesc">Metode biometrik aktif untuk otorisasi pembayaran instan di kasir.</p>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px;">
                    <span id="bioCardIdBadge" style="font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-weight: 600; font-family: monospace;">ID #{{ sprintf('%02d', $biometric->fingerprint_id ?? 1) }}</span>
                    <span id="bioCardTime" style="font-size: 0.75rem; color: var(--text-muted);">Terdaftar: {{ \Carbon\Carbon::parse($biometric->registered_at)->format('d M Y, H:i') }}</span>
                </div>
            @else
                <h2 id="bioCardTitle">Belum Terhubung</h2>
                <p id="bioCardDesc">Tautkan sidik jari Anda ke sensor scanner untuk transaksi cepat tanpa kartu.</p>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 6px;">
                    <span id="bioCardIdBadge" style="display: none; font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 6px; font-weight: 600; font-family: monospace;"></span>
                    <span id="bioCardTime" style="font-size: 0.75rem; color: var(--text-muted); display: none;"></span>
                </div>
            @endif
        </div>
        <button id="bioCardBtn" class="action-btn {{ $biometric ? 'secondary' : '' }}" onclick="openBioModal()">
            {{ $biometric ? 'Perbarui Sidik Jari' : 'Daftarkan Sidik Jari' }}
        </button>
    </div>
</div>



<!-- Preview Transaksi Terakhir -->
<div class="section-title">
    <span>5 Transaksi Terakhir</span>
    <a href="{{ route('mahasiswa.riwayat') }}" style="font-size: 0.85rem; color: var(--accent-color); text-decoration: none;">Lihat Semua &rarr;</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>Stand / Merchant</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions->take(5) as $tx)
            <tr>
                <td style="white-space: nowrap;">{{ $tx->created_at->format('d M Y, H:i') }}</td>
                <td style="white-space: nowrap;">{{ $tx->transaction_code }}</td>
                <td style="white-space: nowrap;">{{ $tx->kantin->nama_kantin ?? 'Kantin PSDKU' }}</td>
                <td style="font-weight: 600; white-space: nowrap;">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                <td style="white-space: nowrap;">
                    @if($tx->status === 'success')
                        <span class="status-badge active">Berhasil</span>
                    @elseif($tx->status === 'pending')
                        <span class="status-badge pending">Menunggu</span>
                    @elseif($tx->status === 'cancelled')
                        <span class="status-badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">Dibatalkan</span>
                    @else
                        <span class="status-badge danger">Gagal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                    Belum ada transaksi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Pendaftaran Sidik Jari AS608 Real-Time (Direct On-Screen Guidance) -->
<div class="modal-overlay" id="registerBioModal">
    <div class="modal-content" style="max-width: 420px; width: 92%; text-align: center; padding: 1.25rem 1.4rem; overflow: hidden;">
        <div id="bioIconContainer" style="position: relative; width: 60px; height: 60px; margin: 0 auto 0.65rem; display: flex; align-items: center; justify-content: center; background: #eff6ff; border-radius: 50%; color: var(--accent-color); transition: all .3s;">
            <div id="bioPulse" style="position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 2px solid #3b82f6; animation: bioPing 1.5s cubic-bezier(0, 0, 0.2, 1) infinite; opacity: 0.75;"></div>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; position: relative; z-index: 10;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
        </div>

        <h2 id="bioModalTitle" style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--primary-color);">Perekaman Sidik Jari</h2>
        <p id="bioModalSubtitle" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem; line-height: 1.35;">
            Silakan ikuti instruksi panduan di bawah ini pada scanner.
        </p>

        <!-- Indikator Langkah Pendaftaran -->
        <div id="bioStepIndicator" style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 0.75rem;">
            <div id="stepDot1" style="display: flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px; background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: 700; border: 1.5px solid #bfdbfe; transition: all .3s;">
                <span id="stepBullet1" style="width: 6px; height: 6px; border-radius: 50%; background: #2563eb;"></span> 1. Scan Awal
            </div>
            <svg style="width: 12px; height: 12px; color: #cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <div id="stepDot2" style="display: flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px; background: #f8fafc; color: #94a3b8; font-size: 0.75rem; font-weight: 600; border: 1.5px solid #e2e8f0; transition: all .3s;">
                <span id="stepBullet2" style="width: 6px; height: 6px; border-radius: 50%; background: #cbd5e1;"></span> 2. Konfirmasi
            </div>
        </div>

        <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 0.75rem 0.95rem; margin-bottom: 0.85rem; text-align: left; transition: all .3s;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Status Perangkat</span>
                <span id="bioDeviceBadge" style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 999px; font-weight: 600;">● Menghubungkan</span>
            </div>
            <div id="bioStepText" style="font-size: 0.875rem; font-weight: 700; color: #1e293b; margin-bottom: 2px;">Menghubungkan ke pemindai...</div>
            <div id="bioHintText" style="font-size: 0.75rem; color: #64748b; line-height: 1.3;">Pastikan scanner biometrik di kasir aktif dan terhubung.</div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="button" id="bioCancelBtn" class="action-btn secondary" style="flex: 1; padding: 0.65rem;" onclick="closeBioModal()">Batal</button>
            <button type="button" id="bioRetryBtn" class="action-btn" style="flex: 1; padding: 0.65rem; display: none;" onclick="startRealtimeEnrollment()">Coba Lagi</button>
            <button type="button" id="bioDoneBtn" class="action-btn" style="flex: 1; padding: 0.65rem; display: none; background: #059669; border-color: #059669;" onclick="finishBioModal()">Selesai</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
@keyframes bioPing { 75%, 100% { transform: scale(1.6); opacity: 0; } }
</style>
<script>
    let enrollPollInterval = null;
    const currentUserId = {{ $user->id }};
    const STEP_TEXT = {
        // Step mapping real-time dari ESP32 (Layar Web sebagai pemandu utama)
        step1_wait:    ['Tempelkan Jari Anda', 'Letakkan jari telunjuk rata pada sensor scanner meja kios.'],
        step1_process: ['Membaca Sidik Jari...', 'Tahan posisi jari sejenak pada kaca sensor...'],
        step2_lift:    ['Angkat Jari Anda', 'Lepaskan jari sejenak dari sensor scanner.'],
        step2_wait:    ['Tempelkan Jari Sekali Lagi', 'Tempelkan jari yang sama untuk konfirmasi verifikasi.'],
        step2_process: ['Menyimpan Model Biometrik...', 'Sedang menyimpan model sidik jari ke sistem...'],
        
        // Cadangan format fallback
        scan1:         ['Tempelkan Jari Anda', 'Letakkan jari telunjuk rata pada sensor scanner meja kios.'],
        lift:          ['Angkat Jari Anda', 'Lepaskan jari sejenak dari sensor scanner.'],
        scan2:         ['Tempelkan Jari Sekali Lagi', 'Tempelkan jari yang sama untuk konfirmasi verifikasi.'],
        process:       ['Menyimpan Sidik Jari...', 'Mohon tunggu sebentar...'],
        retry:         ['Hasil Kurang Jelas, Ulangi', 'Tempelkan kembali jari Anda dari awal.']
    };

    function setBadge(text, bg, color) {
        const b = document.getElementById('bioDeviceBadge');
        b.textContent = text; b.style.background = bg; b.style.color = color;
    }
    function setStep(step, hint) {
        document.getElementById('bioStepText').textContent = step;
        document.getElementById('bioHintText').textContent = hint;
    }
    function setIcon(state) {
        const ic = document.getElementById('bioIconContainer');
        const pulse = document.getElementById('bioPulse');
        if (state === 'success') { ic.style.background = '#dcfce7'; ic.style.color = '#16a34a'; pulse.style.display = 'none'; }
        else if (state === 'failed') { ic.style.background = '#fee2e2'; ic.style.color = '#dc2626'; pulse.style.display = 'none'; }
        else { ic.style.background = '#eff6ff'; ic.style.color = 'var(--accent-color)'; pulse.style.display = 'block'; }
    }

    function openBioModal() {
        document.getElementById('registerBioModal').classList.add('active');
        startRealtimeEnrollment();
    }

    function closeBioModal() {
        if (enrollPollInterval) { clearInterval(enrollPollInterval); enrollPollInterval = null; }
        // Kirim sinyal batal -> ESP32 mendeteksi dalam ±1 detik & LCD kembali idle
        fetch('/api/biometric/cancel-enroll', { method: 'POST' });
        document.getElementById('registerBioModal').classList.remove('active');
    }

    // Jika halaman ditutup/di-refresh saat sedang mendaftar, batalkan juga di alat
    window.addEventListener('beforeunload', function () {
        if (enrollPollInterval) {
            navigator.sendBeacon('/api/biometric/cancel-enroll');
        }
    });

    // Tombol ESC = Batal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('registerBioModal').classList.contains('active')) {
            closeBioModal();
        }
    });

    let bioAutoCloseTimer = null;

    // Tutup modal setelah sukses (tanpa kirim sinyal batal)
    function finishBioModal() {
        if (bioAutoCloseTimer) { clearTimeout(bioAutoCloseTimer); bioAutoCloseTimer = null; }
        document.getElementById('registerBioModal').classList.remove('active');
    }

    // Perbarui kartu biometrik di dashboard langsung tanpa reload
    function updateBioCard(slotId) {
        const now = new Date();
        const bulan = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const pad = n => String(n).padStart(2, '0');
        const waktu = pad(now.getDate()) + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear() + ', ' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        const badge = document.getElementById('bioCardBadge');
        badge.textContent = 'Aktif';
        badge.className = 'status-badge active';
        document.getElementById('bioCardTitle').textContent = 'Sidik Jari Terhubung';
        document.getElementById('bioCardDesc').textContent = 'Metode biometrik aktif untuk otorisasi pembayaran instan di kasir.';
        const t = document.getElementById('bioCardTime');
        t.textContent = 'Terdaftar: ' + waktu;
        t.style.display = 'inline';
        const idBadge = document.getElementById('bioCardIdBadge');
        if (idBadge) {
            idBadge.textContent = 'ID #' + pad(slotId);
            idBadge.style.display = 'inline-block';
        }
        const btn = document.getElementById('bioCardBtn');
        btn.textContent = 'Perbarui Sidik Jari';
        btn.className = 'action-btn secondary';
    }

    function startRealtimeEnrollment() {
        if (bioAutoCloseTimer) { clearTimeout(bioAutoCloseTimer); bioAutoCloseTimer = null; }
        document.getElementById('bioRetryBtn').style.display = 'none';
        document.getElementById('bioDoneBtn').style.display = 'none';
        document.getElementById('bioCancelBtn').style.display = 'block';
        document.getElementById('bioModalTitle').textContent = 'Perekaman Sidik Jari';
        document.getElementById('bioModalSubtitle').textContent = 'Menghubungkan ke pemindai biometrik di meja kasir...';
        setIcon('waiting');
        setBadge('● Menghubungkan', '#e0f2fe', '#0369a1');
        setStep('Mengirim perintah ke pemindai...', 'Pastikan alat pemindai menyala dan terhubung Wi-Fi.');
        resetStepDots();

        fetch('/api/biometric/start-enroll', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentUserId })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                if (data.is_full) {
                    showFullCapacityModal(data.message);
                } else {
                    showFailed(data.message || 'Gagal memulai pendaftaran.');
                }
                return;
            }
            document.getElementById('bioModalSubtitle').textContent = 'Silakan ikuti instruksi langkah di bawah ini pada scanner.';
            setStep('Menunggu alat pemindai merespons...', 'Biasanya hanya butuh 1-2 detik.');
            if (enrollPollInterval) clearInterval(enrollPollInterval);
            enrollPollInterval = setInterval(checkEnrollStatus, 500);
        })
        .catch(() => showFailed('Gagal menghubungi server.'));
    }

    function showFullCapacityModal(msg) {
        if (enrollPollInterval) { clearInterval(enrollPollInterval); enrollPollInterval = null; }
        setIcon('failed');
        document.getElementById('bioModalTitle').textContent = 'Kapasitas Memori Penuh';
        document.getElementById('bioModalSubtitle').textContent = 'Pendaftaran pengguna baru sementara tidak dapat diproses.';
        setBadge('● Memori Penuh', '#fee2e2', '#b91c1c');
        setStep(msg || 'Kapasitas slot sensor pemindai telah mencapai batas maksimal (127 slot).', 'Semua slot penyimpanan sidik jari pada alat telah terisi penuh. Silakan hubungi admin kasir.');
        document.getElementById('bioCancelBtn').textContent = 'Tutup';
        document.getElementById('bioRetryBtn').style.display = 'none';
    }

    function showFailed(msg) {
        if (enrollPollInterval) { clearInterval(enrollPollInterval); enrollPollInterval = null; }
        setIcon('failed');
        document.getElementById('bioModalTitle').textContent = 'Perekaman Gagal';
        document.getElementById('bioModalSubtitle').textContent = 'Sidik jari belum berhasil direkam.';
        setBadge('● Gagal', '#fee2e2', '#b91c1c');
        setStep(msg, 'Pastikan jari bersih, kering, dan ditempel rata. Klik "Coba Lagi".');
        document.getElementById('bioRetryBtn').style.display = 'block';
    }

    function resetStepDots() {
        const d1 = document.getElementById('stepDot1');
        const d2 = document.getElementById('stepDot2');
        const b1 = document.getElementById('stepBullet1');
        const b2 = document.getElementById('stepBullet2');
        if (d1) { d1.style.background = '#eff6ff'; d1.style.color = '#2563eb'; d1.style.borderColor = '#bfdbfe'; }
        if (b1) { b1.style.background = '#2563eb'; }
        if (d2) { d2.style.background = '#f8fafc'; d2.style.color = '#94a3b8'; d2.style.borderColor = '#e2e8f0'; }
        if (b2) { b2.style.background = '#cbd5e1'; }
    }

    function updateStepDots(step) {
        const d1 = document.getElementById('stepDot1');
        const d2 = document.getElementById('stepDot2');
        const b1 = document.getElementById('stepBullet1');
        const b2 = document.getElementById('stepBullet2');
        if (!d1 || !d2) return;

        if (step === 'step1_wait' || step === 'step1_process' || step === 'scan1') {
            d1.style.background = '#eff6ff'; d1.style.color = '#2563eb'; d1.style.borderColor = '#3b82f6';
            b1.style.background = '#2563eb';
            d2.style.background = '#f8fafc'; d2.style.color = '#94a3b8'; d2.style.borderColor = '#e2e8f0';
            b2.style.background = '#cbd5e1';
        } else if (step === 'step2_lift' || step === 'lift') {
            d1.style.background = '#f0fdf4'; d1.style.color = '#16a34a'; d1.style.borderColor = '#86efac';
            b1.style.background = '#16a34a';
            d2.style.background = '#fefce8'; d2.style.color = '#ca8a04'; d2.style.borderColor = '#fef08a';
            b2.style.background = '#ca8a04';
        } else if (step === 'step2_wait' || step === 'step2_process' || step === 'scan2') {
            d1.style.background = '#f0fdf4'; d1.style.color = '#16a34a'; d1.style.borderColor = '#86efac';
            b1.style.background = '#16a34a';
            d2.style.background = '#eff6ff'; d2.style.color = '#2563eb'; d2.style.borderColor = '#3b82f6';
            b2.style.background = '#2563eb';
        }
    }

    function checkEnrollStatus() {
        fetch('/api/biometric/enroll-status')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'scanning') {
                const s = STEP_TEXT[data.step] || STEP_TEXT.step1_wait;
                setBadge('● Sensor Aktif', '#dcfce7', '#15803d');
                setStep(s[0], s[1] + (data.attempt > 1 ? ' (Percobaan ' + data.attempt + '/3)' : ''));
                updateStepDots(data.step);
            } else if (data.status === 'success') {
                clearInterval(enrollPollInterval); enrollPollInterval = null;
                setIcon('success');
                document.getElementById('bioModalTitle').textContent = 'Pendaftaran Berhasil!';
                document.getElementById('bioModalSubtitle').textContent = 'Sidik jari Anda telah tersimpan dan aktif.';
                setBadge('● Berhasil', '#dcfce7', '#15803d');
                const slot = data.slot_id || 1;
                setStep('Sidik Jari Berhasil Disimpan', 'Sidik jari telah aktif untuk transaksi pembayaran di kasir.');
                
                // Set both dots green
                const d1 = document.getElementById('stepDot1');
                const d2 = document.getElementById('stepDot2');
                const b1 = document.getElementById('stepBullet1');
                const b2 = document.getElementById('stepBullet2');
                if (d1) { d1.style.background = '#f0fdf4'; d1.style.color = '#16a34a'; d1.style.borderColor = '#86efac'; }
                if (b1) { b1.style.background = '#16a34a'; }
                if (d2) { d2.style.background = '#f0fdf4'; d2.style.color = '#16a34a'; d2.style.borderColor = '#86efac'; }
                if (b2) { b2.style.background = '#16a34a'; }

                updateBioCard(slot);
                document.getElementById('bioCancelBtn').style.display = 'none';
                document.getElementById('bioDoneBtn').style.display = 'block';
                bioAutoCloseTimer = setTimeout(finishBioModal, 3500);
            } else if (data.status === 'duplicate') {
                if (enrollPollInterval) { clearInterval(enrollPollInterval); enrollPollInterval = null; }
                setIcon('failed');
                document.getElementById('bioModalTitle').textContent = 'Duplikasi Sidik Jari Terdeteksi';
                document.getElementById('bioModalSubtitle').textContent = 'Pendaftaran ditolak demi keamanan akun.';
                setBadge('● Duplikasi Ditolak', '#fee2e2', '#b91c1c');
                setStep(data.message || 'Sidik jari ini sudah terdaftar pada akun lain!', 'Satu sidik jari hanya dapat digunakan oleh satu mahasiswa. Silakan gunakan jari lain yang belum terdaftar.');
                document.getElementById('bioRetryBtn').textContent = 'Gunakan Jari Lain';
                document.getElementById('bioRetryBtn').style.display = 'block';
            } else if (data.status === 'failed') {
                showFailed(data.message || 'Sidik jari tidak cocok.');
            } else if (data.status === 'idle') {
                showFailed('Sesi pendaftaran berakhir.');
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - Portal Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9fafb;
            --surface-color: #ffffff;
            --primary-color: #111827;
            --primary-hover: #374151;
            --accent-color: #2563eb;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: var(--surface-color);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 2rem 1rem;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
        }
        .sidebar-logo img {
            max-height: 40px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.9rem 1rem;
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-decoration: none;
        }
        .nav-item:hover {
            background: #f3f4f6;
            color: var(--primary-color);
        }
        .nav-item.active {
            background: #eff6ff;
            color: var(--accent-color);
            font-weight: 600;
        }
        .nav-item svg {
            width: 20px;
            height: 20px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        /* Header */
        .header {
            padding: 1.5rem 3rem;
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.4rem;
            font-weight: 700;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Alert */
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            border: 1px solid #a7f3d0;
        }

        /* Dashboard Area */
        .dashboard-area {
            padding: 2rem 3rem;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .status-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .status-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
        }
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge.active {
            background: #d1fae5;
            color: var(--success-color);
        }
        .status-badge.warning {
            background: #fef3c7;
            color: var(--warning-color);
        }
        .status-badge.danger {
            background: #fee2e2;
            color: var(--danger-color);
        }
        .card-content h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        .card-content p {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .action-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: auto;
            text-align: center;
            text-decoration: none;
        }
        .action-btn:hover {
            background: var(--primary-hover);
        }
        .action-btn.secondary {
            background: #f3f4f6;
            border: 1px solid var(--border-color);
            color: var(--primary-color);
        }
        .action-btn.secondary:hover {
            background: #e5e7eb;
        }

        /* Table */
        .table-container {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        td {
            font-size: 0.9rem;
            color: var(--primary-color);
            font-weight: 500;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .amount {
            font-weight: 600;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }
        .modal-content {
            background: var(--surface-color);
            padding: 2rem;
            border-radius: 20px;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .modal-content h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .modal-content p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            text-align: center;
        }
        .fingerprint-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            color: var(--accent-color);
            animation: pulse-glow 2s infinite ease-in-out;
        }
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.08); opacity: 1; }
        }
        .form-group {
            margin-bottom: 1.25rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                flex-direction: row;
                padding: 1rem;
                height: 70px;
                align-items: center;
                justify-content: space-between;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            .sidebar-logo {
                margin-bottom: 0;
            }
            .nav-item span {
                display: none;
            }
            .header, .dashboard-area {
                padding: 1.5rem;
            }
            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <span style="font-weight: 800; color: var(--accent-color); font-size: 1.4rem;">MyFin</span>
            <span style="font-weight: 600; color: var(--text-muted); font-size: 0.9rem; margin-left: 4px;">Mahasiswa</span>
        </div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-item {{ ($activeTab ?? 'dashboard') === 'dashboard' ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('mahasiswa.pembayaran') }}" class="nav-item {{ ($activeTab ?? '') === 'pembayaran' ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span>Metode Pembayaran</span>
        </a>
        <a href="{{ route('mahasiswa.riwayat') }}" class="nav-item {{ ($activeTab ?? '') === 'riwayat' ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span>Riwayat Belanja</span>
        </a>

        <!-- Logout Form -->
        <div style="margin-top: auto;">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="nav-item" style="background: transparent; border: none; width: 100%; text-align: left; margin-bottom: 0;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>Selamat Datang, {{ $user->name }}!</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">Sistem Pembayaran Biometrik Kantin PSDKU</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;">{{ $user->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $user->email }}</div>
                </div>
                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            </div>
        </div>

        <!-- Dashboard Area -->
        <div class="dashboard-area">

            @if(session('success'))
            <div class="alert-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            
            <div class="cards-grid">
                <!-- Debit Linking Status (Midtrans) -->
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
                            <p style="font-size: 0.75rem; color: var(--success-color); margin-top: 4px;">✓ Siap untuk One-Click Auto Debit</p>
                        @else
                            <h2>Belum Terhubung</h2>
                            <p>Tautkan kartu debit/rekening untuk aktivasi checkout POS.</p>
                        @endif
                    </div>
                    <button class="action-btn {{ $debit ? 'secondary' : '' }}" onclick="openDebitModal()">
                        {{ $debit ? 'Ganti / Perbarui Kartu' : 'Tautkan Kartu Debit' }}
                    </button>
                </div>

                <!-- Biometric Status (Sensor AS608) -->
                <div class="status-card">
                    <div class="card-header">
                        <h3>Verifikasi Biometrik (Sensor AS608)</h3>
                        @if($biometric)
                            <span class="status-badge active">Terdaftar</span>
                        @else
                            <span class="status-badge warning">Belum Terdaftar</span>
                        @endif
                    </div>
                    <div class="card-content">
                        @if($biometric)
                            <h2>Slot Sensor #{{ $biometric->fingerprint_id ?? 1 }}</h2>
                            <p>Sidik jari aktif untuk otorisasi pembayaran POS.</p>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;">Terdaftar: {{ \Carbon\Carbon::parse($biometric->registered_at)->format('d M Y, H:i') }}</p>
                        @else
                            <h2>Data Kosong</h2>
                            <p>Daftarkan sidik jari Anda ke sensor AS608 untuk belanja tanpa kartu.</p>
                        @endif
                    </div>
                    <button class="action-btn {{ $biometric ? 'secondary' : '' }}" onclick="openBioModal()">
                        {{ $biometric ? 'Perbarui Sidik Jari' : 'Daftarkan Sidik Jari' }}
                    </button>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="section-title">
                <span>Riwayat Belanja (Kantin PSDKU)</span>
                <a href="{{ route('mahasiswa.riwayat') }}" style="font-size: 0.85rem; color: var(--accent-color); text-decoration: none;">Lihat Semua &rarr;</a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Resi Digital</th>
                            <th>Stand / Merchant</th>
                            <th>Total</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                            <td style="font-family: monospace; font-weight: 600;">{{ $tx->transaction_code }}</td>
                            <td>{{ $tx->kantin->nama_kantin ?? 'Kantin PSDKU' }}</td>
                            <td class="amount">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($tx->status === 'success')
                                    <span class="status-badge active">Berhasil (Midtrans)</span>
                                @elseif($tx->status === 'pending')
                                    <span class="status-badge warning">Menunggu Scan</span>
                                @else
                                    <span class="status-badge danger">Gagal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                                Belum ada riwayat transaksi belanja.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal Tautkan Kartu Debit (Midtrans) -->
    <div class="modal-overlay" id="debitModal">
        <div class="modal-content">
            <h2>Tautkan Kartu Debit Midtrans</h2>
            <p>Masukkan kartu debit bank Anda untuk auto-debit saat otorisasi sidik jari di Kios POS.</p>
            <form action="{{ route('mahasiswa.pembayaran.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Pilih Bank</label>
                    <select name="bank_name" class="form-control" id="bankSelect" required>
                        <option value="Bank BCA">Bank BCA</option>
                        <option value="Bank Mandiri">Bank Mandiri</option>
                        <option value="Bank BRI">Bank BRI</option>
                        <option value="Bank BNI">Bank BNI</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Kartu Debit (16 Digit)</label>
                    <input type="text" name="card_number" id="cardNumberInput" class="form-control" placeholder="4811 1111 1111 1234" maxlength="19" required>
                </div>
                <div style="margin-bottom: 1.5rem; text-align: right;">
                    <button type="button" onclick="autofillCard()" style="background: none; border: none; color: var(--accent-color); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                        Gunakan Nomor Kartu Sandbox Demo
                    </button>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="action-btn secondary" style="flex: 1;" onclick="closeDebitModal()">Batal</button>
                    <button type="submit" class="action-btn" style="flex: 1.5;">Simpan Kartu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Biometric Registration Modal -->
    <div class="modal-overlay" id="registerBioModal">
        <div class="modal-content">
            <div class="fingerprint-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
            </div>
            <h2>Perekaman Sidik Jari Sensor AS608</h2>
            <p>Silakan letakkan ibu jari Anda pada sensor AS608 hardware atau tentukan ID slot sensor Anda.</p>
            
            <form action="{{ route('mahasiswa.biometric.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>ID Slot Sensor AS608 (1 - 127)</label>
                    <input type="number" name="fingerprint_id" class="form-control" value="{{ $biometric->fingerprint_id ?? 1 }}" min="1" max="127" required>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Slot ID yang tersimpan di memori sensor sidik jari ESP32.</small>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 1.5rem;">
                    <button type="button" class="action-btn secondary" style="flex: 1;" onclick="closeBioModal()">Batal</button>
                    <button type="submit" class="action-btn" style="flex: 1.5;">Daftarkan Sidik Jari</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDebitModal() {
            document.getElementById('debitModal').classList.add('active');
        }
        function closeDebitModal() {
            document.getElementById('debitModal').classList.remove('active');
        }

        function openBioModal() {
            document.getElementById('registerBioModal').classList.add('active');
        }
        function closeBioModal() {
            document.getElementById('registerBioModal').classList.remove('active');
        }

        function autofillCard() {
            document.getElementById('cardNumberInput').value = '4811 1111 1111 ' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('bankSelect').value = 'Bank BCA';
        }
    </script>
</body>
</html>
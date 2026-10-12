<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin Kantin - Rekening Pencairan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.styles')
    <style>
        .credit-card {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border-radius: 16px;
            padding: 1.75rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 220px;
            position: relative;
            overflow: hidden;
        }
        .credit-card::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            top: -80px;
            right: -80px;
        }
        .card-chip {
            width: 45px;
            height: 32px;
            background: linear-gradient(135deg, #fcd34d, #f59e0b);
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        .card-number {
            font-family: monospace;
            font-size: 1.35rem;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
            <span>Kantin</span>
        </div>
        <a href="/kantin" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="/kantin/menu" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span>Manajemen Menu</span>
        </a>
        <a href="/kantin/rekening" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Rekening Pencairan</span>
        </a>
        <div class="nav-logout">
            <form method="POST" action="/logout">@csrf
                <button type="submit"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg><span>Logout</span></button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Rekening Pencairan Dana</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">Kelola rekening bank stand Anda untuk penyaluran dana penjualan Midtrans</p>
            </div>
            <div class="user-info">
                <div style="text-align: right; margin-right: 4px;">
                    <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">{{ $kantin->nama_kantin ?? 'Stand Merchant' }}</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">{{ auth()->user()->email }}</div>
                </div>
                <div class="avatar">
                    @if(auth()->user()->photo)
                        <img src="/storage/{{ auth()->user()->photo }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </div>

        <div class="dashboard-area">
            @if(session('success'))
                <div class="alert alert-success" style="display: flex; align-items: center; gap: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; align-items: start;">
                <!-- Visual Kartu Debit -->
                <div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--primary-color);">Kartu Debit Terdaftar</h3>
                    <div class="credit-card">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.2rem; letter-spacing: 1px;" id="cardBankDisplay">{{ $rekening->bank_name ?? 'Bank BCA' }}</span>
                            <span style="font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 0.2rem 0.6rem; border-radius: 4px;">Midtrans 1-Click</span>
                        </div>
                        <div class="card-chip"></div>
                        <div class="card-number" id="cardNumberDisplay">{{ $rekening->account_number ?? '4811 1111 1111 1234' }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                            <div>
                                <div style="font-size: 0.7rem; opacity: 0.8; text-transform: uppercase;">Pemegang Kartu</div>
                                <div style="font-weight: 600; font-size: 0.95rem;" id="cardHolderDisplay">{{ $rekening->account_holder_name ?? ($kantin->nama_kantin ?? 'Pemilik Stand') }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; opacity: 0.8; text-transform: uppercase;">Status</div>
                                <div style="font-weight: 600; font-size: 0.85rem; color: #a7f3d0;">● Aktif Terhubung</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Tautkan / Perbarui Kartu Debit -->
                <div style="background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 16px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--primary-color);">Tautkan / Perbarui Kartu Debit</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Kartu ini akan digunakan otomatis saat Anda menempelkan sidik jari di Kios POS.</p>

                    <form method="POST" action="/kantin/rekening">
                        @csrf
                        <div class="form-group">
                            <label>Pilih Bank</label>
                            <select name="bank_name" id="bankSelect" class="form-control" required onchange="updateCardLive()">
                                <option value="Bank BCA" {{ ($rekening->bank_name ?? '') === 'Bank BCA' ? 'selected' : '' }}>Bank BCA</option>
                                <option value="Bank Mandiri" {{ ($rekening->bank_name ?? '') === 'Bank Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                                <option value="Bank BRI" {{ ($rekening->bank_name ?? '') === 'Bank BRI' ? 'selected' : '' }}>Bank BRI</option>
                                <option value="Bank BNI" {{ ($rekening->bank_name ?? '') === 'Bank BNI' ? 'selected' : '' }}>Bank BNI</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nomor Kartu Debit (16 Digit)</label>
                            <input type="text" name="account_number" id="accountNumberInput" class="form-control" placeholder="4811 1111 1111 1234" value="{{ $rekening->account_number ?? '' }}" required oninput="updateCardLive()">
                        </div>
                        <div class="form-group">
                            <label>Nama Pemilik Rekening</label>
                            <input type="text" name="account_holder_name" id="accountHolderInput" class="form-control" placeholder="Nama sesuai buku rekening" value="{{ $rekening->account_holder_name ?? '' }}" required oninput="updateCardLive()">
                        </div>

                        <div style="margin-bottom: 1.5rem; text-align: right;">
                            <button type="button" onclick="autofillSandbox()" style="background: none; border: none; color: var(--accent-color); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                                Gunakan Nomor Kartu Sandbox Demo
                            </button>
                        </div>

                        <button type="submit" class="btn" style="width: 100%; padding: 0.9rem; background: var(--primary-color); color: white; border-radius: 8px; font-weight: 600; cursor: pointer; justify-content: center;">
                            Simpan & Tautkan Kartu ke Midtrans
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCardLive() {
            const bank = document.getElementById('bankSelect').value;
            const number = document.getElementById('accountNumberInput').value.trim();
            const holder = document.getElementById('accountHolderInput').value.trim();

            document.getElementById('cardBankDisplay').textContent = bank || 'Bank BCA';
            document.getElementById('cardNumberDisplay').textContent = number || '4811 1111 1111 1234';
            document.getElementById('cardHolderDisplay').textContent = holder || '{{ $kantin->nama_kantin ?? "Pemilik Stand" }}';
        }

        function autofillSandbox() {
            document.getElementById('bankSelect').value = 'Bank BCA';
            document.getElementById('accountNumberInput').value = '4811 1111 1111 ' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('accountHolderInput').value = '{{ $kantin->nama_kantin ?? "Kantin Bu Sri" }}';
            updateCardLive();
        }
    </script>
</body>
</html>

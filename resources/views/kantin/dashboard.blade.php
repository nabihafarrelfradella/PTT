<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin Kantin - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
            <span>Kantin</span>
        </div>
        <a href="/kantin" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="/kantin/menu" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span>Manajemen Menu</span>
        </a>
        <a href="/kantin/rekening" class="nav-item">
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
            <h1>{{ $kantin->nama_kantin ?? 'Stand Anda' }}</h1>
            <div class="user-info">
                @if($kantin && $kantin->status_verifikasi === 'approved')
                    <span class="status-badge active">Terverifikasi</span>
                @elseif($kantin)
                    <span class="status-badge pending">Menunggu Verifikasi Admin</span>
                @endif
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
            @if(!$kantin)
                <div class="alert alert-danger">Data kantin Anda belum ditemukan. Hubungi admin untuk bantuan.</div>
            @endif

            <h2 class="section-title">Transaksi Terakhir</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Pembeli</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $trx->transaction_code }}</td>
                            <td>{{ $trx->student->name ?? '-' }}</td>
                            <td style="font-weight:600;">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status === 'success')
                                    <span class="status-badge active">Berhasil</span>
                                @elseif($trx->status === 'pending')
                                    <span class="status-badge pending">Menunggu</span>
                                @elseif($trx->status === 'cancelled')
                                    <span class="status-badge" style="background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">Dibatalkan</span>
                                @else
                                    <span class="status-badge danger">Gagal</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding: 2rem;">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

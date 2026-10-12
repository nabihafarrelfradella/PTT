<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - @yield('title', 'Portal Mahasiswa')</title>
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
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; height: 100vh; }
        .sidebar { width: 250px; background: var(--surface-color); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 2rem 1rem; }
        .sidebar-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 2.5rem; padding: 0 1rem; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.9rem 1rem; border-radius: 8px; color: var(--text-muted); cursor: pointer; transition: all 0.2s; font-weight: 500; margin-bottom: 0.5rem; text-decoration: none; }
        .nav-item:hover { background: #f3f4f6; color: var(--primary-color); }
        .nav-item.active { background: #eff6ff; color: var(--accent-color); font-weight: 600; }
        .nav-item svg { width: 20px; height: 20px; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .header { padding: 1.5rem 3rem; background: var(--surface-color); border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.4rem; font-weight: 700; }
        .user-profile { display: flex; align-items: center; gap: 1rem; }
        .avatar { width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; }
        .content-area { padding: 2rem 3rem; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; border: 1px solid #a7f3d0; }
        .section-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--primary-color); display: flex; justify-content: space-between; align-items: center; }
        .status-badge { padding: 0.2rem 0.65rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-block; white-space: nowrap; }
        .status-badge.active, .status-badge.success { background: #d1fae5; color: var(--success-color); }
        .status-badge.warning, .status-badge.pending { background: #fef3c7; color: var(--warning-color); }
        .status-badge.danger { background: #fee2e2; color: var(--danger-color); }
        .action-btn { background: var(--primary-color); color: white; border: none; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: background 0.2s; text-align: center; text-decoration: none; display: inline-block; }
        .action-btn:hover { background: var(--primary-hover); }
        .action-btn.secondary { background: #f3f4f6; border: 1px solid var(--border-color); color: var(--primary-color); }
        .action-btn.secondary:hover { background: #e5e7eb; }
        .table-container { 
            background: var(--surface-color); 
            border: 1px solid var(--border-color); 
            border-radius: 12px; 
            overflow-x: auto; 
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch; 
            margin-bottom: 2rem; 
            width: 100%;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .table-container::-webkit-scrollbar { height: 6px; }
        .table-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 9999px; }
        .table-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .table-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        table { width: 100%; border-collapse: collapse; min-width: 760px; }
        th, td { padding: 0.85rem 1.25rem; text-align: left; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        th { background: #f9fafb; font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; }
        td { font-size: 0.875rem; color: var(--primary-color); font-weight: 500; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem; outline: none; }
        .form-control:focus { border-color: var(--accent-color); }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; flex-direction: row; height: 70px; padding: 1rem; border-right: none; border-bottom: 1px solid var(--border-color); }
            .sidebar-logo { margin-bottom: 0; }
            .nav-item span { display: none; }
            .header, .content-area { padding: 1.5rem; }
            .table-container { overflow-x: auto; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <span style="font-weight: 800; color: var(--accent-color); font-size: 1.4rem;">MyFin</span>
            <span style="font-weight: 600; color: var(--text-muted); font-size: 0.9rem; margin-left: 4px;">Mahasiswa</span>
        </div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('mahasiswa.pembayaran') }}" class="nav-item {{ request()->routeIs('mahasiswa.pembayaran') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span>Metode Pembayaran</span>
        </a>
        <a href="{{ route('mahasiswa.riwayat') }}" class="nav-item {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span>Riwayat Belanja</span>
        </a>
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
    <div class="main-content">
        <div class="header">
            <div>
                <h1>@yield('header_title', 'Selamat Datang!')</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">@yield('header_subtitle', 'Sistem Pembayaran Biometrik Kantin PSDKU')</p>
            </div>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;">{{ $user->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $user->email }}</div>
                </div>
                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            </div>
        </div>
        <div class="content-area">
            @if(session('success'))
            <div class="alert-success">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @yield('content')
        </div>
    </div>
    @yield('scripts')
</body>
</html>
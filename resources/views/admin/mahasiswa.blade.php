<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin Admin - Data Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
            <span>Admin</span>
        </div>
        <a href="/admin/kantin" class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Approval Kantin</span>
        </a>
        <a href="/admin/mahasiswa" class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>Data Mahasiswa</span>
        </a>
        <div class="nav-logout">
            <form method="POST" action="/logout">@csrf
                <button type="submit"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg><span>Logout</span></button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Manajemen Data Mahasiswa</h1>
            <div class="user-info">
                <span style="font-size: 0.9rem; font-weight: 500;">{{ auth()->user()->name }}</span>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>

        <div class="dashboard-area">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                            <th>Rekening Debit</th>
                            <th>Status Biometrik</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $mhs)
                        <tr>
                            <td>
                                @if($mhs->photo)
                                    <img src="/storage/{{ $mhs->photo }}" class="photo-preview">
                                @else
                                    <div style="width:50px;height:50px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:var(--text-muted);">{{ strtoupper(substr($mhs->name, 0, 1)) }}</div>
                                @endif
                            </td>
                            <td>{{ $mhs->name }}</td>
                            <td>{{ $mhs->email }}</td>
                            <td>
                                @if($mhs->debitAccount)
                                    {{ $mhs->debitAccount->bank_name }} ({{ $mhs->debitAccount->card_token_masked }})
                                @else
                                    <span style="color:var(--text-muted); font-style:italic;">Belum diatur</span>
                                @endif
                            </td>
                            <td>
                                @if($mhs->biometric)
                                    <span class="status-badge active">Terdaftar</span>
                                @else
                                    <span class="status-badge warning">Belum</span>
                                @endif
                            </td>
                            <td>
                                @if($mhs->biometric)
                                    <form method="POST" action="/admin/mahasiswa/{{ $mhs->id }}/reset-bio" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Reset sidik jari mahasiswa ini?')">Reset Sidik Jari</button>
                                    </form>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.85rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 2rem;">Belum ada mahasiswa terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

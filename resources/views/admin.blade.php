<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - Portal Admin</title>
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
            margin-bottom: 3rem;
            padding: 0 1rem;
        }
        .sidebar-logo img {
            max-height: 40px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1rem;
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .nav-item:hover {
            background: #f3f4f6;
            color: var(--primary-color);
        }
        .nav-item.active {
            background: #eff6ff;
            color: var(--accent-color);
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
            padding: 2rem 3rem;
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .dashboard-area {
            padding: 2rem 3rem;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .stat-card h3 {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .stat-card .trend {
            font-size: 0.85rem;
            color: var(--success-color);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        /* Table */
        .table-container {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2rem;
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
            font-size: 0.875rem;
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

        .btn-approve {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-approve:hover {
            opacity: 0.9;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-badge.warning {
            background: #fef3c7;
            color: var(--warning-color);
        }
        .status-badge.active {
            background: #d1fae5;
            color: var(--success-color);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">Admin</span>
        </div>
        <div class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </div>
        <div class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Approval Kantin</span>
        </div>
        <div class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>Data Mahasiswa</span>
        </div>
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

    <div class="main-content">
        <div class="header">
            <h1>Sistem Pengawasan Pusat MyFin</h1>
            <div style="font-weight: 500; font-size: 0.9rem;">Admin PSDKU Polinema</div>
        </div>

        <div class="dashboard-area">
            
            <h2 class="section-title">Manajemen & Approval Kantin</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Stand</th>
                            <th>Pemilik</th>
                            <th>Lokasi / Blok</th>
                            <th>Rekening Pencairan</th>
                            <th>Status Verifikasi</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kopi Janji Mahasiswa</td>
                            <td>Rudi Hermawan</td>
                            <td>Stand 13</td>
                            <td>BCA - 123991823 (Rudi H)</td>
                            <td><span class="status-badge warning" id="badge-stand-1">Pending</span></td>
                            <td><button class="btn-approve" id="btn-stand-1" onclick="approveStand()">Approve</button></td>
                        </tr>
                        <tr>
                            <td>Stand Nasi Goreng Bu Sri</td>
                            <td>Sri Wahyuni</td>
                            <td>Stand 01</td>
                            <td>BCA - 8239100234 (Sri W)</td>
                            <td><span class="status-badge active">Aktif</span></td>
                            <td><span style="color: var(--text-muted); font-size: 0.85rem;">Terverifikasi</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h2 class="section-title" style="margin-top: 1rem;">Manajemen Data Mahasiswa</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>NIM / Email</th>
                            <th>Rekening Debit (Midtrans)</th>
                            <th>Status Biometrik</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Budi Santoso</td>
                            <td>budi@student.com</td>
                            <td>Bank BCA (****1234)</td>
                            <td><span class="status-badge active">Terdaftar</span></td>
                            <td><button style="background: transparent; border: 1px solid var(--border-color); padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">Reset Sidik Jari</button></td>
                        </tr>
                        <tr>
                            <td>Siti Aminah</td>
                            <td>siti@student.com</td>
                            <td><span style="color: var(--text-muted); font-style: italic;">Belum diatur</span></td>
                            <td><span class="status-badge warning">Belum Terdaftar</span></td>
                            <td><button style="background: transparent; border: 1px solid var(--border-color); padding: 0.4rem 0.8rem; border-radius: 6px; cursor: pointer;">Kirim Pengingat</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

    <script>
        function approveStand() {
            // Simulasi proses approval
            document.getElementById('badge-stand-1').className = 'status-badge active';
            document.getElementById('badge-stand-1').innerText = 'Aktif';
            document.getElementById('btn-stand-1').outerHTML = '<span style="color: var(--text-muted); font-size: 0.85rem;">Terverifikasi</span>';
            alert('Stand berhasil diverifikasi! Pencairan Midtrans diaktifkan untuk stand ini.');
        }
    </script>
</body>
</html>

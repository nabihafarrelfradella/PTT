<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - Portal Kantin</title>
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
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background: var(--primary-hover);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }
        .btn-outline:hover {
            background: #f3f4f6;
        }

        /* Table Area */
        .table-container {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        .table-container::-webkit-scrollbar {
            height: 6px;
        }
        .table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }
        th, td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
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
        
        .menu-img-preview {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Form Area */
        .form-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: var(--accent-color);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #d1fae5;
            color: var(--success-color);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x40?text=MyFin';">
            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.2rem;">Kantin</span>
        </div>
        <div class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </div>
        <div class="nav-item active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span>Manajemen Menu</span>
        </div>
        <div class="nav-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Rekening Pencairan</span>
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
            <h1>Stand Nasi Goreng Bu Sri</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="status-badge">Terverifikasi Admin</span>
            </div>
        </div>

        <div class="dashboard-area">
            
            <!-- Manajemen Menu -->
            <div>
                <div class="section-header">
                    <h2 class="section-title">Katalog Menu & Stok</h2>
                    <button class="btn" onclick="openMenuModal()">+ Tambah Menu Baru</button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto Produk</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok Tersedia</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="menuTableBody">
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" class="menu-img-preview"></td>
                                <td>Nasi Goreng Spesial</td>
                                <td>Makanan</td>
                                <td>Rp 15.000</td>
                                <td>24 Porsi</td>
                                <td>
                                    <button class="btn btn-outline" style="padding: 0.4rem 0.8rem;">Edit</button>
                                </td>
                            </tr>
                            <tr>
                                <td><img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" class="menu-img-preview"></td>
                                <td>Es Teh Manis</td>
                                <td>Minuman</td>
                                <td>Rp 5.000</td>
                                <td>100 Gelas</td>
                                <td>
                                    <button class="btn btn-outline" style="padding: 0.4rem 0.8rem;">Edit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Rekening Bank -->
            <div style="margin-top: 2rem;">
                <h2 class="section-title" style="margin-bottom: 1rem;">Rekening Bank (Pencairan Midtrans)</h2>
                <div class="form-card">
                    <div class="form-group">
                        <label>Nama Bank</label>
                        <select class="form-control">
                            <option>Bank BCA</option>
                            <option>Bank Mandiri</option>
                            <option>Bank BNI</option>
                            <option>Bank BRI</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nomor Rekening</label>
                        <input type="text" class="form-control" value="8239100234">
                    </div>
                    <div class="form-group">
                        <label>Nama Pemilik Rekening</label>
                        <input type="text" class="form-control" value="Sri Wahyuni">
                    </div>
                    <button class="btn" style="width: 100%;" onclick="alert('Data rekening berhasil disimpan untuk sistem Payout otomatis Midtrans!')">Simpan Perubahan</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Tambah Menu -->
    <div class="modal-overlay" id="addMenuModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000;">
        <div class="form-card" style="width: 100%; max-width: 500px;">
            <h2 class="section-title">Tambah Menu Baru</h2>
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" class="form-control" id="newMenuName" placeholder="Contoh: Es Campur">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select class="form-control" id="newMenuCategory">
                    <option>Makanan</option>
                    <option>Minuman</option>
                </select>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" class="form-control" id="newMenuPrice" placeholder="Contoh: 10000">
            </div>
            <div class="form-group">
                <label>Stok Awal</label>
                <input type="number" class="form-control" id="newMenuStock" placeholder="Contoh: 50">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button class="btn" style="flex: 1;" onclick="saveNewMenu()">Simpan Menu</button>
                <button class="btn btn-outline" style="flex: 1;" onclick="closeMenuModal()">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function openMenuModal() {
            document.getElementById('addMenuModal').style.display = 'flex';
        }
        function closeMenuModal() {
            document.getElementById('addMenuModal').style.display = 'none';
        }
        function saveNewMenu() {
            const name = document.getElementById('newMenuName').value;
            const category = document.getElementById('newMenuCategory').value;
            const price = document.getElementById('newMenuPrice').value;
            const stock = document.getElementById('newMenuStock').value;
            
            if(!name || !price) {
                alert("Mohon isi nama menu dan harga!");
                return;
            }

            const tbody = document.getElementById('menuTableBody');
            const formattedPrice = "Rp " + parseInt(price).toLocaleString('id-ID');
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><img src="https://via.placeholder.com/100x100?text=Menu" class="menu-img-preview"></td>
                <td>${name}</td>
                <td>${category}</td>
                <td>${formattedPrice}</td>
                <td>${stock} Porsi</td>
                <td>
                    <button class="btn btn-outline" style="padding: 0.4rem 0.8rem;">Edit</button>
                </td>
            `;
            tbody.prepend(tr);
            
            closeMenuModal();
            
            // reset
            document.getElementById('newMenuName').value = '';
            document.getElementById('newMenuPrice').value = '';
            document.getElementById('newMenuStock').value = '';
        }
    </script>
</body>
</html>

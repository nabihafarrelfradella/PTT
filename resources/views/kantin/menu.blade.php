<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin Kantin - Manajemen Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.styles')
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
        <a href="/kantin/menu" class="nav-item active">
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
            <h1>Manajemen Menu</h1>
            <div class="user-info">
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
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="section-header">
                <h2 class="section-title" style="margin-bottom:0;">Katalog Menu & Stok</h2>
                <button class="btn" onclick="document.getElementById('addMenuModal').classList.add('active')">+ Tambah Menu Baru</button>
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
                    <tbody>
                        @forelse($menus as $menu)
                        <tr id="row-{{ $menu->id }}">
                            <td>
                                <img src="{{ $menu->foto_url }}" class="menu-img-preview" alt="{{ $menu->nama_menu }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=120&q=80';">
                            </td>
                            <td style="font-weight: 500;">{{ $menu->nama_menu }}</td>
                            <td>{{ $menu->kategori_display }}</td>
                            <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                            <td>{{ $menu->stock_display }}</td>
                            <td style="display: flex; gap: 0.5rem; align-items: center;">
                                <button class="btn btn-outline" style="padding: 0.4rem 0.8rem;" onclick="openEditModal({{ $menu->id }}, '{{ addslashes($menu->nama_menu) }}', '{{ strtolower($menu->kategori_display) }}', {{ $menu->harga }}, {{ $menu->stock }})">Edit</button>
                                <form method="POST" action="/kantin/menu/{{ $menu->id }}" style="display:inline; margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.8rem; color: #ef4444; border-color: #fca5a5;" onclick="return confirm('Hapus menu ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 2rem;">Belum ada menu. Klik "+ Tambah Menu Baru" untuk memulai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Menu -->
    <div class="modal-overlay" id="addMenuModal">
        <div class="modal-body">
            <h2 class="section-title">Tambah Menu Baru</h2>
            <form method="POST" action="/kantin/menu" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" placeholder="Contoh: Nasi Goreng Spesial" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" required>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" placeholder="15000" required>
                </div>
                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" name="stock" class="form-control" placeholder="50" required>
                </div>
                <div class="form-group">
                    <label>Foto Produk (Opsional)</label>
                    <input type="file" name="foto_menu" class="form-control" accept="image/*">
                </div>
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn" style="flex:1;">Simpan</button>
                    <button type="button" class="btn btn-outline" style="flex:1;" onclick="document.getElementById('addMenuModal').classList.remove('active')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div class="modal-overlay" id="editMenuModal">
        <div class="modal-body">
            <h2 class="section-title">Edit Menu</h2>
            <form method="POST" id="editMenuForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" id="editName" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" class="form-control" id="editKategori" required>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" id="editHarga" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stock" class="form-control" id="editStock" required>
                </div>
                <div class="form-group">
                    <label>Ganti Foto (Opsional)</label>
                    <input type="file" name="foto_menu" class="form-control" accept="image/*">
                </div>
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn" style="flex:1;">Simpan Perubahan</button>
                    <button type="button" class="btn btn-outline" style="flex:1;" onclick="document.getElementById('editMenuModal').classList.remove('active')">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, kategori, harga, stock) {
            document.getElementById('editName').value = name;
            document.getElementById('editKategori').value = (kategori === 'minuman') ? 'minuman' : 'makanan';
            document.getElementById('editHarga').value = harga;
            document.getElementById('editStock').value = stock;
            document.getElementById('editMenuForm').action = '/kantin/menu/' + id;
            document.getElementById('editMenuModal').classList.add('active');
        }
    </script>
</body>
</html>

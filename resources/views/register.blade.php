<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - Registrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg-color: #f9fafb; --surface-color: #ffffff; --primary-color: #111827; --primary-hover: #374151; --accent-color: #2563eb; --text-main: #111827; --text-muted: #6b7280; --border-color: #e5e7eb; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.25rem; }
        .register-card { background: var(--surface-color); padding: clamp(1.5rem, 4vw, 2.5rem); border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); width: 100%; max-width: 480px; border: 1px solid var(--border-color); }
        .register-logo { text-align: center; margin-bottom: 2rem; }
        .register-logo img { max-height: 60px; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 0.7rem 1rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s; }
        .form-control:focus { border-color: var(--accent-color); }
        .btn-register { width: 100%; background: var(--primary-color); color: white; border: none; padding: 0.9rem; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; margin-top: 0.5rem; }
        .btn-register:hover { background: var(--primary-hover); }
        .error-msg { color: #ef4444; font-size: 0.82rem; margin-top: 0.25rem; }
        .error-list { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; }
        .error-list ul { padding-left: 1rem; }
        .kantin-fields { display: none; }
        .kantin-fields.show { display: block; }
        .link { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted); }
        .link a { color: var(--accent-color); text-decoration: none; font-weight: 600; }
        .photo-upload { display: flex; align-items: center; gap: 1rem; }
        .photo-preview-box { width: 80px; height: 80px; border-radius: 12px; border: 2px dashed var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-muted); overflow: hidden; flex-shrink: 0; }
        .photo-preview-box img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/120x60?text=MyFin';">
        </div>
        <h2 style="text-align:center; margin-bottom: 0.5rem; font-size: 1.4rem;">Buat Akun Baru</h2>
        <p style="text-align:center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.75rem;">Daftar sebagai Pemilik Kantin atau Mahasiswa</p>
        
        @if($errors->any())
        <div class="error-list">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/register" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Daftar Sebagai</label>
                <select name="role" class="form-control" id="roleSelect" onchange="toggleKantinFields()" required>
                    <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="kantin" {{ old('role') == 'kantin' ? 'selected' : '' }}>Pemilik Kantin / Merchant</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Foto Profil</label>
                <div class="photo-upload">
                    <div class="photo-preview-box" id="photoPreview">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div style="flex: 1;">
                        <input type="file" name="photo" accept="image/jpeg,image/png" class="form-control" id="photoInput" onchange="previewPhoto(event)" required>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem;">JPG/PNG, maks. 2MB</div>
                    </div>
                </div>
            </div>
            
            <div class="kantin-fields" id="kantinFields">
                <div class="form-group">
                    <label>Nama Stand Kantin</label>
                    <input type="text" name="nama_kantin" class="form-control" placeholder="Contoh: Warung Makan Bu Sri" value="{{ old('nama_kantin') }}">
                </div>
                <div class="form-group">
                    <label>No Stand / Lokasi</label>
                    <input type="text" name="no_stand" class="form-control" placeholder="Contoh: Stand 05" value="{{ old('no_stand') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
            </div>
            
            <button type="submit" class="btn-register">Daftar Sekarang</button>
        </form>
        <div class="link">Sudah punya akun? <a href="/login">Masuk di sini</a></div>
    </div>

    <script>
        function toggleKantinFields() {
            const role = document.getElementById('roleSelect').value;
            document.getElementById('kantinFields').classList.toggle('show', role === 'kantin');
        }
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').innerHTML = '<img src="' + e.target.result + '">';
                }
                reader.readAsDataURL(file);
            }
        }
        // Init
        toggleKantinFields();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFin - Login Terintegrasi</title>
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
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 1.25rem;
        }
        .login-card {
            background: var(--surface-color);
            padding: clamp(1.75rem, 5vw, 3rem);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        .login-logo {
            margin-bottom: 2rem;
        }
        .login-logo img {
            max-height: 60px;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
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
        .btn-login {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 1rem;
        }
        .btn-login:hover {
            background: var(--primary-hover);
        }
        .error-msg {
            color: #ef4444;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            text-align: left;
        }
        .credentials-hint {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--border-color);
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: left;
        }
        .credentials-hint ul {
            padding-left: 1rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <img src="/logo.png" alt="MyFin" onerror="this.onerror=null; this.src='https://via.placeholder.com/120x60?text=MyFin';">
        </div>
        <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Selamat Datang</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Silakan masuk ke akun Anda</p>
        
        <form method="POST" action="/login">
            @csrf
            @if(session('success'))
                <div style="background: #d1fae5; color: #065f46; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem;">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            
            <button type="submit" class="btn-login">Masuk ke Portal</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            Belum punya akun? <a href="/register" style="color: #2563eb; text-decoration: none; font-weight: 600;">Daftar di sini</a>
        </div>

        <div class="credentials-hint">
            <strong>Akun Demo Tersedia:</strong>
            <ul>
                <li>admin@polinema.ac.id (Admin)</li>
                <li>sri@kantin.com (Kantin)</li>
                <li>budi@student.com (Mahasiswa)</li>
            </ul>
            <div style="margin-top: 5px;">*Semua password: <b>password</b></div>
        </div>
    </div>
</body>
</html>

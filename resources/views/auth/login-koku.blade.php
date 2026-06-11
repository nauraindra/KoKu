<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login KoKu</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('css/koku.css') }}"> --}}
    <link rel="stylesheet" href="/css/koku.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <section class="login-showcase">
            <div class="brand-login">
                <div class="brand-logo">
                    <i data-lucide="building-2"></i>
                </div>
                <div>
                    <h3>KoKu</h3>
                    <span>Modern Kost Management</span>
                </div>
            </div>

            <div class="showcase-content">
                <h1>Sistem Pengelolaan Kost</h1>
                <p>
                    Kelola kamar, penyewa, dan pembayaran
                    dalam satu dashboard yang sederhana.
                </p>
            </div>
        </section>

        <section class="login-card">
            <div class="login-header">
                <h2>Masuk ke KoKu</h2>
                <p>Gunakan akun admin atau penyewa untuk melanjutkan.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger custom-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success custom-alert">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group-modern">
                    <label>Email</label>
                    <div class="input-icon">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Password</label>
                    <div class="input-icon">
                        <i data-lucide="lock"></i>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="remember-check">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button class="btn-premium w-100">Login</button>
            </form>

            <div class="auth-switch">
                <span>Belum punya akun penyewa?</span>
                <a href="{{ route('register') }}">Daftar di sini</a>
            </div>

            <div class="demo-box">
                <small>Admin: admin@koku.test / Admin123!</small>
                <small>Penyewa: penyewa@koku.test / Tenant123!</small>
            </div>
        </section>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

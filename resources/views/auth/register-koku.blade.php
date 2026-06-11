<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register KoKu</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/koku.css') }}">
</head>
<body class="login-body">
    <div class="login-wrapper register-wrapper">
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
                <h1>Daftar sebagai penyewa kost.</h1>
                <p>
                    Setelah mendaftar, Anda dapat melihat informasi kamar, tagihan,
                    riwayat pembayaran, cuaca, dan preferensi tampilan pribadi.
                </p>
            </div>
        </section>

        <section class="login-card">
            <div class="login-header">
                <h2>Daftar Penyewa</h2>
                <p>Buat akun baru untuk masuk ke dashboard penyewa.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger custom-alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group-modern">
                    <label>Nama Lengkap</label>
                    <div class="input-icon">
                        <i data-lucide="user"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Email</label>
                    <div class="input-icon">
                        <i data-lucide="mail"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>No. Telepon</label>
                    <div class="input-icon">
                        <i data-lucide="phone"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Opsional">
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Alamat</label>
                    <div class="input-icon">
                        <i data-lucide="map-pin"></i>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="Opsional">
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Password</label>
                    <div class="input-icon">
                        <i data-lucide="lock"></i>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label>Konfirmasi Password</label>
                    <div class="input-icon">
                        <i data-lucide="shield-check"></i>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>

                <button class="btn-premium w-100">Daftar Sekarang</button>
            </form>

            <div class="auth-switch">
                <span>Sudah punya akun?</span>
                <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </section>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

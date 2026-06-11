<!DOCTYPE html>
<html lang="id" data-theme="{{ auth()->user()->preference->theme ?? 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoKu - Modern Kost Management</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('css/koku.css') }}"> --}}
    <link rel="stylesheet" href="/css/koku.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <button type="button" class="sidebar-close" data-sidebar-close>
            <i data-lucide="x"></i>
        </button>
        <div class="brand">
            <div class="brand-logo">
                <i data-lucide="building-2"></i>
            </div>
            <div>
                <h4>KoKu</h4>
                <span>Kost Management</span>
            </div>
        </div>

        <div class="nav-section">
            <p>Main Menu</p>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.tenants.index') }}" class="nav-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                    <i data-lucide="users"></i>
                    <span>Penyewa</span>
                </a>

                <a href="{{ route('admin.rooms.index') }}" class="nav-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                    <i data-lucide="door-open"></i>
                    <span>Kamar</span>
                </a>

                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i data-lucide="wallet-cards"></i>
                    <span>Riwayat Transaksi</span>
                </a>
            @else
                <a href="{{ route('tenant.dashboard') }}" class="nav-item {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('tenant.rooms.index') }}" class="nav-item {{ request()->routeIs('tenant.rooms.*') ? 'active' : '' }}">
                    <i data-lucide="door-open"></i>
                    <span>Kamar</span>
                </a>

                <a href="{{ route('tenant.payments.index') }}" class="nav-item {{ request()->routeIs('tenant.payments.*') ? 'active' : '' }}">
                    <i data-lucide="receipt"></i>
                    <span>Riwayat Pembayaran</span>
                </a>

                <a href="{{ route('tenant.profile.edit') }}" class="nav-item {{ request()->routeIs('tenant.profile.*') ? 'active' : '' }}">
                    <i data-lucide="user-round"></i>
                    <span>Profil</span>
                </a>
            @endif
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item logout-btn">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <nav class="topbar">
            <button type="button" class="icon-btn mobile-menu" data-sidebar-toggle>
                <i data-lucide="menu"></i>
            </button>

            <div>
                <h1>@yield('title')</h1>
                <p>@yield('subtitle')</p>
            </div>

            <div class="topbar-actions">
                <button type="button" class="icon-btn" data-bs-toggle="modal" data-bs-target="#preferenceModal">
                    <i data-lucide="settings"></i>
                </button>

                <div class="user-chip">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                             class="topbar-avatar-img"
                             alt="Foto profil">
                    @else
                        <div class="avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success custom-alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger custom-alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger custom-alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="page-content">
            @yield('content')
        </section>

        <footer class="footer">
            <span>KoKu</span>
            <span>Sistem Manajemen Kost Modern</span>
        </footer>
    </main>
</div>

<div class="modal fade" id="preferenceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content premium-modal" method="POST" action="{{ route('preferences.update') }}">
            @csrf

            @php($pref = auth()->user()->preference)

            <div class="modal-header">
                <div>
                    <h5>Preferensi Tampilan</h5>
                    <p>Atur tema, warna, dan lokasi cuaca.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Tema</label>
                <select name="theme" class="form-select mb-3">
                    <option value="light" @selected(($pref->theme ?? 'light') === 'light')>Light Mode</option>
                    <option value="dark" @selected(($pref->theme ?? 'light') === 'dark')>Dark Mode</option>
                </select>

                <label class="form-label">Kota Cuaca</label>
                <input type="text"
                       name="weather_city"
                       id="weatherCity"
                       class="form-control mb-3"
                       value="{{ $pref->weather_city ?? 'Jakarta' }}">
                <button
                    type="button"
                    id="clearWeatherCity"
                    class="btn btn-outline-secondary btn-sm mb-3">
                    Reset Kota Cuaca
                </button>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Latitude</label>
                        <input type="text"
                               name="latitude"
                               class="form-control"
                               value="{{ $pref->latitude ?? '-6.200000' }}">
                    </div>

                    <div class="col-6">
                        <label class="form-label">Longitude</label>
                        <input type="text"
                               name="longitude"
                               class="form-control"
                               value="{{ $pref->longitude ?? '106.816666' }}">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-premium w-100">Simpan Preferensi</button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/koku.js') }}"></script>

<script>
    lucide.createIcons();
</script>

@stack('scripts')
</body>
</html>

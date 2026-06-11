@extends('layouts.app')

@section('title', 'Data Penyewa')
@section('subtitle', 'Kelola semua penyewa: akun terdaftar, sedang booking, dan yang menempati kamar.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Total Penyewa</p>
            <h3 class="fw-bold mb-0">{{ $tenantStats['total'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Menempati Kamar</p>
            <h3 class="fw-bold mb-0">{{ $tenantStats['occupying'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Sedang Booking</p>
            <h3 class="fw-bold mb-0">{{ $tenantStats['booking'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Akun Tanpa Kamar</p>
            <h3 class="fw-bold mb-0">{{ $tenantStats['account_only'] }}</h3>
        </div>
    </div>
</div>

<div class="koku-card mb-4">
    <h5 class="fw-bold mb-3">Tambah Penyewa</h5>

    <form method="POST" action="{{ route('admin.tenants.store') }}" class="row g-3">
        @csrf

        <div class="col-md-6">
            <label class="form-label">Nama Penyewa</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Kamar</label>
            <select name="room_id" class="form-select">
                <option value="">Belum memilih kamar</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }} - @rupiah($room->price)</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label">Alamat</label>
            <input type="text" name="address" class="form-control">
        </div>

        <div class="col-md-4">
            <label class="form-label">Status Akun</label>
            <select name="status" class="form-select">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn-premium">Tambah Penyewa</button>
        </div>
    </form>
</div>

<div class="koku-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Penyewa</th>
                <th>Kontak</th>
                <th>Status Hunian</th>
                <th>Kamar</th>
                <th>Status Akun</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($tenant->profile_photo)
                                <img src="{{ asset('storage/' . $tenant->profile_photo) }}"
                                     style="width:44px;height:44px;border-radius:16px;object-fit:cover;">
                            @else
                                <div class="avatar" style="width:44px;height:44px;">
                                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <strong>{{ $tenant->name }}</strong>
                                <small class="d-block text-muted">{{ $tenant->email }}</small>
                            </div>
                        </div>
                    </td>

                    <td>{{ $tenant->phone ?? '-' }}</td>

                    <td>
                        @if($tenant->room_id)
                            <span class="badge-soft badge-paid">Menempati Kamar</span>
                        @elseif($tenant->activeBooking)
                            <span class="badge-soft badge-filled">Sedang Booking</span>
                        @else
                            <span class="badge-soft badge-maintenance">Akun Tanpa Kamar</span>
                        @endif
                    </td>

                    <td>
                        @if($tenant->room)
                            {{ $tenant->room->room_number }}
                        @elseif($tenant->activeBooking)
                            {{ $tenant->activeBooking->room->room_number ?? '-' }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        <span class="badge-soft {{ $tenant->status === 'aktif' ? 'badge-paid' : 'badge-unpaid' }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>

                    <td class="text-end">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            Edit
                        </a>

                        <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus penyewa ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">Belum ada data penyewa.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $tenants->links() }}
    </div>
</div>
@endsection

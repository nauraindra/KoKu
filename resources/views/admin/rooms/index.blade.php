@extends('layouts.app')

@section('title', 'Data Kamar')
@section('subtitle', 'Kelola kamar, foto kamar, status ketersediaan, dan penghuni.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Total Kamar</p>
            <h3 class="fw-bold mb-0">{{ $roomStats['total'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Tersedia</p>
            <h3 class="fw-bold mb-0">{{ $roomStats['available'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Ditempati</p>
            <h3 class="fw-bold mb-0">{{ $roomStats['occupied'] ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Perawatan</p>
            <h3 class="fw-bold mb-0">{{ $roomStats['maintenance'] ?? 0 }}</h3>
        </div>
    </div>
</div>

<div class="koku-card mb-4">
    <h5 class="fw-bold mb-3">Tambah Kamar</h5>

    <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" class="row g-3">
        @csrf

        <div class="col-md-3">
            <label class="form-label">Nomor Kamar</label>
            <input type="text" name="room_number" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Tipe</label>
            <input type="text" name="type" class="form-control" value="Standard" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Harga</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="kosong">Tersedia</option>
                <option value="booking">Sedang Dibooking</option>
                <option value="terisi">Ditempati</option>
                <option value="maintenance">Perawatan</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Foto Kamar</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>

        <div class="col-md-6">
            <label class="form-label">Deskripsi</label>
            <input type="text" name="description" class="form-control">
        </div>

        <div class="col-12">
            <button class="btn-premium">Tambah Kamar</button>
        </div>
    </form>
</div>

<div class="row g-4">
    @forelse($rooms as $room)
        <div class="col-md-6 col-xl-4">
            <div class="koku-card h-100">
                <div class="room-photo mb-3">
                    @if($room->photo)
                        <img src="{{ asset('storage/' . $room->photo) }}" alt="Foto kamar {{ $room->room_number }}">
                    @else
                        <div class="room-photo-placeholder">
                            <i data-lucide="image"></i>
                            <span>Belum ada foto kamar</span>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $room->room_number }}</h4>
                        <p class="text-muted mb-0">{{ $room->type }}</p>
                    </div>

                    @if($room->status === 'kosong')
                        <span class="badge-soft badge-paid">Tersedia</span>
                    @elseif($room->status === 'booking')
                        <span class="badge-soft badge-filled">Sedang Dibooking</span>
                    @elseif($room->status === 'terisi')
                        <span class="badge-soft badge-unpaid">Ditempati</span>
                    @else
                        <span class="badge-soft badge-maintenance">Perawatan</span>
                    @endif
                </div>

                <h5 class="fw-bold mb-2">@rupiah($room->price)</h5>
                <p class="text-muted">{{ $room->description ?? 'Tidak ada deskripsi.' }}</p>

                <div class="mb-3">
                    <small class="text-muted d-block">Penghuni / Booking</small>

                    @if($room->tenant)
                        <strong>{{ $room->tenant->name }}</strong>
                    @elseif($room->activeBooking)
                        <strong>{{ $room->activeBooking->tenant->name ?? '-' }}</strong>
                    @else
                        <strong>-</strong>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('admin.rooms.show', $room) }}"
                       class="btn btn-sm btn-outline-secondary rounded-pill">
                        Detail
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="row g-2">
                    @csrf
                    @method('PUT')

                    <div class="col-6">
                        <input type="text" name="room_number" value="{{ $room->room_number }}" class="form-control">
                    </div>

                    <div class="col-6">
                        <input type="text" name="type" value="{{ $room->type }}" class="form-control">
                    </div>

                    <div class="col-6">
                        <input type="number" name="price" value="{{ $room->price }}" class="form-control">
                    </div>

                    <div class="col-6">
                        <select name="status" class="form-select">
                            <option value="kosong" @selected($room->status === 'kosong')>Tersedia</option>
                            <option value="booking" @selected($room->status === 'booking')>Sedang Dibooking</option>
                            <option value="terisi" @selected($room->status === 'terisi')>Ditempati</option>
                            <option value="maintenance" @selected($room->status === 'maintenance')>Perawatan</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>

                    <div class="col-12">
                        <input type="text" name="description" value="{{ $room->description }}" class="form-control">
                    </div>

                    <div class="col-12">
                        <button class="btn btn-sm btn-premium">Update</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="mt-2"
                      onsubmit="return confirm('Hapus kamar ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="koku-card text-muted">Belum ada data kamar.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $rooms->links() }}
</div>
@endsection

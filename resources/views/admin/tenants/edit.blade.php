@extends('layouts.app')

@section('title', 'Edit Penyewa')
@section('subtitle', 'Perbarui informasi penyewa.')

@section('content')
<div class="koku-card">
    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
            <label class="form-label">Nama Penyewa</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti password.</small>
        </div>

        <div class="col-md-6">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Kamar</label>
            <select name="room_id" class="form-select">
                <option value="">Belum memilih kamar</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" @selected($tenant->room_id === $room->id)>
                        {{ $room->room_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="aktif" @selected($tenant->status === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected($tenant->status === 'nonaktif')>Nonaktif</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="3">{{ old('address', $tenant->address) }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-koku">Update</button>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary rounded-pill">Kembali</a>
        </div>
    </form>
</div>
@endsection

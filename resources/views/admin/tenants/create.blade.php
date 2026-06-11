@extends('layouts.app')

@section('title', 'Tambah Penyewa')
@section('subtitle', 'Form tambah penyewa baru.')

@section('content')
<div class="koku-card">
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

        <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Kamar</label>
            <select name="room_id" class="form-select">
                <option value="">Belum memilih kamar</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="3"></textarea>
        </div>

        <div class="col-12">
            <button class="btn btn-koku">Simpan</button>
        </div>
    </form>
</div>
@endsection

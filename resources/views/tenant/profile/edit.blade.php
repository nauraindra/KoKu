@extends('layouts.app')

@section('title', 'Profil Saya')
@section('subtitle', 'Kelola foto profil, nama, nomor HP, dan alamat.')

@section('content')
<div class="koku-card">
    <form method="POST" action="{{ route('tenant.profile.update') }}" enctype="multipart/form-data" class="row g-4">
        @csrf
        @method('PUT')

        <div class="col-md-4">
            <div class="profile-card-preview">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}"
                         class="profile-photo-large"
                         alt="Foto profil">
                @else
                    <div class="avatar profile-avatar-large">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <h5 class="fw-bold mt-3 mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->email }}</p>

                <input type="file" name="profile_photo" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="col-md-8">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control mb-3" value="{{ old('name', $user->name) }}" required>

            <label class="form-label">Email</label>
            <input type="email" class="form-control mb-3" value="{{ $user->email }}" readonly>

            <label class="form-label">No. HP</label>
            <input type="text" name="phone" class="form-control mb-3" value="{{ old('phone', $user->phone) }}">

            <label class="form-label">Alamat</label>
            <input type="text" name="address" class="form-control mb-4" value="{{ old('address', $user->address) }}">

            <button class="btn-premium">Simpan Profil</button>
        </div>
    </form>
</div>
@endsection

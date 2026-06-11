@extends('layouts.app')

@section('title', 'Detail Kamar')
@section('subtitle', 'Lihat detail kamar sebelum melakukan booking.')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="koku-card">
            <div class="room-detail-photo mb-4">
                @if($room->photo)
                    <img src="{{ asset('storage/' . $room->photo) }}" alt="Foto kamar {{ $room->room_number }}">
                @else
                    <div class="room-photo-placeholder">
                        <i data-lucide="image"></i>
                        <span>Foto kamar belum tersedia</span>
                    </div>
                @endif
            </div>

            <h2 class="fw-bold mb-1">{{ $room->room_number }}</h2>
            <p class="text-muted mb-3">{{ $room->type }}</p>

            @if($room->status === 'kosong')
                <span class="badge-soft badge-paid">Tersedia</span>
            @elseif($room->status === 'booking')
                <span class="badge-soft badge-filled">Sedang Dibooking</span>
            @elseif($room->status === 'terisi')
                <span class="badge-soft badge-unpaid">Ditempati</span>
            @else
                <span class="badge-soft badge-maintenance">Perawatan</span>
            @endif

            <hr>

            <h4 class="fw-bold">@rupiah($room->price) / bulan</h4>
            <p class="text-muted">{{ $room->description ?? 'Tidak ada deskripsi.' }}</p>

            <div class="d-flex gap-2">
                <a href="{{ route('tenant.rooms.index') }}" class="btn btn-outline-secondary rounded-pill">
                    Kembali
                </a>

                @if(!$activeBooking && !auth()->user()->room_id && $room->status === 'kosong')
                    <form method="POST" action="{{ route('tenant.rooms.booking', $room) }}">
                        @csrf
                        <button class="btn-premium"
                                onclick="return confirm('Booking kamar ini sekarang?')">
                            Booking Kamar
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="koku-card">
            <h5 class="fw-bold mb-3">Informasi Ketersediaan</h5>

            @if($room->status === 'kosong')
                <p class="text-muted">
                    Kamar ini tersedia dan dapat langsung dibooking oleh penyewa.
                    Setelah booking, tagihan pertama akan otomatis dibuat pada riwayat pembayaran.
                </p>
            @elseif($room->status === 'booking')
                <p class="text-muted">
                    Kamar ini sedang dalam proses booking oleh penyewa lain.
                </p>
            @elseif($room->status === 'terisi')
                <p class="text-muted">
                    Kamar ini sudah ditempati dan belum tersedia untuk penyewa baru.
                </p>
            @else
                <p class="text-muted">
                    Kamar ini sedang dalam perawatan dan belum dapat dibooking.
                </p>
            @endif

            <div class="mt-4">
                <small class="text-muted d-block">Status Anda</small>

                @if(auth()->user()->room_id)
                    <strong>Sudah menempati kamar.</strong>
                @elseif($activeBooking)
                    <strong>Sudah memiliki booking aktif.</strong>
                @else
                    <strong>Belum memiliki kamar.</strong>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

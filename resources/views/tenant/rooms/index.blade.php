@extends('layouts.app')

@section('title', 'Kamar')
@section('subtitle', 'Lihat kamar tersedia, kamar yang ditempati, dan pilih kamar sesuai kebutuhan.')

@section('content')
@if($activeBooking)
    <div class="koku-card mb-4">
        <h5 class="fw-bold mb-1">Status Kamar Anda</h5>
        <p class="text-muted mb-0">
            Anda sedang menempati kamar
            <strong>{{ $activeBooking->room->room_number }}</strong>.
        </p>
    </div>
@endif

<div class="row g-4">
    @foreach($rooms as $room)
        <div class="col-md-6 col-xl-4">
            <div class="koku-card h-100 d-flex flex-column">

                <div class="room-photo mb-3">
                    @if($room->photo)
                        <img src="{{ asset('storage/' . $room->photo) }}"
                             alt="Foto kamar {{ $room->room_number }}">
                    @else
                        <div class="room-photo-placeholder">
                            <i data-lucide="image"></i>
                            <span>Foto kamar belum tersedia</span>
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

                <h5 class="fw-bold">@rupiah($room->price) / bulan</h5>

                <p class="text-muted mb-3">
                    {{ $room->description ?? 'Tidak ada deskripsi.' }}
                </p>

                {{-- Area info dengan tinggi tetap --}}
                <div style="min-height: 48px;">
                    @if($room->tenant)
                        <small class="text-muted d-block">
                            Ditempati oleh {{ $room->tenant->name }}
                        </small>
                    @elseif($room->activeBooking)
                        <small class="text-muted d-block">
                            Sedang dibooking oleh {{ $room->activeBooking->tenant->name ?? '-' }}
                        </small>
                    @else
                        <small class="text-muted d-block">
                            Kamar siap dipilih
                        </small>
                    @endif
                </div>

                {{-- Tombol selalu di bawah --}}
                <div class="mt-auto pt-3">
                    <a href="{{ route('tenant.rooms.show', $room) }}"
                       class="btn btn-outline-secondary rounded-pill w-100 mb-2">
                        Lihat Detail
                    </a>

                    @if(auth()->user()->room_id === $room->id)
                        <button class="btn btn-outline-secondary rounded-pill w-100" disabled>
                            Kamar Anda
                        </button>

                    @elseif($activeBooking || auth()->user()->room_id)
                        <button class="btn btn-outline-secondary rounded-pill w-100" disabled>
                            Anda Sudah Memiliki Kamar
                        </button>

                    @elseif($room->status === 'kosong')
                        <form method="POST" action="{{ route('tenant.rooms.booking', $room) }}">
                            @csrf
                            <button type="submit"
                                    class="btn-premium w-100"
                                    onclick="return confirm('Booking kamar ini sekarang?')">
                                Booking Kamar
                            </button>
                        </form>

                    @else
                        <button class="btn btn-outline-secondary rounded-pill w-100" disabled>
                            Tidak Tersedia
                        </button>
                    @endif
                </div>

            </div>
        </div>
    @endforeach
</div>
@endsection

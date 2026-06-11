@extends('layouts.app')

@section('title', 'Detail Kamar')
@section('subtitle', 'Informasi lengkap kamar, penghuni, status, dan riwayat transaksi.')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="koku-card">
            <div class="room-detail-photo mb-4">
                @if($room->photo)
                    <img src="{{ asset('storage/' . $room->photo) }}" alt="Foto kamar {{ $room->room_number }}">
                @else
                    <div class="room-photo-placeholder">
                        <i data-lucide="image"></i>
                        <span>Belum ada foto kamar</span>
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

            <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary rounded-pill">
                Kembali
            </a>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="koku-card mb-4">
            <h5 class="fw-bold mb-3">Informasi Penghuni</h5>

            @if($room->tenant)
                <div class="d-flex align-items-center gap-3">
                    @if($room->tenant->profile_photo)
                        <img src="{{ asset('storage/' . $room->tenant->profile_photo) }}"
                             style="width:58px;height:58px;border-radius:20px;object-fit:cover;">
                    @else
                        <div class="avatar" style="width:58px;height:58px;">
                            {{ strtoupper(substr($room->tenant->name, 0, 1)) }}
                        </div>
                    @endif

                    <div>
                        <strong>{{ $room->tenant->name }}</strong>
                        <small class="d-block text-muted">{{ $room->tenant->email }}</small>
                        <small class="d-block text-muted">{{ $room->tenant->phone ?? '-' }}</small>
                    </div>
                </div>
            @elseif($room->activeBooking)
                <p class="mb-1">Sedang dibooking oleh:</p>
                <strong>{{ $room->activeBooking->tenant->name ?? '-' }}</strong>
            @else
                <p class="text-muted mb-0">Belum ada penghuni atau booking aktif.</p>
            @endif
        </div>

        <div class="koku-card">
            <h5 class="fw-bold mb-3">Riwayat Transaksi Kamar</h5>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Bulan</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($room->payments as $payment)
                        <tr>
                            <td>{{ $payment->tenant->name ?? '-' }}</td>
                            <td>{{ $payment->month }}</td>
                            <td>@rupiah($payment->amount)</td>
                            <td>
                                @if($payment->status === 'lunas')
                                    <span class="badge-soft badge-paid">Lunas</span>
                                @elseif($payment->status === 'menunggu_verifikasi')
                                    <span class="badge-soft badge-filled">Menunggu Verifikasi</span>
                                @elseif($payment->status === 'ditolak')
                                    <span class="badge-soft badge-unpaid">Ditolak</span>
                                @else
                                    <span class="badge-soft badge-unpaid">Belum Lunas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Belum ada transaksi untuk kamar ini.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

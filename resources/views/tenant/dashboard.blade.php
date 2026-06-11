@extends('layouts.app')

@section('title', 'Dashboard Penyewa')
@section('subtitle', 'Ringkasan kamar, tagihan, dan status pembayaran Anda.')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="koku-card gradient-card h-100">
            <h5 class="fw-bold mb-3">Kamar Saya</h5>

            @if($user->room)
                <h2 class="fw-bold">{{ $user->room->room_number }}</h2>
                <p class="mb-1">{{ $user->room->type }}</p>
                <p class="mb-0">@rupiah($user->room->price) / bulan</p>
            @else
                <h4 class="fw-bold">Belum Ada Kamar</h4>
                <p>Silakan pilih kamar yang tersedia untuk mulai menempati kos.</p>
                <a href="{{ route('tenant.rooms.index') }}" class="btn btn-light rounded-pill mt-2">
                    Pilih Kamar
                </a>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="koku-card h-100">
            <p class="text-muted mb-1">Tagihan Aktif</p>

            @if($nextBill)
                <h3 class="fw-bold">@rupiah($nextBill->amount)</h3>

                @if($nextBill->status === 'menunggu_verifikasi')
                    <span class="badge-soft badge-filled">Menunggu Verifikasi</span>
                @elseif($nextBill->status === 'ditolak')
                    <span class="badge-soft badge-unpaid">Ditolak</span>
                @else
                    <span class="badge-soft badge-unpaid">Belum Lunas</span>
                @endif

                <div class="mt-3">
                    <a href="{{ route('tenant.payments.index') }}" class="btn btn-sm btn-premium">
                        Lihat Tagihan
                    </a>
                </div>
            @else
                <h3 class="fw-bold">Aman</h3>
                <p class="text-muted mb-0">Tidak ada tagihan aktif.</p>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="koku-card h-100">
            <p class="text-muted mb-1">Total Pembayaran Lunas</p>
            <h3 class="fw-bold">@rupiah($paidTotal)</h3>
            <p class="text-muted mb-0">Akumulasi pembayaran yang sudah diverifikasi.</p>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="koku-card gradient-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Weather Card</h5>
                    <p class="mb-0">Informasi cuaca sesuai preferensi</p>
                </div>
                <i data-lucide="cloud-sun"></i>
            </div>

            <div data-weather-box>
                <p class="mb-0">Memuat data cuaca...</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="koku-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Pembayaran Terbaru</h5>
                    <p class="text-muted mb-0">Transaksi terakhir dari akun Anda.</p>
                </div>

                <a href="{{ route('tenant.payments.index') }}" class="btn btn-sm btn-premium">
                    Lihat Semua
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Kamar</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->month }}</td>
                            <td>{{ $payment->room->room_number ?? '-' }}</td>
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
                            <td colspan="4" class="text-muted">Belum ada pembayaran.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

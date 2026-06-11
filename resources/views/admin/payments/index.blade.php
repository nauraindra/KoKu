@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('subtitle', 'Pantau semua pembayaran penyewa, bukti transfer, dan status verifikasi.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Total Transaksi</p>
            <h3 class="fw-bold mb-0">{{ $paymentStats['total'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Menunggu Verifikasi</p>
            <h3 class="fw-bold mb-0">{{ $paymentStats['waiting'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Lunas</p>
            <h3 class="fw-bold mb-0">{{ $paymentStats['paid'] }}</h3>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card">
            <p class="text-muted mb-1">Total Pemasukan</p>
            <h3 class="fw-bold mb-0">@rupiah($paymentStats['income'])</h3>
        </div>
    </div>
</div>

<div class="koku-card mb-4">
    <h5 class="fw-bold mb-3">Buat Tagihan Manual</h5>

    <form method="POST" action="{{ route('admin.payments.store') }}" class="row g-3">
        @csrf

        <div class="col-md-3">
            <label class="form-label">Penyewa</label>
            <select name="tenant_id" class="form-select" required>
                <option value="">Pilih penyewa</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">
                        {{ $tenant->name }} - {{ $tenant->room->room_number ?? 'Tanpa kamar' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Bulan</label>
            <input type="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Nominal</label>
            <input type="number" name="amount" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="belum_lunas">Belum Lunas</option>
                <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                <option value="lunas">Lunas</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Catatan</label>
            <input type="text" name="notes" class="form-control">
        </div>

        <div class="col-12">
            <button class="btn-premium">Buat Tagihan</button>
        </div>
    </form>
</div>

<div class="koku-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Penyewa</th>
                <th>Kamar</th>
                <th>Bulan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Metode</th>
                <th>Bukti</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>
                        <strong>{{ $payment->tenant->name ?? '-' }}</strong>
                        <small class="d-block text-muted">{{ $payment->tenant->email ?? '-' }}</small>
                    </td>

                    <td>{{ $payment->room->room_number ?? '-' }}</td>
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

                    <td>{{ $payment->payment_method ?? '-' }}</td>

                    <td>
                        @if($payment->proof_photo)
                            <a href="{{ asset('storage/' . $payment->proof_photo) }}" target="_blank" class="fw-bold">
                                Lihat Bukti
                            </a>
                        @else
                            -
                        @endif
                    </td>

                    <td class="text-end">
                        @if($payment->status === 'menunggu_verifikasi')
                            <form action="{{ route('admin.payments.paid', $payment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-premium">Verifikasi</button>
                            </form>

                            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-danger rounded-pill">Tolak</button>
                            </form>
                        @elseif($payment->status !== 'lunas')
                            <form action="{{ route('admin.payments.paid', $payment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-premium">Lunas</button>
                            </form>
                        @endif

                        <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-secondary rounded-pill">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted">Belum ada transaksi pembayaran.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>
@endsection

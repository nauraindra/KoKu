@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('subtitle', 'Ringkasan data kos, pembayaran, kunjungan, dan penyewa aktif.')

@section('content')
<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="koku-card stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Penyewa</p>
                    <div class="stat-number">{{ $totalTenants }}</div>
                </div>
                <div class="stat-icon"><i data-lucide="users"></i></div>
            </div>
            <small class="text-muted">{{ $activeTenants }} penyewa aktif</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Kamar Kosong</p>
                    <div class="stat-number">{{ $availableRooms }}</div>
                </div>
                <div class="stat-icon"><i data-lucide="door-open"></i></div>
            </div>
            <small class="text-muted">Siap ditempati</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Pendapatan Bulan Ini</p>
                    <div class="stat-number">@rupiah($monthlyIncome)</div>
                </div>
                <div class="stat-icon"><i data-lucide="wallet"></i></div>
            </div>
            <small class="text-muted">Pembayaran lunas</small>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="koku-card stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Belum Lunas</p>
                    <div class="stat-number">{{ $unpaidBills }}</div>
                </div>
                <div class="stat-icon"><i data-lucide="receipt"></i></div>
            </div>
            <small class="text-muted">Tagihan perlu dipantau</small>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="koku-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Statistik Kunjungan</h5>
                    <p class="text-muted mb-0">Aktivitas akses dashboard 7 hari terakhir</p>
                </div>
            </div>
            <canvas id="visitChart" height="120"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="koku-card gradient-card h-100">
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h5 class="fw-bold">Weather Card</h5>
                    <p class="mb-0 opacity-75">Informasi cuaca saat ini</p>
                </div>
                <i data-lucide="cloud-sun"></i>
            </div>
            <div data-weather-box>
                <p class="mb-0">Memuat data cuaca...</p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="koku-card">
            <h5 class="fw-bold mb-3">Live Search Penyewa</h5>
            <div class="search-wrapper">
                <input type="text" id="tenantSearch" class="form-control"
                       placeholder="Cari nama, email, atau nomor telepon">
                <div id="tenantSearchResult" class="search-result"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="koku-card">
            <h5 class="fw-bold mb-3">Pembayaran Terbaru</h5>
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
                    @forelse($latestPayments as $payment)
                        <tr>
                            <td>{{ $payment->tenant->name ?? '-' }}</td>
                            <td>{{ $payment->month }}</td>
                            <td>@rupiah($payment->amount)</td>
                            <td>
                                <span class="badge-soft {{ $payment->status === 'lunas' ? 'badge-paid' : 'badge-unpaid' }}">
                                    {{ $payment->status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                                </span>
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

@push('scripts')
<script>
const visitChart = document.getElementById('visitChart');

if (visitChart) {
    new Chart(visitChart, {
        type: 'line',
        data: {
            labels: @json($visitLabels),
            datasets: [{
                label: 'Kunjungan',
                data: @json($visitData),
                tension: .45,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
}
</script>
@endpush

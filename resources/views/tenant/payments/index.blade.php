@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('subtitle', 'Lihat tagihan, status transaksi, dan kirim bukti pembayaran.')

@section('content')
<div class="koku-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Bulan</th>
                <th>Kamar</th>
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
                        @if(in_array($payment->status, ['belum_lunas', 'ditolak']))
                            <button type="button"
                                    class="btn btn-sm btn-premium"
                                    data-bs-toggle="modal"
                                    data-bs-target="#payModal{{ $payment->id }}">
                                Bayar
                            </button>
                        @elseif($payment->status === 'menunggu_verifikasi')
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                                Diproses
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                                Selesai
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-muted">Belum ada riwayat pembayaran.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>

@foreach($payments as $payment)
    @if(in_array($payment->status, ['belum_lunas', 'ditolak']))
        <div class="modal fade" id="payModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content premium-modal"
                      method="POST"
                      enctype="multipart/form-data"
                      action="{{ route('tenant.payments.pay', $payment) }}">
                    @csrf

                    <div class="modal-header">
                        <div>
                            <h5>Bayar Tagihan</h5>
                            <p>{{ $payment->month }} - @rupiah($payment->amount)</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="payment_method"
                                class="form-select mb-3 payment-method-select"
                                data-payment-id="{{ $payment->id }}"
                                required>
                            <option value="">Pilih metode</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Tunai">Tunai</option>
                        </select>

                        <div id="bankInfo{{ $payment->id }}" class="payment-info-box d-none mb-3">
                            <h6 class="fw-bold mb-2">Informasi Rekening Pemilik Kos</h6>
                            <div class="payment-info-row">
                                <span>Bank</span>
                                <strong>BCA</strong>
                            </div>
                            <div class="payment-info-row">
                                <span>No. Rekening</span>
                                <strong>1234567890</strong>
                            </div>
                            <div class="payment-info-row">
                                <span>Atas Nama</span>
                                <strong>Admin KoKu</strong>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.
                            </small>
                        </div>

                        <div id="ewalletInfo{{ $payment->id }}" class="payment-info-box d-none mb-3">
                            <h6 class="fw-bold mb-2">Informasi E-Wallet Pemilik Kos</h6>
                            <div class="payment-info-row">
                                <span>E-Wallet</span>
                                <strong>DANA / OVO / GoPay</strong>
                            </div>
                            <div class="payment-info-row">
                                <span>Nomor</span>
                                <strong>081234567890</strong>
                            </div>
                            <div class="payment-info-row">
                                <span>Atas Nama</span>
                                <strong>Admin KoKu</strong>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Kirim pembayaran ke nomor e-wallet di atas, lalu upload bukti pembayaran.
                            </small>
                        </div>

                        <div id="cashInfo{{ $payment->id }}" class="payment-info-box d-none mb-3">
                            <h6 class="fw-bold mb-2">Informasi Pembayaran Tunai</h6>
                            <p class="text-muted mb-0">
                                Pembayaran tunai dilakukan langsung kepada pemilik kos.
                                Setelah membayar, admin akan memverifikasi pembayaran secara manual.
                            </p>
                        </div>

                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file"
                               name="proof_photo"
                               class="form-control"
                               accept="image/*">

                        <small class="text-muted d-block mt-2">
                            Bukti pembayaran boleh berupa JPG, PNG, atau WEBP maksimal 2 MB.
                        </small>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn-premium w-100">
                            Kirim Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selects = document.querySelectorAll('.payment-method-select');

        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                const paymentId = this.dataset.paymentId;
                const method = this.value;

                const bankInfo = document.getElementById('bankInfo' + paymentId);
                const ewalletInfo = document.getElementById('ewalletInfo' + paymentId);
                const cashInfo = document.getElementById('cashInfo' + paymentId);

                bankInfo.classList.add('d-none');
                ewalletInfo.classList.add('d-none');
                cashInfo.classList.add('d-none');

                if (method === 'Transfer Bank') {
                    bankInfo.classList.remove('d-none');
                }

                if (method === 'E-Wallet') {
                    ewalletInfo.classList.remove('d-none');
                }

                if (method === 'Tunai') {
                    cashInfo.classList.remove('d-none');
                }
            });
        });
    });
</script>
@endpush

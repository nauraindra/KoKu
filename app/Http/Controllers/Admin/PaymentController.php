<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['tenant', 'room'])
            ->latest()
            ->paginate(10);

        $tenants = User::with('room')
            ->where('role', 'penyewa')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $paymentStats = [
            'total' => Payment::count(),
            'paid' => Payment::where('status', 'lunas')->count(),
            'waiting' => Payment::where('status', 'menunggu_verifikasi')->count(),
            'unpaid' => Payment::where('status', 'belum_lunas')->count(),
            'rejected' => Payment::where('status', 'ditolak')->count(),
            'income' => Payment::where('status', 'lunas')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'tenants', 'paymentStats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'exists:users,id'],
            'month' => [
                'required',
                'date_format:Y-m',
                Rule::unique('payments')->where(function ($query) use ($request) {
                    return $query->where('tenant_id', $request->tenant_id)
                        ->where('month', $request->month);
                }),
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:belum_lunas,menunggu_verifikasi,lunas,ditolak'],
            'notes' => ['nullable', 'string'],
        ], [
            'month.unique' => 'Tagihan untuk penyewa dan bulan ini sudah ada.',
        ]);

        $tenant = User::findOrFail($data['tenant_id']);

        $data['room_id'] = $tenant->room_id;
        $data['paid_at'] = $data['status'] === 'lunas' ? now() : null;

        Payment::create($data);

        return back()->with('success', 'Tagihan pembayaran berhasil dibuat.');
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'exists:users,id'],
            'month' => ['required', 'date_format:Y-m'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:belum_lunas,menunggu_verifikasi,lunas,ditolak'],
            'notes' => ['nullable', 'string'],
        ]);

        $tenant = User::findOrFail($data['tenant_id']);

        $data['room_id'] = $tenant->room_id;
        $data['paid_at'] = $data['status'] === 'lunas'
            ? ($payment->paid_at ?? now())
            : null;

        $payment->update($data);

        return back()->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'lunas',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi lunas.');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'ditolak',
            'paid_at' => null,
        ]);

        return back()->with('success', 'Pembayaran berhasil ditolak.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('success', 'Data pembayaran berhasil dihapus.');
    }
}

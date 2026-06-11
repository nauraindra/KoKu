<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function index()
    {
        $payments = Payment::with('room')
            ->where('tenant_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('tenant.payments.index', compact('payments'));
    }

    public function pay(Request $request, Payment $payment)
    {
        abort_if($payment->tenant_id !== auth()->id(), 403);

        if ($payment->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $data = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'proof_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $proofPath = $payment->proof_photo;

        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('payment-proofs', 'public');
        }

        $payment->update([
            'status' => 'menunggu_verifikasi',
            'payment_method' => $data['payment_method'],
            'proof_photo' => $proofPath,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dikirim dan menunggu verifikasi admin.');
    }
}

<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request, Room $room)
    {
        $user = auth()->user();

        if ($user->room_id) {
            return back()->with('error', 'Anda sudah menempati kamar.');
        }

        if ($user->bookings()->whereIn('status', ['menunggu', 'diterima'])->exists()) {
            return back()->with('error', 'Anda masih memiliki booking aktif.');
        }

        if ($room->status !== 'kosong') {
            return back()->with('error', 'Kamar ini tidak tersedia untuk booking.');
        }

        Booking::create([
            'tenant_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'diterima',
            'notes' => 'Booking otomatis dari penyewa.',
            'approved_at' => now(),
        ]);

        $room->update([
            'status' => 'terisi',
        ]);

        $user->update([
            'room_id' => $room->id,
            'status' => 'aktif',
        ]);

        Payment::firstOrCreate(
            [
                'tenant_id' => $user->id,
                'month' => now()->format('Y-m'),
            ],
            [
                'room_id' => $room->id,
                'amount' => $room->price,
                'status' => 'belum_lunas',
                'notes' => 'Tagihan pertama setelah booking kamar.',
            ]
        );

        return redirect()->route('tenant.dashboard')
            ->with('success', 'Booking berhasil. Anda sekarang menempati kamar ' . $room->room_number . '.');
    }
}

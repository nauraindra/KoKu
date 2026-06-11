<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;

class RoomBrowseController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['tenant', 'activeBooking.tenant'])
            ->orderBy('room_number')
            ->get();

        $activeBooking = auth()->user()
            ->bookings()
            ->with('room')
            ->whereIn('status', ['menunggu', 'diterima'])
            ->latest()
            ->first();

        return view('tenant.rooms.index', compact('rooms', 'activeBooking'));
    }

    public function show(Room $room)
    {
        $room->load(['tenant', 'activeBooking.tenant']);

        $activeBooking = auth()->user()
            ->bookings()
            ->with('room')
            ->whereIn('status', ['menunggu', 'diterima'])
            ->latest()
            ->first();

        return view('tenant.rooms.show', compact('room', 'activeBooking'));
    }
}

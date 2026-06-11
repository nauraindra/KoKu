<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['tenant', 'activeBooking.tenant'])
            ->orderBy('room_number')
            ->paginate(12);

        $roomStats = [
            'total' => Room::count(),
            'available' => Room::where('status', 'kosong')->count(),
            'booking' => Room::where('status', 'booking')->count(),
            'occupied' => Room::where('status', 'terisi')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];

        return view('admin.rooms.index', compact('rooms', 'roomStats'));
    }

    public function show(Room $room)
    {
        $room->load(['tenant', 'activeBooking.tenant', 'payments.tenant']);

        return view('admin.rooms.show', compact('room'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_number' => ['required', 'string', 'max:30', 'unique:rooms,room_number'],
            'type' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:kosong,booking,terisi,maintenance'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('room-photos', 'public');
        }

        Room::create($data);

        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('rooms', 'room_number')->ignore($room->id),
            ],
            'type' => ['required', 'string', 'max:80'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:kosong,booking,terisi,maintenance'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($room->tenant && $data['status'] === 'kosong') {
            return back()->with('error', 'Kamar masih ditempati penyewa, status tidak bisa diubah menjadi tersedia.');
        }

        if ($request->hasFile('photo')) {
            if ($room->photo) {
                Storage::disk('public')->delete($room->photo);
            }

            $data['photo'] = $request->file('photo')->store('room-photos', 'public');
        }

        $room->update($data);

        return back()->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->tenant) {
            return back()->with('error', 'Kamar masih ditempati penyewa.');
        }

        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }

        $room->delete();

        return back()->with('success', 'Kamar berhasil dihapus.');
    }
}

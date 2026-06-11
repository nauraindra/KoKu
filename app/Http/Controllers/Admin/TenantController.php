<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = User::with(['room', 'activeBooking.room'])
            ->where('role', 'penyewa')
            ->latest()
            ->paginate(10);

        $rooms = Room::where('status', 'kosong')
            ->orderBy('room_number')
            ->get();

        $tenantStats = [
            'total' => User::where('role', 'penyewa')->count(),
            'occupying' => User::where('role', 'penyewa')->whereNotNull('room_id')->count(),
            'booking' => User::where('role', 'penyewa')
                ->whereNull('room_id')
                ->whereHas('bookings', function ($query) {
                    $query->whereIn('status', ['menunggu', 'diterima']);
                })
                ->count(),
            'account_only' => User::where('role', 'penyewa')
                ->whereNull('room_id')
                ->whereDoesntHave('bookings', function ($query) {
                    $query->whereIn('status', ['menunggu', 'diterima']);
                })
                ->count(),
        ];

        return view('admin.tenants.index', compact('tenants', 'rooms', 'tenantStats'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'kosong')->orderBy('room_number')->get();

        return view('admin.tenants.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        if (! empty($data['room_id'])) {
            $room = Room::findOrFail($data['room_id']);

            if ($room->status !== 'kosong') {
                return back()->with('error', 'Kamar yang dipilih tidak tersedia.')->withInput();
            }
        }

        $data['role'] = 'penyewa';
        $data['password'] = Hash::make($data['password']);

        $tenant = User::create($data);

        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)->update(['status' => 'terisi']);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Data penyewa berhasil ditambahkan.');
    }

    public function edit(User $tenant)
    {
        abort_if($tenant->role !== 'penyewa', 404);

        $rooms = Room::where('status', 'kosong')
            ->orWhere('id', $tenant->room_id)
            ->orderBy('room_number')
            ->get();

        return view('admin.tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, User $tenant)
    {
        abort_if($tenant->role !== 'penyewa', 404);

        $oldRoomId = $tenant->room_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($tenant->id)],
            'password' => ['nullable', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        if (! empty($data['room_id']) && $data['room_id'] != $oldRoomId) {
            $newRoom = Room::findOrFail($data['room_id']);

            if ($newRoom->status !== 'kosong') {
                return back()->with('error', 'Kamar yang dipilih tidak tersedia.')->withInput();
            }
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $tenant->update($data);

        if ($oldRoomId && $oldRoomId != $tenant->room_id) {
            Room::where('id', $oldRoomId)->update(['status' => 'kosong']);
        }

        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)->update(['status' => 'terisi']);
            $tenant->bookings()
                ->whereIn('status', ['menunggu', 'diterima'])
                ->update(['status' => 'dibatalkan']);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Data penyewa berhasil diperbarui.');
    }

    public function destroy(User $tenant)
    {
        abort_if($tenant->role !== 'penyewa', 404);

        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)->update(['status' => 'kosong']);
        }

        $tenant->bookings()
            ->whereIn('status', ['menunggu', 'diterima'])
            ->update(['status' => 'dibatalkan']);

        $tenant->delete();

        return back()->with('success', 'Data penyewa berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q');

        $tenants = User::with(['room', 'activeBooking.room'])
            ->where('role', 'penyewa')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->limit(8)
            ->get();

        return response()->json($tenants);
    }
}

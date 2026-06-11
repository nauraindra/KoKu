<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $today = now();

        $totalTenants = User::where('role', 'penyewa')->count();
        $activeTenants = User::where('role', 'penyewa')->where('status', 'aktif')->count();
        $availableRooms = Room::where('status', 'kosong')->count();

        $monthlyIncome = Payment::where('status', 'lunas')
            ->whereMonth('paid_at', $today->month)
            ->whereYear('paid_at', $today->year)
            ->sum('amount');

        $unpaidBills = Payment::where('status', 'belum_lunas')->count();

        $latestTenants = User::with('room')
            ->where('role', 'penyewa')
            ->latest()
            ->limit(6)
            ->get();

        $latestPayments = Payment::with(['tenant', 'room'])
            ->latest()
            ->limit(6)
            ->get();

        $visits = Visit::select(
                DB::raw('DATE(visited_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('visited_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $visitLabels = [];
        $visitData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $visitLabels[] = Carbon::parse($date)->format('d M');
            $visitData[] = $visits->firstWhere('date', $date)->total ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'availableRooms',
            'monthlyIncome',
            'unpaidBills',
            'latestTenants',
            'latestPayments',
            'visitLabels',
            'visitData'
        ));
    }

    public function tenant()
    {
        $user = auth()->user()->load(['room', 'payments', 'activeBooking.room']);

        $nextBill = Payment::where('tenant_id', $user->id)
            ->whereIn('status', ['belum_lunas', 'menunggu_verifikasi', 'ditolak'])
            ->oldest('month')
            ->first();

        $paidTotal = Payment::where('tenant_id', $user->id)
            ->where('status', 'lunas')
            ->sum('amount');

        $payments = Payment::with('room')
            ->where('tenant_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('tenant.dashboard', compact('user', 'nextBill', 'paidTotal', 'payments'));
    }
}

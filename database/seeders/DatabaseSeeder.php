<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Preference;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roomA101 = Room::create([
            'room_number' => 'A-101',
            'type' => 'Standard',
            'price' => 750000,
            'status' => 'terisi',
            'description' => 'Kamar nyaman dengan kasur, lemari, dan meja belajar.',
            'photo' => null,
        ]);

        $roomA102 = Room::create([
            'room_number' => 'A-102',
            'type' => 'Standard',
            'price' => 750000,
            'status' => 'kosong',
            'description' => 'Kamar minimalis untuk satu orang.',
            'photo' => null,
        ]);

        $roomB201 = Room::create([
            'room_number' => 'B-201',
            'type' => 'Premium',
            'price' => 1200000,
            'status' => 'terisi',
            'description' => 'Kamar premium dengan AC dan kamar mandi dalam.',
            'photo' => null,
        ]);

        $roomB202 = Room::create([
            'room_number' => 'B-202',
            'type' => 'Premium',
            'price' => 1200000,
            'status' => 'kosong',
            'description' => 'Kamar premium area lantai dua.',
            'photo' => null,
        ]);

        $roomC301 = Room::create([
            'room_number' => 'C-301',
            'type' => 'Deluxe',
            'price' => 1500000,
            'status' => 'maintenance',
            'description' => 'Kamar deluxe sedang dalam perawatan.',
            'photo' => null,
        ]);

        $admin = User::create([
            'name' => 'Admin KoKu',
            'email' => 'admin@koku.test',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Kantor KoKu',
            'status' => 'aktif',
        ]);

        $tenantOne = User::create([
            'room_id' => $roomA101->id,
            'name' => 'Nadia Putri',
            'email' => 'penyewa@koku.test',
            'password' => Hash::make('Tenant123!'),
            'role' => 'penyewa',
            'phone' => '081234000111',
            'address' => 'Jakarta',
            'status' => 'aktif',
        ]);

        $tenantTwo = User::create([
            'room_id' => $roomB201->id,
            'name' => 'Raka Pratama',
            'email' => 'raka@koku.test',
            'password' => Hash::make('Tenant123!'),
            'role' => 'penyewa',
            'phone' => '081234000222',
            'address' => 'Bandung',
            'status' => 'aktif',
        ]);

        $tenantThree = User::create([
            'name' => 'Dinda Laras',
            'email' => 'dinda@koku.test',
            'password' => Hash::make('Tenant123!'),
            'role' => 'penyewa',
            'phone' => '081234000333',
            'address' => 'Depok',
            'status' => 'aktif',
        ]);

        foreach ([$admin, $tenantOne, $tenantTwo, $tenantThree] as $user) {
            Preference::create([
                'user_id' => $user->id,
                'theme' => 'light',
                'weather_city' => 'Jakarta',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);
        }

        Booking::create([
            'tenant_id' => $tenantOne->id,
            'room_id' => $roomA101->id,
            'status' => 'diterima',
            'notes' => 'Data awal penghuni kamar.',
            'approved_at' => now()->subMonths(2),
        ]);

        Booking::create([
            'tenant_id' => $tenantTwo->id,
            'room_id' => $roomB201->id,
            'status' => 'diterima',
            'notes' => 'Data awal penghuni kamar.',
            'approved_at' => now()->subMonth(),
        ]);

        Payment::create([
            'tenant_id' => $tenantOne->id,
            'room_id' => $tenantOne->room_id,
            'month' => now()->format('Y-m'),
            'amount' => 750000,
            'status' => 'belum_lunas',
            'notes' => 'Tagihan bulan berjalan.',
        ]);

        Payment::create([
            'tenant_id' => $tenantOne->id,
            'room_id' => $tenantOne->room_id,
            'month' => now()->subMonth()->format('Y-m'),
            'amount' => 750000,
            'status' => 'lunas',
            'payment_method' => 'Transfer Bank',
            'submitted_at' => now()->subMonth()->addDays(3),
            'paid_at' => now()->subMonth()->addDays(3),
            'notes' => 'Pembayaran diterima.',
        ]);

        Payment::create([
            'tenant_id' => $tenantTwo->id,
            'room_id' => $tenantTwo->room_id,
            'month' => now()->format('Y-m'),
            'amount' => 1200000,
            'status' => 'menunggu_verifikasi',
            'payment_method' => 'E-Wallet',
            'submitted_at' => now()->subDays(1),
            'notes' => 'Menunggu verifikasi admin.',
        ]);
    }
}

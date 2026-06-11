<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'type',
        'price',
        'status',
        'description',
        'photo',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->hasOne(User::class, 'room_id')->where('role', 'penyewa');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking()
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['menunggu', 'diterima'])
            ->latestOfMany();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

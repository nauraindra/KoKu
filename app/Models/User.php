<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_photo',
        'room_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tenant_id');
    }

    public function activeBooking()
    {
        return $this->hasOne(Booking::class, 'tenant_id')
            ->whereIn('status', ['menunggu', 'diterima'])
            ->latestOfMany();
    }

    public function preference()
    {
        return $this->hasOne(Preference::class);
    }

    public function getTenantStatusLabelAttribute()
    {
        if ($this->room_id) {
            return 'Menempati Kamar';
        }

        if ($this->activeBooking) {
            return 'Sedang Booking';
        }

        return 'Akun Terdaftar';
    }
}

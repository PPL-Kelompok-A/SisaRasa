<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_MITRA = 'mitra';
    const ROLE_CUSTOMER = 'customer';

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'mitra_id');
    }

    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function isMitra()
    {
        return $this->role === self::ROLE_MITRA;
    }

    public function mitraDetail(): HasOne
    {
        return $this->hasOne(MitraDetail::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
    ];

}

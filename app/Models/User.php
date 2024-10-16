<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    // Define role constants
    const ADMIN = 'ADMIN';
    const BUYER = 'BUYER';
    const SELLER = 'SELLER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'role',
        'username',
        'password',
        'digital_signature',
        'default_address_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function defaultAddress()
    {
        return $this->belongsTo(Address::class, 'default_address_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'user_id', 'id');
    }

    // Define the relationship with CouponUsage model
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'user_id');
    }
}

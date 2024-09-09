<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'discount_value',
        'min_order_value',
        'max_discount',
        'start_date',
        'end_date',
        'status',
    ];

    public function guestOrders()
    {
        return $this->hasMany(GuestOrder::class, 'coupon_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id', 'id');
    }
}

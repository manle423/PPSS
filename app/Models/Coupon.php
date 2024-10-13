<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function is_valid($subtotal)
    {
        if (!$this->status) {
            return false; // Coupon is inactive
        }

        if ($this->min_order_value > $subtotal) {
            return false; // Subtotal is less than the required minimum order value
        }


        $now = Carbon::now();
        if ($this->start_date && $now->lt(Carbon::parse($this->start_date))) {
            return false; // Coupon is not yet valid
        }

        if ($this->end_date && $now->gt(Carbon::parse($this->end_date))) {
            return false; // Coupon has expired
        }

        return true; // Coupon is valid
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'guest_order_id',
        'status',
        'order_date',
        'shipping_address',
        'shipping_method_id',
        'payment_method',
        'promotion_id',
        'coupon_id',
        'total_price',
        'discount_value',
        'final_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function guestOrder()
    {
        return $this->belongsTo(GuestOrder::class, 'guest_order_id', 'id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function contracts()
    {
        return $this->hasOne(Contract::class, 'order_id', 'id');
    }
}

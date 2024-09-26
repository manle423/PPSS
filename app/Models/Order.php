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
        'order_code',
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

    protected static function boot()
    {
        // Chuẩn order_code: HD + ngày tháng năm + số thứ tự order ngày hôm đó
        parent::boot();

        static::creating(function ($order) {
            $date = now()->format('dmy');
            $orderCount = self::whereDate('created_at', now()->toDateString())->count();
            $order->order_code = 'HD' . $date . str_pad($orderCount + 1, 4, '0', STR_PAD_LEFT);
        });
    }

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

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'order_promotion', 'order_id', 'promotion_id');
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

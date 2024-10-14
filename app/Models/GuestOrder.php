<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guest_orders';

    protected $fillable = [
        'order_code',
        'guest_name',
        'guest_email',
        'guest_phone_number',
        'guest_address',
        'status',
        'order_date',
        'shipping_method_id',
        'payment_method',
        'promotion_id',
        'coupon_id',
        'total_price',
        'discount_value',
        'final_price',
        'digital_signature',
    ];

    protected static function boot()
    {
        // Chuẩn order_code: GT + ngày tháng năm + số thứ tự order ngày hôm đó
        parent::boot();

        static::creating(function ($order) {
            $date = now()->format('dmy');
            $orderCount = self::whereDate('created_at', now()->toDateString())->count();
            $order->order_code = 'GT' . $date . str_pad($orderCount + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id', 'id');
    }

    public function orders()
    {
        return $this->hasOne(Order::class, 'guest_order_id', 'id');
    }

    public function contracts()
    {
        return $this->hasOne(Contract::class, 'guest_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'guest_order_id', 'id');
    }
}

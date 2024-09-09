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
        'guest_email',
        'guest_phone_number',
        'guest_address',
        'total_amount',
        'status',
        'order_date',
        'shipping_method_id',
        'payment_method',
    ];

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
}

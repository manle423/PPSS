<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shipping_methods';

    protected $fillable = [
        'method_name',
        'cost',
        'estimated_delivery_time',
    ];

    public function guestOrders()
    {
        return $this->hasMany(GuestOrder::class, 'shipping_method_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_method_id', 'id');
    }
}

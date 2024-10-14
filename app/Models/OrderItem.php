<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'guest_order_id',
        'item_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function guestOrder()
    {
        return $this->belongsTo(GuestOrder::class, 'guest_order_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Product::class, 'item_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'order_id',
        'guest_order_id',
        'payment_method',
        'transaction_date',
        'amount',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function guestOrder()
    {
        return $this->belongsTo(GuestOrder::class, 'guest_order_id', 'id');
    }
}

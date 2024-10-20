<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'coupon_id', 'order_id'];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the Coupon model
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}

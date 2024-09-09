<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'discount_type',
        'discount_value',
        'status',
    ];

    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class, 'promotion_id', 'id');
    }
}

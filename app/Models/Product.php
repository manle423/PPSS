<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'image',
        'category_id',
        'description',
        'price',
        'stock_quantity',
        'weight',
        'length',
        'width',
        'height',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class, 'product_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function orders() {
        return $this->hasMany(OrderItem::class, 'item_id', 'id');
    }
}

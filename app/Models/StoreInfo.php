<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInfo extends Model
{
    use HasFactory;
    protected $table = 'store_info';
    protected $fillable = [
        'name', 'description', 'address', 'phone', 'email',
        'footer_why_people_like_us', 'logo', 'team', 'product_category',
        'trusted', 'quality', 'price', 'delivery', 'thanks'
    ];
}

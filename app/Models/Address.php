<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Encryptable;

class Address extends Model
{
    use HasFactory, SoftDeletes, Encryptable;

    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'province_id',
        'district_id',
        'ward_id',
        'is_default',
    ];

    protected $encryptable = [
        'address_line_1',
        'address_line_2',
        'province_id',
        'district_id',
        'ward_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }
}


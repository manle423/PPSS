<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table = 'districts';

    public function province(){
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function wards(){
        return $this->hasMany(Ward::class,'district_id','id');
    }
}

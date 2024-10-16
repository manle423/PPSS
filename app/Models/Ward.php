<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;
    
    protected $table = 'wards';

    protected $fillable = ['name', 'codename', 'district_id'];

    public function district(){
        return $this->belongsTo(District::class,'district_id','id');
    }
}

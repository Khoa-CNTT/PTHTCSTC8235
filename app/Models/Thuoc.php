<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thuoc extends Model
{
    protected $table = 'thuocs';
    protected $fillable = [
        'ten_thuoc',
        'don_vi',
        'mo_ta',
        'tinh_trang'
    ];
}

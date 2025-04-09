<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thuoc extends Model
{
    protected $table = 'thuocs';
    protected $fillable = [
        'ten_thuoc',
        'don_vi',
        'don_gia',
        'mo_ta',
        'han_su_dung',
        'tinh_trang',
        'id_kho',
    ];
}

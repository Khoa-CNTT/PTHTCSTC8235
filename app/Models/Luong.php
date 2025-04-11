<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Luong extends Model
{
    protected $table = 'luongs';
    protected $fillable = [
        'id_luong',
        'id_nv',
        'tien_luong',
        'ngay_thanh_toan',
        'tinh_trang',
        'tien_thuong',
    ];
}

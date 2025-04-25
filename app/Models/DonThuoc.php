<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonThuoc extends Model
{
    protected $table = 'don_thuocs';
    protected $fillable = [
            'id_hsba',
            'ten_nv',
            'ngay_kham',
            'chuan_doan',
            'id_thuoc',
            'so_luong',
            'lieu_luong',
    ];
}

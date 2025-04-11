<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuNhap extends Model
{
    protected $table = 'phieu_nhaps';
    protected $fillable = [
        'id_kho',
        'id_ncc',
        'ngay_nhap',
        'tinh_trang',
    ];
}

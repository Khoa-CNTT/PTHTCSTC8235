<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuNhapChiTiet extends Model
{
    protected $table = 'phieu_nhap_chi_tiets';
    protected $fillable = [
        'id_phieu_nhap',
        'id_thuoc',
        'so_luong',
        'gia_nhap',
        'han_su_dung'
    ];
}

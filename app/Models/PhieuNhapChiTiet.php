<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuNhapChiTiet extends Model
{
    protected $table = 'phieu_nhap_chi_tiets';
    protected $fillable = [
        'id_phieunhap',
        'id_thuoc',
        'so_luong_nhap',
        'gia_nhap'
    ];
}

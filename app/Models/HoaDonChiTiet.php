<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDonChiTiet extends Model
{
    protected $table = 'hoa_don_chi_tiets';
    protected $fillable = [
        'id_kh',
        'id_pet',
        'phuong_thuc',
        'ngay_xuat_hoa_don',
        'tinh_trang'
    ];
}

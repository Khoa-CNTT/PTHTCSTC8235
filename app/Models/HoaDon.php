<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'hoa_dons';
    protected $fillable = [
        'id_kh',
        'id_nv',
        'id_pet',
        'id_lich_pet',
        'phuong_thuc',
        'ngay_xuat_hoa_don',
        'tinh_trang'
    ];
}

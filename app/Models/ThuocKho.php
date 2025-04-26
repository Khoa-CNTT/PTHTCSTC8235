<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuocKho extends Model
{
    protected $table = 'thuoc_khos';
    protected $fillable = [
        'id_kho',
        'id_thuoc',
        'id_phieu_nhap_CT',
        'gia_nhap',
        'so_luong_ton_kho',
        'han_su_dung',
        'gia_ban',
        'tinh_trang'
    ];
}

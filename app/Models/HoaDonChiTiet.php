<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDonChiTiet extends Model
{
    protected $table = 'hoa_don_chi_tiets';
    protected $fillable = [
        'id_hoadon',
        'id_ct_don_thuoc',
        'id_nv',
        'id_dvct',
        'ngay_tt',
        'tien_kham'
    ];
}

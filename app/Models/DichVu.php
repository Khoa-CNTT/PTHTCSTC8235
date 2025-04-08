<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    protected $table = 'dich_vus';
    protected $fillable = [
        "ten_dv",
        'id_loaidv',
        'mo_ta',
        'gia',
        'hinh_anh',
        'can_nang_min',
        'can_nang_max',
        'phan_loai_kg',
        'tinh_trang',

    ];
}

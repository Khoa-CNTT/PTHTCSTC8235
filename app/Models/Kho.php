<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kho extends Model
{
    protected $table = 'bac_sis';
    protected $fillable = [
        'ten_bs',
        'gioi_tinh',
        'email',
        'password',
        'mo_ta',
        'hinh_anh',
        'tinh_trang',
        'id_chucvu',
        'chuyen_khoa',
        'tien_kham',
    ];
}

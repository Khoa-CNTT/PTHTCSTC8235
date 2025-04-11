<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'nhan_viens';
    protected $fillable = [
        'ten_nv',
        'gioi_tinh',
        'email',
        'tien_kham',
        'password',
        'mo_ta',
        'hinh_anh',
        'tinh_trang',
        'id_chucvu',
    ];
}

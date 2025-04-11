<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danh_gias';
    protected $fillable = [
        'id_kh',
        'noi_dung',
        'ngay_tao',
        'tinh_trang',
    ];
}

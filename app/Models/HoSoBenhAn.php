<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoSoBenhAn extends Model
{
    protected $table = 'ho_so_benh_ans';
    protected $fillable = [
        "id_nv",
        "ngay_kham",
        "chuan_doan",
        "id_pet",
        "tinh_trang",
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHenPet extends Model
{
    protected $table = 'lich_hen_pets';
    protected $fillable = [
        "id_lich",
        "id_kh",
        "id_dv",
        "id_pet",
        "ngay",
        "gio",
        "id_nv",
        "tinh_trang",
        "tien_coc",
    ];
}

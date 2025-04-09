<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiDichVu extends Model
{
    protected $table = 'loai_dich_vus';
    protected $fillable = [
        "ten_loaidv",
        'mo_ta'
    ];
}

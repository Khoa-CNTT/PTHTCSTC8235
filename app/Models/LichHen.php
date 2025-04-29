<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHen extends Model
{
    protected $table = 'lich_hens';
    protected $fillable = [
        "tinh_trang",
        "khung_gio"
    ];
}

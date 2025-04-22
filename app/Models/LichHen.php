<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHen extends Model
{
    protected $table = 'lich_hen';
    protected $fillable = [
        "tinh_trang",
        "khung_gio"
    ];
}

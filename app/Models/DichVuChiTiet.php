<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DichVuChiTiet extends Model
{
    protected $table = 'dich_vu_chi_tiets';
    protected $fillable = [
        "id_dv",
        "id_lich",
        "gia",
    ];
}

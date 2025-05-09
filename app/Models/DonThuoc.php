<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonThuoc extends Model
{
    protected $table = 'don_thuocs';
    protected $fillable = [
        'ngay_ke_don',
    ];
}

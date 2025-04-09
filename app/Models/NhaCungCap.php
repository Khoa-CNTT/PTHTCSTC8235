<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'nha_cung_caps';
    protected $fillable = [
            'ten_ncc',
            'email',
            'sdt',
            'dia_chi',
            'tinh_trang',
    ];
}

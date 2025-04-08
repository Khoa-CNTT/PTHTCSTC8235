<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pet extends Model
{
    protected $table='pets';
    protected $fillable=[
        'id_kh',
        'ten_pet',
        'chung_loai',
        'gioi_tinh',
        'tuoi',
        'hinh_anh',
        'can_nang',
        'tinh_trang',
    ];
}

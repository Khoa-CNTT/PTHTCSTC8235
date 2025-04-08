<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Luong extends Model
{
    protected $table = 'luongs';
    protected $fillable = [
           'ten_kho',
            'dia_chi',
            'trang_thai',
            'so_luong_ton_khokho',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Thuoc extends Model
{
    use HasFactory;

    protected $table = 'thuocs';
    protected $fillable = [
        'ten_thuoc',
        'don_vi',
        'mo_ta',
        'gia',
        'so_luong_ton'
    ];

    public function chiTietThuocs()
    {
        return $this->hasMany(ChiTietThuoc::class, 'id_thuoc');
    }
}

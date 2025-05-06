<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietThuoc extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_thuocs';
    protected $fillable = [
        'id_ho_so_benh_an',
        'id_thuoc',
        'so_luong',
        'lieu_luong',
        'ghi_chu'
    ];

    public function thuoc()
    {
        return $this->belongsTo(Thuoc::class, 'id_thuoc');
    }

    public function hoSoBenhAn()
    {
        return $this->belongsTo(HoSoBenhAn::class, 'id_ho_so_benh_an');
    }
} 
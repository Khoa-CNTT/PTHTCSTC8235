<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoBenhAn extends Model
{
    use HasFactory;

    protected $table = 'ho_so_benh_ans';

    protected $fillable = [
        'id_nv',
        'id_lich_hen_pet',
        'id_don_thuoc',
        'chuan_doan',
        'tinh_trang'
    ];

    public function chiTietThuocs()
    {
        return $this->hasMany(ChiTietThuoc::class, 'id_ho_so_benh_an');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonThuocChiTiet extends Model
{
    use HasFactory;

    protected $table = 'don_thuoc_chi_tiets';
    protected $primaryKey = 'id_ctthuoc';

    protected $fillable = [
        'id_thuoc',
        'id_don_thuoc',
        'so_luong',
        'lieu_luong',
        'tinh_trang',
    ];

    public function donThuoc()
    {
        return $this->belongsTo(DonThuoc::class, 'id_don_thuoc', 'id');
    }

    public function thuoc()
    {
        return $this->belongsTo(Thuoc::class, 'id_thuoc', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuNhap extends Model
{
    protected $table = 'phieu_nhaps';
    protected $fillable = [
        'id_kho',
        'id_ncc',
        'ngay_nhap',
        'tinh_trang',
    ];
    public function kho()
    {
        return $this->belongsTo(Kho::class, 'id_kho');
    }

    public function ncc()
    {
        return $this->belongsTo(NhaCungCap::class, 'id_ncc');
    }

    public function chiTiet()
    {
        return $this->hasMany(PhieuNhapChiTiet::class, 'id_phieu_nhap');
    }
}

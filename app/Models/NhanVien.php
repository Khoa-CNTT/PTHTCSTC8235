<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class NhanVien extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $table = 'nhan_viens';
    protected $fillable = [
        'ten_nv',
        'gioi_tinh',
        'email',
        'tien_kham',
        'password',
        'mo_ta',
        'hinh_anh',
        'tinh_trang',
        'id_chucvu',
    ];
    public function chuc_vu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chucvu', 'id');
    }
}

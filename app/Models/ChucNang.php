<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    use HasFactory;

    protected $table = 'chuc_nangs';

    protected $fillable = [
        'ten_chuc_nang',
        'tinh_trang',
    ];

    public function phan_quyens()
    {
        return $this->hasMany(PhanQuyen::class, 'id_chuc_nang', 'id');
    }

    public function chuc_vus()
    {
        return $this->belongsToMany(ChucVu::class, 'phan_quyens', 'id_chuc_nang', 'id_chuc_vu');
    }
}

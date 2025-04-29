<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;

    protected $table = 'chuc_vus';

    protected $fillable = [
        'ten_chuc_vu',
        'tinh_trang',
    ];

    public function phan_quyens()
    {
        return $this->hasMany(PhanQuyen::class, 'id_chuc_vu', 'id');
    }

    public function chuc_nangs()
    {
        return $this->belongsToMany(ChucNang::class, 'phan_quyens', 'id_chuc_vu', 'id_chuc_nang');
    }
}

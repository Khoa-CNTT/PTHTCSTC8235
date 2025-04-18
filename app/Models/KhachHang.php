<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
class KhachHang extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;
    protected $table = 'khach_hangs';
    protected $fillable = [
        'ho_va_ten',
        'email',
        'password',
        'so_dien_thoai',
        'ngay_sinh',
        'is_active',
        'is_block',
        'hash_active',
        'hash_reset',
    ];
}

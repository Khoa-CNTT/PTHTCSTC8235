<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kho extends Model
{
    protected $table = 'khos';
    protected $fillable = [
        'ten_kho',
        'dia_chi',
        'tinh_trang'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichHenPet extends Model
{
    protected $table = 'lich_hen_pets';
    protected $fillable = [
        "id_lich",
        "id_pet",
        "ngay_gio_hen",
        "id_nv",
        "ten_loaidv",
        'mo_ta'
    ];
}

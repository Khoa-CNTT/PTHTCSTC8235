<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HoSoBenhAn;

class HoSoBenhAnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HoSoBenhAn::insert([
            [
                'id_nv' => 1,
                'ngay_kham' => now(),
                'chuan_doan' => 'Viêm da dị ứng',
                'id_pet' => 1,
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_nv' => 1,
                'ngay_kham' => now()->subDays(2),
                'chuan_doan' => 'Cảm cúm',
                'id_pet' => 1,
                'tinh_trang' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

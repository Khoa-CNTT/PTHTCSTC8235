<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LichHenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lich_hen_pets')->delete();

        DB::table('lich_hen_pets')->insert([
            [
                'id_lich' => 1,
                'id_kh' => 1,
                'id_dv' => 1,
                'id_pet' => 1,
                'ngay' => '2025-05-08',
                'gio' => '09:00 - 10:00',
                'id_nv' => 1,
                'tinh_trang' => '0',
                'tien_coc' => 75000,
            ],
            [
                'id_lich' => 2,
                'id_kh' => 2,
                'id_dv' => 2,
                'id_pet' => 2,
                'ngay' => '2025-05-09',
                'gio' => '10:00 - 11:00',
                'id_nv' => 2,
                'tinh_trang' => '1',
                'tien_coc' => 100000,
            ],
            [
                'id_lich' => 3,
                'id_kh' => 3,
                'id_dv' => 1,
                'id_pet' => 3,
                'ngay' => '2025-05-10',
                'gio' => '13:00 - 14:00',
                'id_nv' => 1,
                'tinh_trang' => '0',
                'tien_coc' => 80000,
            ],
            [
                'id_lich' => 4,
                'id_kh' => 1,
                'id_dv' => 3,
                'id_pet' => 1,
                'ngay' => '2025-05-11',
                'gio' => '15:00 - 16:00',
                'id_nv' => 3,
                'tinh_trang' => '1',
                'tien_coc' => 50000,
            ],
            [
                'id_lich' => 5,
                'id_kh' => 4,
                'id_dv' => 4,
                'id_pet' => 4,
                'ngay' => '2025-05-12',
                'gio' => '08:00 - 09:00',
                'id_nv' => 2,
                'tinh_trang' => '0',
                'tien_coc' => 120000,
            ],
            [
                'id_lich' => 6,
                'id_kh' => 2,
                'id_dv' => 2,
                'id_pet' => 2,
                'ngay' => '2025-05-13',
                'gio' => '14:00 - 15:00',
                'id_nv' => 1,
                'tinh_trang' => '1',
                'tien_coc' => 95000,
            ],
        ]);}

    }


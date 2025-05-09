<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'ngay' => Carbon::now()->toDateString(),
                'gio' => '09:00',
                'id_nv' => 1,
                'tinh_trang' => '0',
                'tien_coc' => 100000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_lich' => 2,
                'id_kh' => 2,
                'id_dv' => 2,
                'id_pet' => 2,
                'ngay' => Carbon::now()->addDays(1)->toDateString(),
                'gio' => '14:00',
                'id_nv' => 2,
                'tinh_trang' => '0',
                'tien_coc' => 150000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_lich' => 3,
                'id_kh' => 3,
                'id_dv' => 1,
                'id_pet' => 3,
                'ngay' => Carbon::now()->addDays(2)->toDateString(),
                'gio' => '16:30',
                'id_nv' => 3,
                'tinh_trang' => '0',
                'tien_coc' => 200000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_lich' => 4,
                'id_kh' => 4,
                'id_dv' => 2,
                'id_pet' => 4,
                'ngay' => Carbon::now()->addDays(3)->toDateString(),
                'gio' => '10:30',
                'id_nv' => 4,
                'tinh_trang' => '0',
                'tien_coc' => 50000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_lich' => 5,
                'id_kh' => 5,
                'id_dv' => 3,
                'id_pet' => 5,
                'ngay' => Carbon::now()->addDays(4)->toDateString(),
                'gio' => '11:00',
                'id_nv' => 5,
                'tinh_trang' => '0',
                'tien_coc' => 120000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}

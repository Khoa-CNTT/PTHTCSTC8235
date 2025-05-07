<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoSoBenhAnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ho_so_benh_ans')->delete();
        DB::table('ho_so_benh_ans')->insert([
            [
                'id_pet' => 1,
                'id_nv' => 1,
                'ngay_kham' => now(),
                'chuan_doan' => 'Viêm da dị ứng',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_pet' => 2,
                'id_nv' => 2,
                'ngay_kham' => now(),
                'chuan_doan' => 'Sốt virus',
                'tinh_trang' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

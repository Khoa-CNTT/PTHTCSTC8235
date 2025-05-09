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
                'id_lich_hen_pet' => 28,
                'id_nv' => 1,
                'id_don_thuoc' => null,
                'chuan_doan' => 'Viêm da dị ứng',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_lich_hen_pet' => 29,
                'id_nv' => 2,
                'id_don_thuoc' => null,
                'chuan_doan' => 'Sốt virus',
                'tinh_trang' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

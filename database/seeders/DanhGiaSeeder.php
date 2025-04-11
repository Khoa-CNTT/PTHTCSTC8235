<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_gias')->delete();
        DB::table('danh_gias')->insert([
            [
                'id_kh' => 'Thu Thảo',
                'noi_dung' => 'tốt lắm',
                'ngay_tao' => '2022-01-01',
                'tinh_trang' => '1',
            ],
        ]);
    }
}

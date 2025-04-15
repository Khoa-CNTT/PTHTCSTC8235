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
                'id_kh' => 1,
                'noi_dung' => 'Dịch vụ tuyệt vời!',
                'ngay_tao' => '2022-01-01',
                'tinh_trang' => 1,
            ],
            [
                'id_kh' => 2,
                'noi_dung' => 'Nhân viên thân thiện.',
                'ngay_tao' => '2022-01-02',
                'tinh_trang' => 1,
            ],
            [
                'id_kh' => 3,
                'noi_dung' => 'Giá cả hợp lý.',
                'ngay_tao' => '2022-01-03',
                'tinh_trang' => 0,
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('luongs')->insert([
            [
                'id_nv' => 'NV001',
                'tien_luong' => 7000000,
                'ngay_thanh_toan' => Carbon::now()->subDays(5),
                'tinh_trang' => 1, // đã thanh toán
                'tien_thuong' => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_nv' => 'NV002',
                'tien_luong' => 6000000,
                'ngay_thanh_toan' => Carbon::now()->subDays(3),
                'tinh_trang' => 0, // chưa thanh toán
                'tien_thuong' => 300000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_nv' => 'NV003',
                'tien_luong' => 8000000,
                'ngay_thanh_toan' => Carbon::now()->subDays(1),
                'tinh_trang' => 1,
                'tien_thuong' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

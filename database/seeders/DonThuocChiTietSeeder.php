<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonThuocChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('don_thuoc_chi_tiets')->insert([
            [
                'id_thuoc' => 1,
                'id_don_thuoc' => 1,
                'so_luong' => 10,
                'lieu_luong' => '2 viên/ngày',
                'tinh_trang' => 'Đang sử dụng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_thuoc' => 2,
                'id_don_thuoc' => 1,
                'so_luong' => 5,
                'lieu_luong' => '1 viên/ngày',
                'tinh_trang' => 'Đã hoàn thành',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_thuoc' => 1,
                'id_don_thuoc' => 2,
                'so_luong' => 7,
                'lieu_luong' => '1 viên/2 ngày',
                'tinh_trang' => 'Đang sử dụng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 
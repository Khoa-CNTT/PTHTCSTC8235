<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonThuocChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('don_thuoc_chi_tiets')->delete();

        // Thêm chi tiết đơn thuốc trực tiếp
        DB::table('don_thuoc_chi_tiets')->insert([
            // Chi tiết cho đơn thuốc ID 1
            [
                'id' => 1,
                'id_don_thuoc' => 1,
                'id_thuoc' => 1,
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'id_don_thuoc' => 1,
                'id_thuoc' => 2,
                'so_luong' => 20,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            // Chi tiết cho đơn thuốc ID 2
            [
                'id' => 3,
                'id_don_thuoc' => 2,
                'id_thuoc' => 3,
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 4,
                'id_don_thuoc' => 2,
                'id_thuoc' => 4,
                'so_luong' => 17,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            // Chi tiết cho đơn thuốc ID 3
            [
                'id' => 5,
                'id_don_thuoc' => 3,
                'id_thuoc' => 5,
                'so_luong' => 25,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 6,
                'id_don_thuoc' => 3,
                'id_thuoc' => 6,
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            // Chi tiết cho đơn thuốc ID 4
            [
                'id' => 7,
                'id_don_thuoc' => 4,
                'id_thuoc' => 7,
                'so_luong' => 16,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 8,
                'id_don_thuoc' => 4,
                'id_thuoc' => 8,
                'so_luong' => 14,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            // Chi tiết cho đơn thuốc ID 5
            [
                'id' => 9,
                'id_don_thuoc' => 5,
                'id_thuoc' => 9,
                'so_luong' => 13,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 10,
                'id_don_thuoc' => 5,
                'id_thuoc' => 10,
                'so_luong' => 16,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 11,
                'id_don_thuoc' => 6,
                'id_thuoc' => 1,
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 12,
                'id_don_thuoc' => 7,
                'id_thuoc' => 2,
                'so_luong' => 12,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'id' => 13,
                'id_don_thuoc' => 8,
                'id_thuoc' => 3,
                'so_luong' => 17,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 14,
                'id_don_thuoc' => 9,
                'id_thuoc' => 4,
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 15,
                'id_don_thuoc' => 10,
                'id_thuoc' => 5,
                'so_luong' => 11,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(24),
                'updated_at' => Carbon::now()->subDays(24),
            ],
        ]);
    }
}

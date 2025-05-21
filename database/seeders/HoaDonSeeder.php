<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('hoa_dons')->delete();

        // Thêm dữ liệu hóa đơn trực tiếp
        DB::table('hoa_dons')->insert([
            [
                'id' => 1,
                'id_kh' => 1,
                'id_nv' => 5,
                'phuong_thuc' => 1, // Thanh toán tiền mặt
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(5),
                'tinh_trang' => 0, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'id_kh' => 2,
                'id_nv' => 6,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(7),
                'tinh_trang' => 0, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 3,
                'id_kh' => 4,
                'id_nv' => 7,
                'phuong_thuc' => 1, // Thanh toán tiền mặt
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(10),
                'tinh_trang' => 0, // Chưa thanh toán
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 4,
                'id_kh' => 5,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(12),
                'tinh_trang' => 0, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 5,
                'id_kh' => 6,
                'id_nv' => 6,
                'phuong_thuc' => 1, // Thanh toán tiền mặt
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(14),
                'tinh_trang' => 0, // Chưa thanh toán
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 6,
                'id_kh' => 7,
                'id_nv' => 7,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(16),
                'tinh_trang' => 1, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 7,
                'id_kh' => 8,
                'id_nv' => 5,
                'phuong_thuc' => 1, // Thanh toán tiền mặt
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(18),
                'tinh_trang' => 0, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'id' => 8,
                'id_kh' => 9,
                'id_nv' => 6,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(20),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 9,
                'id_kh' => 10,
                'id_nv' => 7,
                'phuong_thuc' => 1, // Thanh toán tiền mặt
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(22),
                'tinh_trang' => 1, // Đã thanh toán
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 10,
                'id_kh' => 11,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::now()->subDays(24),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::now()->subDays(24),
                'updated_at' => Carbon::now()->subDays(24),
            ],
            [
                'id' => 11,
                'id_kh' => 1,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::create(2025, 2, 5),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::create(2025, 2, 5),
                'updated_at' => Carbon::create(2025, 2, 5),
            ],
            [
                'id' => 12,
                'id_kh' => 4,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::create(2025, 3, 6),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::create(2025, 3, 6),
                'updated_at' => Carbon::create(2025, 3, 6),
            ],
            [
                'id' => 13,
                'id_kh' => 5,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::create(2025, 3, 5),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::create(2025, 3, 5),
                'updated_at' => Carbon::create(2025, 3, 5),
            ],
            [
                'id' => 14,
                'id_kh' => 5,
                'id_nv' => 5,
                'phuong_thuc' => 0, // Thanh toán qua thẻ
                'ngay_xuat_hoa_don' => Carbon::create(2025, 2, 7),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::create(2025, 2, 7),
                'updated_at' => Carbon::create(2025, 2, 7),
            ],
            [
                'id' => 15,
                'id_kh' => 5,
                'id_nv' => 7,
                'phuong_thuc' => 1,
                'ngay_xuat_hoa_don' => Carbon::create(2024, 11, 7),
                'tinh_trang' => 1, // Chưa thanh toán
                'created_at' => Carbon::create(2024, 2, 7),
                'updated_at' => Carbon::create(2024, 2, 7),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhanQuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('phan_quyens')->delete();
        DB::table('phan_quyens')->insert([
            [
                'id_chuc_vu' => 3, // ADMIN
                'id_chuc_nang' => 1, // Quản Lý Phiếu Nhập Thuốc
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 2, // Quản Lý Tồn Kho
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 3, // Quản Lý Lịch Hẹn
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 4, // Quản Lý Dịch Vụ
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 5, // Quản Lý Nhân Viên
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 6, // Quản Lý Khách Hàng
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 7, // Quản Lý Pet
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 8, // Quản Lý Thuốc
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 9, // Quản Lý Nhà Cung Cấp
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 10, // Quản Lý Lương
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 11, // Quản Lý Đánh Giá
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 12, // Quản Lý Kho
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 13, // Quản Lý Hóa Đơn
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 14, // Quản Lý Doanh Thu
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 15, // Quản Lý Chức Vụ
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 16, // Phân Quyền
            ],
            [
                'id_chuc_vu' => 3,
                'id_chuc_nang' => 17, // Kê đơn thuốc
            ],
        ]);
    }
} 
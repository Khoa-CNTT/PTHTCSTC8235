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
            // Chi tiết cho đơn thuốc ID 1 - Viêm phổi cấp tính do vi khuẩn
            [
                'id' => 1,
                'id_don_thuoc' => 1,
                'id_thuoc' => 1, // Amoxicillin
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'id_don_thuoc' => 1,
                'id_thuoc' => 2, // Paracetamol
                'so_luong' => 20,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            
            // Chi tiết cho đơn thuốc ID 2 - Tiêu chảy cấp do vi khuẩn E.coli
            [
                'id' => 3,
                'id_don_thuoc' => 2,
                'id_thuoc' => 3, // Ciprofloxacin
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 4,
                'id_don_thuoc' => 2,
                'id_thuoc' => 4, // Loperamide
                'so_luong' => 17,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            
            // Chi tiết cho đơn thuốc ID 3 - Viêm da dị ứng do thức ăn
            [
                'id' => 5,
                'id_don_thuoc' => 3,
                'id_thuoc' => 5, // Prednisolone
                'so_luong' => 25,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 6,
                'id_don_thuoc' => 3,
                'id_thuoc' => 6, // Cetirizine
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            
            // Chi tiết cho đơn thuốc ID 4 - Cảm cúm thông thường do virus
            [
                'id' => 7,
                'id_don_thuoc' => 4,
                'id_thuoc' => 7, // Ibuprofen
                'so_luong' => 16,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 8,
                'id_don_thuoc' => 4,
                'id_thuoc' => 8, // Vitamin C
                'so_luong' => 14,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            
            // Chi tiết cho đơn thuốc ID 5 - Viêm tai giữa cấp tính
            [
                'id' => 9,
                'id_don_thuoc' => 5,
                'id_thuoc' => 9, // Moxifloxacin
                'so_luong' => 13,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'id' => 10,
                'id_don_thuoc' => 5,
                'id_thuoc' => 10, // Acetaminophen
                'so_luong' => 16,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            
            // Chi tiết cho đơn thuốc ID 6 - Viêm khớp mãn tính do tuổi tác
            [
                'id' => 11,
                'id_don_thuoc' => 6,
                'id_thuoc' => 1, // Amoxicillin
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 12,
                'id_don_thuoc' => 6,
                'id_thuoc' => 7, // Ibuprofen
                'so_luong' => 20,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            
            // Chi tiết cho đơn thuốc ID 7 - Suy dinh dưỡng do thiếu protein
            [
                'id' => 13,
                'id_don_thuoc' => 7,
                'id_thuoc' => 2, // Paracetamol
                'so_luong' => 12,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 14,
                'id_don_thuoc' => 7,
                'id_thuoc' => 8, // Vitamin C
                'so_luong' => 30,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // Chi tiết cho đơn thuốc ID 8 - Nhiễm trùng đường ruột do ký sinh trùng
            [
                'id' => 15,
                'id_don_thuoc' => 8,
                'id_thuoc' => 3, // Ciprofloxacin
                'so_luong' => 17,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 2 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 16,
                'id_don_thuoc' => 8,
                'id_thuoc' => 6, // Cetirizine
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            
            // Chi tiết cho đơn thuốc ID 9 - Viêm phế quản mãn tính do khói bụi
            [
                'id' => 17,
                'id_don_thuoc' => 9,
                'id_thuoc' => 4, // Loperamide
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 18,
                'id_don_thuoc' => 9,
                'id_thuoc' => 5, // Prednisolone
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            
            // Chi tiết cho đơn thuốc ID 10 - Viêm mắt đỏ do vi khuẩn
            [
                'id' => 19,
                'id_don_thuoc' => 10,
                'id_thuoc' => 5, // Prednisolone
                'so_luong' => 11,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'id' => 20,
                'id_don_thuoc' => 10,
                'id_thuoc' => 9, // Moxifloxacin
                'so_luong' => 7,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            
            // Chi tiết cho đơn thuốc ID 11 - Viêm phổi mãn tính do hít phải chất độc
            [
                'id' => 21,
                'id_don_thuoc' => 11,
                'id_thuoc' => 1, // Amoxicillin
                'so_luong' => 14,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'id' => 22,
                'id_don_thuoc' => 11,
                'id_thuoc' => 10, // Acetaminophen
                'so_luong' => 12,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            
            // Chi tiết cho đơn thuốc ID 12 - Tiêu chảy mãn tính do rối loạn tiêu hóa
            [
                'id' => 23,
                'id_don_thuoc' => 12,
                'id_thuoc' => 2, // Paracetamol
                'so_luong' => 8,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(36),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 24,
                'id_don_thuoc' => 12,
                'id_thuoc' => 4, // Loperamide
                'so_luong' => 16,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(36),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            
            // Chi tiết cho đơn thuốc ID 13 - Viêm da mãn tính do nấm
            [
                'id' => 25,
                'id_don_thuoc' => 13,
                'id_thuoc' => 3, // Ciprofloxacin
                'so_luong' => 13,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(42),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => 26,
                'id_don_thuoc' => 13,
                'id_thuoc' => 6, // Cetirizine
                'so_luong' => 20,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(42),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            
            // Chi tiết cho đơn thuốc ID 14 - Cảm cúm mãn tính do suy giảm miễn dịch
            [
                'id' => 27,
                'id_don_thuoc' => 14,
                'id_thuoc' => 7, // Ibuprofen
                'so_luong' => 18,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(37),
                'updated_at' => Carbon::now()->subDays(13),
            ],
            [
                'id' => 28,
                'id_don_thuoc' => 14,
                'id_thuoc' => 8, // Vitamin C
                'so_luong' => 30,
                'lieu_luong' => 'Ngày uống 3 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(37),
                'updated_at' => Carbon::now()->subDays(13),
            ],
            
            // Chi tiết cho đơn thuốc ID 15 - Viêm tai mãn tính do bẩn
            [
                'id' => 29,
                'id_don_thuoc' => 15,
                'id_thuoc' => 9, // Moxifloxacin
                'so_luong' => 15,
                'lieu_luong' => 'Ngày uống 1 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(44),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'id' => 30,
                'id_don_thuoc' => 15,
                'id_thuoc' => 10, // Acetaminophen
                'so_luong' => 10,
                'lieu_luong' => 'Ngày uống 2 lần, mỗi lần 1 viên',
                'tinh_trang' => 1,
                'created_at' => Carbon::now()->subDays(44),
                'updated_at' => Carbon::now()->subDays(18),
            ],
        ]);
    }
}

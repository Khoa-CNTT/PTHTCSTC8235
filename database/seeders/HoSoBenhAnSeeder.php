<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HoSoBenhAnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('ho_so_benh_ans')->delete();

        // Thêm dữ liệu hồ sơ bệnh án với các pet không trùng nhau
        // tinh_trang: 0 = đã khỏi, 1 = chưa khỏi (chỉ giữ lại 3 cái chưa khỏi)
        DB::table('ho_so_benh_ans')->insert([
            [
                'id' => 1,
                'id_nv' => 1,
                'id_lich_hen_pet' => 1,
                'id_don_thuoc' => 1,
                'chuan_doan' => 'Viêm phổi cấp tính do vi khuẩn',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'id_nv' => 2,
                'id_lich_hen_pet' => 2,
                'id_don_thuoc' => 2,
                'chuan_doan' => 'Tiêu chảy cấp do vi khuẩn E.coli',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 3,
                'id_nv' => 3,
                'id_lich_hen_pet' => 3,
                'id_don_thuoc' => 3,
                'chuan_doan' => 'Viêm da dị ứng do thức ăn',
                'tinh_trang' => 1, // Chưa khỏi (1)
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 4,
                'id_nv' => 4,
                'id_lich_hen_pet' => 4,
                'id_don_thuoc' => 4,
                'chuan_doan' => 'Cảm cúm thông thường do virus',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 5,
                'id_nv' => 5,
                'id_lich_hen_pet' => 5,
                'id_don_thuoc' => 5,
                'chuan_doan' => 'Viêm tai giữa cấp tính',
                'tinh_trang' => 1, // Chưa khỏi (2)
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'id' => 6,
                'id_nv' => 1,
                'id_lich_hen_pet' => 6,
                'id_don_thuoc' => 6,
                'chuan_doan' => 'Viêm khớp mãn tính do tuổi tác',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 7,
                'id_nv' => 2,
                'id_lich_hen_pet' => 7,
                'id_don_thuoc' => 7,
                'chuan_doan' => 'Suy dinh dưỡng do thiếu protein',
                'tinh_trang' => 1, // Chưa khỏi (3)
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 8,
                'id_nv' => 3,
                'id_lich_hen_pet' => 8,
                'id_don_thuoc' => 8,
                'chuan_doan' => 'Nhiễm trùng đường ruột do ký sinh trùng',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 9,
                'id_nv' => 4,
                'id_lich_hen_pet' => 9,
                'id_don_thuoc' => 9,
                'chuan_doan' => 'Viêm phế quản mãn tính do khói bụi',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 10,
                'id_nv' => 5,
                'id_lich_hen_pet' => 10,
                'id_don_thuoc' => 10,
                'chuan_doan' => 'Viêm mắt đỏ do vi khuẩn',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'id' => 11,
                'id_nv' => 1,
                'id_lich_hen_pet' => 11,
                'id_don_thuoc' => 1,
                'chuan_doan' => 'Viêm phổi mãn tính do hít phải chất độc',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'id' => 12,
                'id_nv' => 2,
                'id_lich_hen_pet' => 12,
                'id_don_thuoc' => 2,
                'chuan_doan' => 'Tiêu chảy mãn tính do rối loạn tiêu hóa',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(36),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 13,
                'id_nv' => 3,
                'id_lich_hen_pet' => 13,
                'id_don_thuoc' => 3,
                'chuan_doan' => 'Viêm da mãn tính do nấm',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(42),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => 14,
                'id_nv' => 4,
                'id_lich_hen_pet' => 14,
                'id_don_thuoc' => 4,
                'chuan_doan' => 'Cảm cúm mãn tính do suy giảm miễn dịch',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(37),
                'updated_at' => Carbon::now()->subDays(13),
            ],
            [
                'id' => 15,
                'id_nv' => 5,
                'id_lich_hen_pet' => 15,
                'id_don_thuoc' => 5,
                'chuan_doan' => 'Viêm tai mãn tính do bẩn',
                'tinh_trang' => 0, // Đã khỏi
                'created_at' => Carbon::now()->subDays(44),
                'updated_at' => Carbon::now()->subDays(18),
            ],
        ]);
    }
}

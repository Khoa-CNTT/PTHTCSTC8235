<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HoaDonChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ
        DB::table('hoa_don_chi_tiets')->delete();

        // Thêm chi tiết hóa đơn trực tiếp
        DB::table('hoa_don_chi_tiets')->insert([
            [
                'id' => 1,
                'id_hoadon' => 1,
                'id_ct_don_thuoc' => 1,
                'id_lich_hen_pet' => 1,
                'tien_kham' => 150000,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'id_hoadon' => 2,
                'id_ct_don_thuoc' => 2,
                'id_lich_hen_pet' => 2,
                'tien_kham' => 200000,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 3,
                'id_hoadon' => 3,
                'id_ct_don_thuoc' => 3,
                'id_lich_hen_pet' => 3,
                'tien_kham' => 180000,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 4,
                'id_hoadon' => 4,
                'id_ct_don_thuoc' => 4,
                'id_lich_hen_pet' => 4,
                'tien_kham' => 250000,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 5,
                'id_hoadon' => 5,
                'id_ct_don_thuoc' => 5,
                'id_lich_hen_pet' => 5,
                'tien_kham' => 300000,
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 6,
                'id_hoadon' => 6,
                'id_ct_don_thuoc' => 6,
                'id_lich_hen_pet' => 6,
                'tien_kham' => 220000,
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 7,
                'id_hoadon' => 7,
                'id_ct_don_thuoc' => 7,
                'id_lich_hen_pet' => 7,
                'tien_kham' => 170000,
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'id' => 8,
                'id_hoadon' => 8,
                'id_ct_don_thuoc' => 8,
                'id_lich_hen_pet' => 8,
                'tien_kham' => 190000,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 9,
                'id_hoadon' => 9,
                'id_ct_don_thuoc' => 9,
                'id_lich_hen_pet' => 9,
                'tien_kham' => 210000,
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 10,
                'id_hoadon' => 10,
                'id_ct_don_thuoc' => 10,
                'id_lich_hen_pet' => 10,
                'tien_kham' => 230000,
                'created_at' => Carbon::now()->subDays(24),
                'updated_at' => Carbon::now()->subDays(24),
            ],
            [
                'id' => 11,
                'id_hoadon' => 11,
                'id_ct_don_thuoc' => 11,
                'id_lich_hen_pet' => 11,
                'tien_kham' => 250000,
                'created_at' => Carbon::now()->subDays(26),
                'updated_at' => Carbon::now()->subDays(26),
            ],
            [
                'id' => 12,
                'id_hoadon' => 12,
                'id_ct_don_thuoc' => 12,
                'id_lich_hen_pet' => 12,
                'tien_kham' => 270000,
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'id' => 13,
                'id_hoadon' => 13,
                'id_ct_don_thuoc' => 13,
                'id_lich_hen_pet' => 13,
                'tien_kham' => 290000,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'id' => 14,
                'id_hoadon' => 14,
                'id_ct_don_thuoc' => 14,
                'id_lich_hen_pet' => 14,
                'tien_kham' => 310000,
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(32),
            ],
            [
                'id' => 15,
                'id_hoadon' => 15,
                'id_ct_don_thuoc' => 15,
                'id_lich_hen_pet' => 15,
                'tien_kham' => 330000,
                'created_at' => Carbon::now()->subDays(34),
                'updated_at' => Carbon::now()->subDays(34),
            ],
        ]);
    }
}

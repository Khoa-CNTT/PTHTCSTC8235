<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            // 1. Lấy lịch hẹn có khách + có dịch vụ
            $lichHen = DB::table('lich_hen_pets')
                ->whereNotNull('id_kh')
                ->whereNotNull('id_dv')
                ->inRandomOrder()
                ->first();

            if (!$lichHen) continue;

            // 2. Lấy thuốc
            $thuoc = DB::table('thuocs')->where('gia_ban', '>', 0)->inRandomOrder()->first();
            if (!$thuoc) continue;

            $soLuong = rand(1, 5);
            $idDonThuoc = DB::table('don_thuocs')->insertGetId([
                'ngay_ke_don' => now()->subDays(rand(0, 10)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // 3. Tạo dòng chi tiết đơn thuốc
            DB::table('don_thuoc_chi_tiets')->insert([
                'id_thuoc' => $thuoc->id,
                'id_don_thuoc' => $idDonThuoc,
                'so_luong' => $soLuong,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $idCtDonThuoc = DB::getPdo()->lastInsertId();

            // 4. Chọn nhân viên thu ngân
            $idNV = DB::table('nhan_viens')->where('id_chucvu', 2)->inRandomOrder()->value('id');
            $idKH = DB::table('khach_hangs')->inRandomOrder()->value('id');

            // 5. Tạo hóa đơn
            $hoaDonId = DB::table('hoa_dons')->insertGetId([
                'id_nv' => $idNV,
                'id_kh' => $idKH,
                'phuong_thuc' => rand(0, 1), // 0: tiền mặt, 1: chuyển khoản
                'ngay_xuat_hoa_don' => now()->subDays(rand(0, 10)),
                'tinh_trang' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Tạo dòng chi tiết hóa đơn (liên kết đầy đủ)
            DB::table('hoa_don_chi_tiets')->insert([
                'id_hoadon' => $hoaDonId,
                'id_lich_hen_pet' => $lichHen->id,
                'id_ct_don_thuoc' => $idCtDonThuoc,
                'tien_kham' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

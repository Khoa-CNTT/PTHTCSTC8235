<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $idLich = DB::table('lich_hen_pets')->inRandomOrder()->value('id');
            $idNV   = DB::table('nhan_viens')->where('id_chucvu', 2)->inRandomOrder()->value('id');
            $idKH = DB::table('khach_hangs')->inRandomOrder()->value('id');
            $hoaDonId = DB::table('hoa_dons')->insertGetId([
                'id_nv' => $idNV,
                'id_kh' => $idKH,
                'phuong_thuc' => rand(0, 1),
                'ngay_xuat_hoa_don' => now()->subDays(rand(0, 10)),
                'tinh_trang' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('hoa_don_chi_tiets')->insert([
                'id_lich_hen_pet' => $idLich,
                'id_hoadon' => $hoaDonId,
                'tien_kham' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

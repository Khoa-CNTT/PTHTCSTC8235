<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaCungCapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nha_cung_caps')->delete();

        DB::table('nha_cung_caps')->insert([
            [
                'id'=>1,
                'ten_ncc' => 'Công ty Dược An Khang',
                'email' => 'ankhang@duoc.vn',
                'sdt' => '0901234567',
                'dia_chi' => '12 Nguyễn Văn Cừ, Hà Nội',
                'tinh_trang' => 1,
            ],
            [
                'id'=>2,
                'ten_ncc' => 'Công ty TNHH Dược Hoà Bình',
                'email' => 'hoabinhpharma@gmail.com',
                'sdt' => '0934567890',
                'dia_chi' => '45 Trần Hưng Đạo, TP.HCM',
                'tinh_trang' => 1,
            ],
            [
                'id'=>3,
                'ten_ncc' => 'Công ty Dược Minh Tâm',
                'email' => 'minhtam@duoc.vn',
                'sdt' => '0978899001',
                'dia_chi' => '99 Lê Lợi, Đà Nẵng',
                'tinh_trang' => 1,
            ],
            [
                'id'=>4,
                'ten_ncc' => 'Pharma Đỉnh Cao',
                'email' => 'support@pharmadinhcao.vn',
                'sdt' => '0912345678',
                'dia_chi' => '25 Nguyễn Trãi, Hải Phòng',
                'tinh_trang' => 0,
            ],
            [
                'id'=>5,
                'ten_ncc' => 'Công ty Dược Phúc An',
                'email' => 'phucan@duocpharma.vn',
                'sdt' => '0909988776',
                'dia_chi' => '18 Nguyễn Thị Minh Khai, Cần Thơ',
                'tinh_trang' => 1,
            ],
            [
                'id'=>6,
                'ten_ncc' => 'Nhà Thuốc Hữu Nghị',
                'email' => 'nhathuochuunghi@gmail.com',
                'sdt' => '0911223344',
                'dia_chi' => '5 Phạm Văn Đồng, Nha Trang',
                'tinh_trang' => 1,
            ],
            [
                'id'=>7,
                'ten_ncc' => 'Dược Sĩ Mạnh',
                'email' => 'manhduoc@duocsimanh.vn',
                'sdt' => '0922334455',
                'dia_chi' => '210 Hai Bà Trưng, Vũng Tàu',
                'tinh_trang' => 0,
            ],
        ]);
    }
}

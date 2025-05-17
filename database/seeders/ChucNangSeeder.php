<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucNangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_nangs')->delete();
        DB::table('chuc_nangs')->insert([
            [ 'id' => 1,  'ten_chuc_nang' => 'Quản Lý Phiếu Nhập Thuốc' ],
            [ 'id' => 2,  'ten_chuc_nang' => 'Quản Lý Tồn Kho' ],
            [ 'id' => 3,  'ten_chuc_nang' => 'Quản Lý Lịch Hẹn' ],
            [ 'id' => 4,  'ten_chuc_nang' => 'Quản Lý Dịch Vụ' ],
            [ 'id' => 5,  'ten_chuc_nang' => 'Quản Lý Nhân Viên' ],
            [ 'id' => 6,  'ten_chuc_nang' => 'Quản Lý Khách Hàng' ],
            [ 'id' => 7,  'ten_chuc_nang' => 'Quản Lý Pet' ],
            [ 'id' => 8,  'ten_chuc_nang' => 'Quản Lý Thuốc' ],
            [ 'id' => 9,  'ten_chuc_nang' => 'Quản Lý Nhà Cung Cấp' ],
            [ 'id' => 10, 'ten_chuc_nang' => 'Quản Lý Lương' ],
            [ 'id' => 11, 'ten_chuc_nang' => 'Quản Lý Đánh Giá' ],
            [ 'id' => 12, 'ten_chuc_nang' => 'Quản Lý Kho' ],
            [ 'id' => 13, 'ten_chuc_nang' => 'Quản Lý Hóa Đơn' ],
            [ 'id' => 14, 'ten_chuc_nang' => 'Quản Lý Doanh Thu' ],
            [ 'id' => 15, 'ten_chuc_nang' => 'Quản Lý Chức Vụ' ],
            [ 'id' => 16, 'ten_chuc_nang' => 'Phân Quyền' ],
            [ 'id' => 17, 'ten_chuc_nang' => 'Chức năng bác sĩ' ],
            [ 'id' => 18, 'ten_chuc_nang' => 'Quản lý giờ' ],

        ]);
    }
}

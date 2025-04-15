<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khach_hangs')->delete();
        DB::table('khach_hangs')->insert([
            [
                'id_kh' => '1',
                'noi_dung' => 'tốt lắm',
                'ngay_tao' => '2022-01-01',
                'tinh_trang' => '1',
            ],
        ]);
    }
}

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
                'ten_ncc' => 'Công ty TNHH ABC',
                'email' => 'abc@example.com',
                'sdt' => '0909123456',
                'dia_chi' => '123 Lê Lợi, TP.HCM',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_ncc' => 'Công ty XYZ',
                'email' => 'xyz@example.com',
                'sdt' => '0911222333',
                'dia_chi' => '456 Trần Hưng Đạo, Hà Nội',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}

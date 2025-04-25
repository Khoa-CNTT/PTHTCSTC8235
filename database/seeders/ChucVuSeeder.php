<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChucVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chuc_vus')->delete();
        DB::table('chuc_vus')->insert([
            [
                'id' => 1,
                'ten_chuc_vu' => 'Bác sĩ',
                'tinh_trang' => 1,
            ],
            [
                'id' => 2,
                'ten_chuc_vu' => 'Nhân viên',
                'tinh_trang' => 1,
            ],
            [
                'id' => 3,
                'ten_chuc_vu' => 'ADMIN',
                'tinh_trang' => 1,
            ],
        ]);
    }
}

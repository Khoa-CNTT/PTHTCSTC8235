<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LichSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lich_hens')->delete();
        DB::table('lich_hens')->insert([
            [
                'id'=>1,
                'tinh_trang' => 1,
                'khung_gio' => '8:00 - 9:00',
            ],
            [
                'id'=>2,
                'tinh_trang' => 1,
                'khung_gio' => '9:00 - 10:00',
            ],
            [
                'id'=>3,
                'tinh_trang' => 1,
                'khung_gio' => '10:00 - 11:00',
            ],
            [
                'id'=>4,
                'tinh_trang' => 1,
                'khung_gio' => '13:00 - 14:00',
            ],
            [
                'id'=>5,
                'tinh_trang' => 1,
                'khung_gio' => '14:00 - 15:00',
            ],
            [
                'id'=>6,
                'tinh_trang' => 1,
                'khung_gio' => '15:00 - 16:00',
            ],
            [
                'id'=>7,
                'tinh_trang' => 1,
                'khung_gio' => '16:00 - 17:00',
            ],
        ]);
    }
}

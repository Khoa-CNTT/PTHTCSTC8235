<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khos')->delete();
        DB::table('khos')->insert([
            [
                'id'=>1,
                'ten_kho'=>'Kho chính',
                'dia_chi'=>'03 Quang Trung, DN',
                'tinh_trang'=>1,
            ],
            [
                'id'=>2,
                'ten_kho'=>'Kho phụ 1',
                'dia_chi'=>'Nguyễn Văn Linh, DN',
                'tinh_trang'=>0,
            ],
            [
                'id'=>3,
                'ten_kho'=>'Kho phụ 2',
                'dia_chi'=>'Hòa Khánh Nam, DN',
                'tinh_trang'=>0,
            ],

        ]);
    }
}

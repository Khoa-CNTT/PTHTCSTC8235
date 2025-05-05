<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class QLTonKhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thuoc_khos')->delete();

        DB::table('thuoc_khos')->insert([
            [
                'id_kho' => 1,
                'id_thuoc' => 1,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 1500,
                'gia_ban' => 2200,
                'so_luong_ton_kho' => 5,
                'han_su_dung' => Carbon::now()->addMonths(12),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 2,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 2000,
                'gia_ban' => 2800,
                'so_luong_ton_kho' => 3,
                'han_su_dung' => Carbon::now()->addMonths(10),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 3,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 1800,
                'gia_ban' => 2500,
                'so_luong_ton_kho' => 2,
                'han_su_dung' => Carbon::now()->addMonths(8),
                'tinh_trang' => 2
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 4,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 2200,
                'gia_ban' => 3000,
                'so_luong_ton_kho' => 1,
                'han_su_dung' => Carbon::now()->addMonths(9),
                'tinh_trang' => 3
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 5,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 2500,
                'gia_ban' => 3500,
                'so_luong_ton_kho' => 4,
                'han_su_dung' => Carbon::now()->addMonths(14),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 6,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 1600,
                'gia_ban' => 2300,
                'so_luong_ton_kho' => 2,
                'han_su_dung' => Carbon::now()->addMonths(11),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 7,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 2700,
                'gia_ban' => 3800,
                'so_luong_ton_kho' => 2,
                'han_su_dung' => Carbon::now()->addMonths(10),
                'tinh_trang' => 3
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 8,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 2100,
                'gia_ban' => 2900,
                'so_luong_ton_kho' => 1,
                'han_su_dung' => Carbon::now()->addMonths(12),
                'tinh_trang' => 2
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 9,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 1900,
                'gia_ban' => 2700,
                'so_luong_ton_kho' => 6,
                'han_su_dung' => Carbon::now()->addDays(13),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 10,
                'id_phieu_nhap_CT' => null,
                'gia_nhap' => 1200,
                'gia_ban' => 2000,
                'so_luong_ton_kho' => 8,
                'han_su_dung' => Carbon::now()->subDays(14),
                'tinh_trang' => 1
            ],
        ]);
    }
}

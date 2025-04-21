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
                'id_thuoc' => 1, // Paracetamol 500mg
                'gia_nhap' => 1500,
                'so_luong_ton_kho' => 500,
                'han_su_dung' => Carbon::now()->addMonths(12),
                'ngay_nhap' => Carbon::now()->subDays(10),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 2, // Amoxicillin 500mg
                'gia_nhap' => 2000,
                'so_luong_ton_kho' => 300,
                'han_su_dung' => Carbon::now()->addMonths(10),
                'ngay_nhap' => Carbon::now()->subDays(15),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 3, // Vitamin C 1000mg
                'gia_nhap' => 1800,
                'so_luong_ton_kho' => 1000,
                'han_su_dung' => Carbon::now()->addMonths(8),
                'ngay_nhap' => Carbon::now()->subDays(5),
                'tinh_trang' => 2
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 4, // Hydrocortisone 1%
                'gia_nhap' => 2200,
                'so_luong_ton_kho' => 150,
                'han_su_dung' => Carbon::now()->addMonths(9),
                'ngay_nhap' => Carbon::now()->subDays(12),
                'tinh_trang' => 3
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 5, // Omeprazole 20mg
                'gia_nhap' => 2500,
                'so_luong_ton_kho' => 400,
                'han_su_dung' => Carbon::now()->addMonths(14),
                'ngay_nhap' => Carbon::now()->subDays(20),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 6, // Loratadine 10mg
                'gia_nhap' => 1600,
                'so_luong_ton_kho' => 250,
                'han_su_dung' => Carbon::now()->addMonths(11),
                'ngay_nhap' => Carbon::now()->subDays(7),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 7, // Cefuroxime 500mg
                'gia_nhap' => 2700,
                'so_luong_ton_kho' => 220,
                'han_su_dung' => Carbon::now()->addMonths(10),
                'ngay_nhap' => Carbon::now()->subDays(9),
                'tinh_trang' => 3
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 8, // Prednisolone 5mg
                'gia_nhap' => 2100,
                'so_luong_ton_kho' => 180,
                'han_su_dung' => Carbon::now()->addMonths(12),
                'ngay_nhap' => Carbon::now()->subDays(11),
                'tinh_trang' => 2
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 9, // Metformin 500mg
                'gia_nhap' => 1900,
                'so_luong_ton_kho' => 600,
                'han_su_dung' => Carbon::now()->addDays(13),
                'ngay_nhap' => Carbon::now()->subDays(6),
                'tinh_trang' => 1
            ],
            [
                'id_kho' => 1,
                'id_thuoc' => 10, // Aspirin 81mg
                'gia_nhap' => 1200,
                'so_luong_ton_kho' => 800,
                'han_su_dung' => Carbon::now()->subDays(14),
                'ngay_nhap' => Carbon::now()->subMonths(10),
                'tinh_trang' => 1
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonThuocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First clear existing data
        DB::table('don_thuoc_chi_tiets')->delete();
        DB::table('don_thuocs')->delete();

        // Insert prescriptions directly
        DB::table('don_thuocs')->insert([
            [
                'id' => 1,
                'ngay_ke_don' => Carbon::now()->subDays(30),
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'ngay_ke_don' => Carbon::now()->subDays(28),
                'created_at' => Carbon::now()->subDays(28),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 3,
                'ngay_ke_don' => Carbon::now()->subDays(25),
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 4,
                'ngay_ke_don' => Carbon::now()->subDays(27),
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 5,
                'ngay_ke_don' => Carbon::now()->subDays(20),
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'id' => 6,
                'ngay_ke_don' => Carbon::now()->subDays(35),
                'created_at' => Carbon::now()->subDays(35),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 7,
                'ngay_ke_don' => Carbon::now()->subDays(15),
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 8,
                'ngay_ke_don' => Carbon::now()->subDays(40),
                'created_at' => Carbon::now()->subDays(40),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 9,
                'ngay_ke_don' => Carbon::now()->subDays(45),
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 10,
                'ngay_ke_don' => Carbon::now()->subDays(32),
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'id' => 11,
                'ngay_ke_don' => Carbon::now()->subDays(38),
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'id' => 12,
                'ngay_ke_don' => Carbon::now()->subDays(36),
                'created_at' => Carbon::now()->subDays(36),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 13,
                'ngay_ke_don' => Carbon::now()->subDays(42),
                'created_at' => Carbon::now()->subDays(42),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => 14,
                'ngay_ke_don' => Carbon::now()->subDays(37),
                'created_at' => Carbon::now()->subDays(37),
                'updated_at' => Carbon::now()->subDays(13),
            ],
            [
                'id' => 15,
                'ngay_ke_don' => Carbon::now()->subDays(44),
                'created_at' => Carbon::now()->subDays(44),
                'updated_at' => Carbon::now()->subDays(18),
            ],
        ]);
    }
}
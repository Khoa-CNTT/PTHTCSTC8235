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
                'ngay_ke_don' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 2,
                'ngay_ke_don' => Carbon::now()->subDays(7),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'id' => 3,
                'ngay_ke_don' => Carbon::now()->subDays(10),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 4,
                'ngay_ke_don' => Carbon::now()->subDays(12),
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 5,
                'ngay_ke_don' => Carbon::now()->subDays(14),
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ],
            [
                'id' => 6,
                'ngay_ke_don' => Carbon::now()->subDays(16),
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 7,
                'ngay_ke_don' => Carbon::now()->subDays(18),
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'id' => 8,
                'ngay_ke_don' => Carbon::now()->subDays(20),
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 9,
                'ngay_ke_don' => Carbon::now()->subDays(22),
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(22),
            ],
            [
                'id' => 10,
                'ngay_ke_don' => Carbon::now()->subDays(24),
                'created_at' => Carbon::now()->subDays(24),
                'updated_at' => Carbon::now()->subDays(24),
            ],
        ]);
    }
}
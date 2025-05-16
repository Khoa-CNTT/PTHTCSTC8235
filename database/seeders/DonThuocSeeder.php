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
            
        // Get available medication IDs
        $medicationIds = DB::table('thuocs')->pluck('id')->toArray();
        
        // Create 20 prescriptions
        for ($i = 1; $i <= 20; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            
            // Insert prescription
            $prescriptionId = DB::table('don_thuocs')->insertGetId([
                'ngay_ke_don' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // Add 2-5 medications to each prescription
            $medicationCount = rand(2, 5);
            $usedMedications = [];
            
            for ($j = 0; $j < $medicationCount; $j++) {
                // Get a random medication that hasn't been used in this prescription
                do {
                    $medicationId = $medicationIds[array_rand($medicationIds)];
                } while (in_array($medicationId, $usedMedications));
                
                $usedMedications[] = $medicationId;
                
                // Get medication price
                $medication = DB::table('thuocs')->where('id', $medicationId)->first();
                $quantity = rand(1, 10);
                
                // Insert prescription detail
                DB::table('don_thuoc_chi_tiets')->insert([
                    'id_don_thuoc' => $prescriptionId,
                    'id_thuoc' => $medicationId,
                    'so_luong' => $quantity,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
} 
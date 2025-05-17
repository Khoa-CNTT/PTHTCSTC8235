<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonThuocChiTietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing prescription details first
        DB::table('don_thuoc_chi_tiets')->delete();
        
        // Get available don_thuoc ids
        $donThuocIds = DB::table('don_thuocs')->pluck('id')->toArray();
        
        // If no prescriptions exist, create them using DonThuocSeeder
        if (empty($donThuocIds)) {
            $this->call(DonThuocSeeder::class);
            $donThuocIds = DB::table('don_thuocs')->pluck('id')->toArray();
        }
        
        // Get all available medicines
        $thuocIds = DB::table('thuocs')->pluck('id')->toArray();
        
        // For each prescription, add 2-5 medicines
        foreach ($donThuocIds as $donThuocId) {
            // Get the date of the prescription for consistency
            $donThuoc = DB::table('don_thuocs')->where('id', $donThuocId)->first();
            $date = Carbon::parse($donThuoc->ngay_ke_don);
            
            // Add 2-5 unique medicines to each prescription
            $medicineCount = rand(2, 5);
            $usedMedicines = [];
            
            for ($i = 0; $i < $medicineCount; $i++) {
                // Pick a random medicine that hasn't been used in this prescription
                do {
                    $thuocId = $thuocIds[array_rand($thuocIds)];
                } while (in_array($thuocId, $usedMedicines));
                
                $usedMedicines[] = $thuocId;
                
                // Quantity between 1-10
                $quantity = rand(1, 10);
                
                // Insert prescription detail
                DB::table('don_thuoc_chi_tiets')->insert([
                    'id_don_thuoc' => $donThuocId,
                    'id_thuoc' => $thuocId,
                    'so_luong' => $quantity,
                    'lieu_luong' => $this->getDosage(),
                    'tinh_trang' => 1, // Đã cấp thuốc
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
        
        // Now, ensure all ho_so_benh_an with id_don_thuoc have a valid prescription
        $hoSoBenhAns = DB::table('ho_so_benh_ans')
            ->whereNotNull('id_don_thuoc')
            ->get();
            
        foreach ($hoSoBenhAns as $hoSo) {
            // Check if the prescription exists
            $donThuocExists = DB::table('don_thuocs')->where('id', $hoSo->id_don_thuoc)->exists();
            
            if (!$donThuocExists) {
                // Create a new prescription
                $date = Carbon::parse($hoSo->created_at);
                
                $newDonThuocId = DB::table('don_thuocs')->insertGetId([
                    'ngay_ke_don' => $date,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                
                // Update the medical record
                DB::table('ho_so_benh_ans')
                    ->where('id', $hoSo->id)
                    ->update(['id_don_thuoc' => $newDonThuocId]);
                
                // Add 2-5 medicines to this prescription
                $medicineCount = rand(2, 5);
                $usedMedicines = [];
                
                for ($i = 0; $i < $medicineCount; $i++) {
                    // Pick a random medicine that hasn't been used in this prescription
                    do {
                        $thuocId = $thuocIds[array_rand($thuocIds)];
                    } while (in_array($thuocId, $usedMedicines));
                    
                    $usedMedicines[] = $thuocId;
                    
                    // Quantity between 1-10
                    $quantity = rand(1, 10);
                    
                    // Insert prescription detail
                    DB::table('don_thuoc_chi_tiets')->insert([
                        'id_don_thuoc' => $newDonThuocId,
                        'id_thuoc' => $thuocId,
                        'so_luong' => $quantity,
                        'lieu_luong' => $this->getDosage(),
                        'tinh_trang' => 1,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
        
        // Finally, ensure that all id_ct_don_thuoc in hoa_don_chi_tiets are valid
        $hoaDonChiTiets = DB::table('hoa_don_chi_tiets')
            ->whereNotNull('id_ct_don_thuoc')
            ->get();
            
        foreach ($hoaDonChiTiets as $chiTiet) {
            // Check if the prescription detail exists
            $donThuocChiTietExists = DB::table('don_thuoc_chi_tiets')
                ->where('id', $chiTiet->id_ct_don_thuoc)
                ->exists();
                
            if (!$donThuocChiTietExists) {
                // Create a new prescription if needed
                $hoaDon = DB::table('hoa_dons')->where('id', $chiTiet->id_hoadon)->first();
                $date = Carbon::parse($hoaDon->ngay_xuat_hoa_don);
                
                $newDonThuocId = DB::table('don_thuocs')->insertGetId([
                    'ngay_ke_don' => $date,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                
                // Create a new prescription detail
                $thuocId = $thuocIds[array_rand($thuocIds)];
                $quantity = rand(1, 10);
                
                $newDonThuocChiTietId = DB::table('don_thuoc_chi_tiets')->insertGetId([
                    'id_don_thuoc' => $newDonThuocId,
                    'id_thuoc' => $thuocId,
                    'so_luong' => $quantity,
                    'lieu_luong' => $this->getDosage(),
                    'tinh_trang' => 1,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
                
                // Update the invoice detail
                DB::table('hoa_don_chi_tiets')
                    ->where('id', $chiTiet->id)
                    ->update(['id_ct_don_thuoc' => $newDonThuocChiTietId]);
            }
        }
    }
    
    /**
     * Generate a random dosage instruction
     */
    private function getDosage()
    {
        $dosages = [
            'Uống 1 viên × 3 lần/ngày sau ăn',
            'Uống 1 viên × 2 lần/ngày sau ăn',
            'Uống 2 viên × 3 lần/ngày sau ăn',
            'Uống 1 viên mỗi sáng',
            'Uống 1 viên trước khi đi ngủ',
            'Bôi 2 lần/ngày vào vùng bị bệnh',
            'Uống 1/2 viên × 3 lần/ngày',
            'Uống 1 viên khi có triệu chứng',
            'Uống 1 gói hòa với nước × 2 lần/ngày',
            'Nhỏ 2 giọt × 3 lần/ngày',
        ];
        
        return $dosages[array_rand($dosages)];
    }
}

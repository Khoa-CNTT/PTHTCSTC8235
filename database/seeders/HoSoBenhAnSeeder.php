<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HoSoBenhAnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records
        DB::table('ho_so_benh_ans')->delete();
        
        // Get available appointments
        $appointments = DB::table('lich_hen_pets')
            ->where('tinh_trang', 1) // Completed appointments
            ->get();
            
        // Get available doctors
        $doctorIds = DB::table('nhan_viens')
            ->where('id_chucvu', 2) // Doctor role
            ->pluck('id')
            ->toArray();
            
        // Get available prescriptions
        $prescriptionIds = DB::table('don_thuocs')
            ->pluck('id')
            ->toArray();
            
        // Create medical records for some appointments
        foreach ($appointments as $index => $appointment) {
            // For demonstration purposes, create records for most appointments
            if (rand(0, 10) <= 8) { // 80% chance to create a record
                $doctorId = $doctorIds[array_rand($doctorIds)];
                
                // 70% chance to link a prescription
                $prescriptionId = null;
                if (rand(0, 10) <= 7 && !empty($prescriptionIds)) {
                    $prescriptionId = $prescriptionIds[array_rand($prescriptionIds)];
                }
                
                $diagnosis = $this->getDiagnosis();
                $status = rand(0, 1); // 0: pending, 1: completed
                $date = Carbon::parse($appointment->ngay)->addHours(rand(0, 8));
                
                DB::table('ho_so_benh_ans')->insert([
                    'id_lich_hen_pet' => $appointment->id,
                    'id_nv' => $doctorId,
                    'id_don_thuoc' => $prescriptionId,
                    'chuan_doan' => $diagnosis,
                    'tinh_trang' => $status,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
        
        // Create additional records if there aren't many appointments
        if (count($appointments) < 10) {
            $additionalCount = 10 - count($appointments);
            
            for ($i = 0; $i < $additionalCount; $i++) {
                $doctorId = $doctorIds[array_rand($doctorIds)];
                
                // 70% chance to link a prescription
                $prescriptionId = null;
                if (rand(0, 10) <= 7 && !empty($prescriptionIds)) {
                    $prescriptionId = $prescriptionIds[array_rand($prescriptionIds)];
                }
                
                $diagnosis = $this->getDiagnosis();
                $status = rand(0, 1); // 0: pending, 1: completed
                $date = Carbon::now()->subDays(rand(0, 30));
                
                DB::table('ho_so_benh_ans')->insert([
                    'id_lich_hen_pet' => $appointments[array_rand($appointments->toArray())]->id ?? null,
                    'id_nv' => $doctorId,
                    'id_don_thuoc' => $prescriptionId,
                    'chuan_doan' => $diagnosis,
                    'tinh_trang' => $status,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
    
    /**
     * Get a random diagnosis
     */
    private function getDiagnosis()
    {
        $diagnoses = [
            'Viêm da dị ứng',
            'Nhiễm trùng da',
            'Viêm tai ngoài',
            'Viêm kết mạc',
            'Viêm đường hô hấp trên',
            'Viêm phế quản',
            'Viêm dạ dày ruột',
            'Bệnh nấm da',
            'Bệnh ký sinh trùng ngoài da',
            'Tiêu chảy cấp tính',
            'Viêm tuyến vú',
            'Viêm miệng',
            'Viêm niệu đạo',
            'Rối loạn tiêu hóa',
        ];
        
        return $diagnoses[array_rand($diagnoses)];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data first
        DB::table('hoa_don_chi_tiets')->delete();
        DB::table('hoa_dons')->delete();
        
        // Reset auto-increment to ensure consistent invoice IDs
        DB::statement('ALTER TABLE hoa_dons AUTO_INCREMENT = 1');

        // Get all the possible IDs for relationships
        $lichHenIds = DB::table('lich_hen_pets')->pluck('id')->toArray();
        $nhanVienIds = DB::table('nhan_viens')->where('id_chucvu', 2)->pluck('id')->toArray();
        $khachHangIds = DB::table('khach_hangs')->pluck('id')->toArray();

        // Ensure we have thuoc data for better medicine revenue
        $this->ensureThuocData();

        // Create or update don_thuoc_chi_tiets with higher medicine quantities and prices
        $this->createOrUpdateDonThuocChiTiets();

        // Get actual prescription detail IDs with their medication information
        $donThuocChiTiets = DB::table('don_thuoc_chi_tiets as dt')
            ->join('thuocs as t', 'dt.id_thuoc', '=', 't.id')
            ->select('dt.id', 'dt.id_don_thuoc', 'dt.so_luong', 't.gia_ban', 't.ten_thuoc')
            ->get();

        if ($donThuocChiTiets->isEmpty()) {
            // If no prescription details exist, run the DonThuocSeeder first
            $this->call(DonThuocSeeder::class);
            $this->call(DonThuocChiTietSeeder::class);

            // Then get the prescription details again
            $donThuocChiTiets = DB::table('don_thuoc_chi_tiets as dt')
                ->join('thuocs as t', 'dt.id_thuoc', '=', 't.id')
                ->select('dt.id', 'dt.id_don_thuoc', 'dt.so_luong', 't.gia_ban', 't.ten_thuoc')
                ->get();
        }

        $donThuocChiTietIds = $donThuocChiTiets->pluck('id')->toArray();

        // Define invoice distribution by time periods for better analysis
        $invoiceDistribution = [
            // For previous months, create realistic revenue patterns
            ['period' => 'current-month', 'count' => 15, 'days_ago_max' => 30, 'days_ago_min' => 0],
            ['period' => 'previous-month', 'count' => 15, 'days_ago_max' => 60, 'days_ago_min' => 31],
            ['period' => 'two-months-ago', 'count' => 10, 'days_ago_max' => 90, 'days_ago_min' => 61],
            ['period' => 'older', 'count' => 10, 'days_ago_max' => 200, 'days_ago_min' => 91]
        ];

        // Create invoices with varied data based on distribution
        $currentInvoiceId = 1; // Start with ID 1
        
        foreach ($invoiceDistribution as $period) {
            for ($i = 1; $i <= $period['count']; $i++) {
                // Generate date within the specific period
                $date = Carbon::now()->subDays(rand($period['days_ago_min'], $period['days_ago_max']));
                
                // For recent invoices (current month), include some unpaid ones
                $isPaid = !($period['period'] === 'current-month' && $i <= 10);
                
                $this->createInvoice($currentInvoiceId, $isPaid, $nhanVienIds, $khachHangIds, $lichHenIds, $donThuocChiTietIds, $date);
                $currentInvoiceId++; // Increment the ID for the next invoice
            }
        }
    }

    /**
     * Ensure we have thuoc with high prices for better medicine revenue
     */
    private function ensureThuocData()
    {
        // Update existing medicine prices to higher values
        DB::table('thuocs')->update([
            'gia_ban' => DB::raw('gia_ban * 2') // Double all medicine prices
        ]);

        // Add some expensive medicines if they don't exist
        $highPricedMedicines = [
            ['ten_thuoc' => 'Esomeprazole 40mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc đặc trị dạ dày cao cấp', 'gia_ban' => 25000, 'tinh_trang' => 1],
            ['ten_thuoc' => 'Augmentin 1g', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh phổ rộng cao cấp', 'gia_ban' => 32000, 'tinh_trang' => 1],
            ['ten_thuoc' => 'Crestor 20mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc hạ mỡ máu cao cấp', 'gia_ban' => 28000, 'tinh_trang' => 1],
            ['ten_thuoc' => 'Concor 5mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc điều trị cao huyết áp cao cấp', 'gia_ban' => 31000, 'tinh_trang' => 1],
            ['ten_thuoc' => 'Xolair 150mg', 'don_vi' => 'ống tiêm', 'mo_ta' => 'Thuốc điều trị dị ứng cao cấp', 'gia_ban' => 85000, 'tinh_trang' => 1],
        ];

        foreach ($highPricedMedicines as $medicine) {
            $exists = DB::table('thuocs')->where('ten_thuoc', $medicine['ten_thuoc'])->exists();
            if (!$exists) {
                DB::table('thuocs')->insert($medicine);
            }
        }
    }

    /**
     * Create or update don_thuoc_chi_tiets with higher medicine quantities
     */
    private function createOrUpdateDonThuocChiTiets()
    {
        // Get all prescription detail IDs
        $chiTietIds = DB::table('don_thuoc_chi_tiets')->pluck('id')->toArray();
        
        // Update existing prescription details with higher quantities (5-20 instead of 1-10)
        if (!empty($chiTietIds)) {
            foreach ($chiTietIds as $id) {
                DB::table('don_thuoc_chi_tiets')
                    ->where('id', $id)
                    ->update(['so_luong' => rand(5, 20)]);
            }
        }
    }

    /**
     * Create a single invoice with its details
     */
    private function createInvoice($invoiceId, $isPaid, $nhanVienIds, $khachHangIds, $lichHenIds, $donThuocChiTietIds, $date)
    {
        // Randomize invoice data
        $idNV = $nhanVienIds[array_rand($nhanVienIds)];
        $idKH = !empty($khachHangIds) ? $khachHangIds[array_rand($khachHangIds)] : 1;
        $paymentMethod = rand(0, 1); // 0: Cash, 1: Card/Transfer
        $status = $isPaid ? 1 : 0; // Paid or unpaid based on parameter

        // Insert invoice with specific ID to ensure exact match with actual database
        DB::table('hoa_dons')->insert([
            'id' => $invoiceId, // Use the specific invoice ID
            'id_nv' => $idNV,
            'id_kh' => $idKH,
            'phuong_thuc' => $paymentMethod,
            'ngay_xuat_hoa_don' => $date,
            'tinh_trang' => $status,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Always include a lichhen
        $idLichHen = !empty($lichHenIds) ? $lichHenIds[array_rand($lichHenIds)] : null;
        $tienKham = rand(50000, 200000);

        DB::table('hoa_don_chi_tiets')->insert([
            'id_hoadon' => $invoiceId,
            'id_lich_hen_pet' => $idLichHen,
            'id_ct_don_thuoc' => null,
            'tien_kham' => $tienKham,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Add 1-3 prescription details for each invoice to increase medicine revenue
        if (!empty($donThuocChiTietIds)) {
            $numPrescriptions = rand(1, 3); // Each invoice can now have multiple medicine items
            $usedPrescriptions = [];
            
            for ($i = 0; $i < $numPrescriptions; $i++) {
                // Make sure we don't use the same prescription detail twice
                do {
                    $idDonThuoc = $donThuocChiTietIds[array_rand($donThuocChiTietIds)];
                } while (in_array($idDonThuoc, $usedPrescriptions));
                
                $usedPrescriptions[] = $idDonThuoc;

                DB::table('hoa_don_chi_tiets')->insert([
                    'id_hoadon' => $invoiceId,
                    'id_lich_hen_pet' => null,
                    'id_ct_don_thuoc' => $idDonThuoc,
                    'tien_kham' => 0, // No examination fee for prescription item
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}

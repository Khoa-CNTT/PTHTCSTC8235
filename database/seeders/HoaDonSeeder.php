<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data first
        DB::table('hoa_don_chi_tiets')->delete();
        DB::table('hoa_dons')->delete();

        // Get all the possible IDs for relationships
        $lichHenIds = DB::table('lich_hen_pets')->pluck('id')->toArray();
        $nhanVienIds = DB::table('nhan_viens')->where('id_chucvu', 2)->pluck('id')->toArray();
        $khachHangIds = DB::table('khach_hangs')->pluck('id')->toArray();

        // Get actual prescription detail IDs with their medication information
        $donThuocChiTiets = DB::table('don_thuoc_chi_tiets as dt')
            ->join('thuocs as t', 'dt.id_thuoc', '=', 't.id')
            ->select('dt.id', 'dt.id_don_thuoc', 'dt.so_luong', 't.gia_ban', 't.ten_thuoc')
            ->get();

        if ($donThuocChiTiets->isEmpty()) {
            // If no prescription details exist, run the DonThuocSeeder first
            $this->call(DonThuocSeeder::class);

            // Then get the prescription details again
            $donThuocChiTiets = DB::table('don_thuoc_chi_tiets as dt')
                ->join('thuocs as t', 'dt.id_thuoc', '=', 't.id')
                ->select('dt.id', 'dt.id_don_thuoc', 'dt.so_luong', 't.gia_ban', 't.ten_thuoc')
                ->get();
        }

        $donThuocChiTietIds = $donThuocChiTiets->pluck('id')->toArray();

        // Create invoices with varied data
        $totalInvoices = 30;
        $unpaidInvoices = 10; // Exactly 10 unpaid invoices
        $paidInvoices = $totalInvoices - $unpaidInvoices;

        // Create paid invoices first
        for ($i = 1; $i <= $paidInvoices; $i++) {
            $this->createInvoice(true, $nhanVienIds, $khachHangIds, $lichHenIds, $donThuocChiTietIds);
        }

        // Now create exactly 10 unpaid invoices
        for ($i = 1; $i <= $unpaidInvoices; $i++) {
            $this->createInvoice(false, $nhanVienIds, $khachHangIds, $lichHenIds, $donThuocChiTietIds);
        }
    }

    /**
     * Create a single invoice with its details
     */
    private function createInvoice($isPaid, $nhanVienIds, $khachHangIds, $lichHenIds, $donThuocChiTietIds)
    {
        // Randomize invoice data
        $idNV = $nhanVienIds[array_rand($nhanVienIds)];
        $idKH = !empty($khachHangIds) ? $khachHangIds[array_rand($khachHangIds)] : 1;
        $paymentMethod = rand(0, 1); // 0: Cash, 1: Card/Transfer
        $status = $isPaid ? 1 : 0; // Paid or unpaid based on parameter

        // For unpaid invoices, use more recent dates
        if ($isPaid) {
            $date = Carbon::now()->subDays(rand(5, 30));
        } else {
            $date = Carbon::now()->subDays(rand(0, 4));
        }

        // Insert invoice - ensure each invoice has a valid customer ID
        $hoaDonId = DB::table('hoa_dons')->insertGetId([
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
            'id_hoadon' => $hoaDonId,
            'id_lich_hen_pet' => $idLichHen,
            'id_ct_don_thuoc' => null,
            'tien_kham' => $tienKham,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Always include a prescription detail for each invoice (to ensure medication cost shows up)
        if (!empty($donThuocChiTietIds)) {
            $idDonThuoc = $donThuocChiTietIds[array_rand($donThuocChiTietIds)];

            DB::table('hoa_don_chi_tiets')->insert([
                'id_hoadon' => $hoaDonId,
                'id_lich_hen_pet' => null,
                'id_ct_don_thuoc' => $idDonThuoc,
                'tien_kham' => 0, // No examination fee for prescription item
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}

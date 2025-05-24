<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            // Cơ bản
            ChucVuSeeder::class,
            ChucNangSeeder::class,
            KhachHangSeeder::class,
            NhanVienSeeder::class,
            PhanQuyenSeeder::class,
            
            // Thuốc và kho
            KhoSeeder::class,
            NhaCungCapSeeder::class,
            ThuocSeeder::class,
            QLTonKhoSeeder::class,
            
            // Thú cưng
            PetSeeder::class,
            
            // Dịch vụ
            LoaiDichVuSeeder::class,
            DichVuSeeder::class,
            
            // Lịch hẹn
            LichSeeder::class,
            LichHenSeeder::class,
            
            // Đơn thuốc
            DonThuocSeeder::class,
            DonThuocChiTietSeeder::class,
            
            // Hồ sơ bệnh án
            HoSoBenhAnSeeder::class,
            
            // Hóa đơn
            HoaDonSeeder::class,
            HoaDonChiTietSeeder::class,
            
            // Đánh giá
            DanhGiaSeeder::class,
            
            // Lương
            LuongSeeder::class,
        ]);
    }
}

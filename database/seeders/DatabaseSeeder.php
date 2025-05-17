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
            DichVuSeeder::class,
            LoaiDichVuSeeder::class,
            KhoSeeder::class,
            NhaCungCapSeeder::class,
            ChucVuSeeder::class,
            ThuocSeeder::class,
            LichSeeder::class,
            LichHenSeeder::class,
            DanhGiaSeeder::class,
            LuongSeeder::class,
            KhachHangSeeder::class,
            NhaCungCapSeeder::class,
            ChucNangSeeder::class,
            QLTonKhoSeeder::class,
            NhanVienSeeder::class,
            PetSeeder::class,
            DonThuocSeeder::class,
            HoSoBenhAnSeeder::class,
            PhanQuyenSeeder::class,
            LichHenSeeder::class,
            HoaDonSeeder::class,
        ]);
    }
}

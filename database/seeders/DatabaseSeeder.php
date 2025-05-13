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
            KhachHangSeeder::class,
            NhaCungCapSeeder::class,
            ChucNangSeeder::class,
            QLTonKhoSeeder::class,
            NhanVienSeeder::class,
            PetSeeder::class,
            HoSoBenhAnSeeder::class,
            PhanQuyenSeeder::class,
        ]);

        // Thêm chức vụ
        DB::table('chuc_vus')->insert([
            [
                'ten_chuc_vu' => 'Bác sĩ',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_chuc_vu' => 'Y tá',
                'tinh_trang' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Thêm bác sĩ
        DB::table('nhan_viens')->insert([
            [
                'ten_nv' => 'Nguyễn Văn A',
                'gioi_tinh' => 1,
                'email' => 'nguyenvana@example.com',
                'tien_kham' => 200000,
                'password' => Hash::make('password'),
                'mo_ta' => 'Bác sĩ thú y có kinh nghiệm 5 năm',
                'hinh_anh' => 'default.jpg',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ten_nv' => 'Trần Thị B',
                'gioi_tinh' => 0,
                'email' => 'tranthib@example.com',
                'tien_kham' => 180000,
                'password' => Hash::make('password'),
                'mo_ta' => 'Bác sĩ thú y có kinh nghiệm 3 năm',
                'hinh_anh' => 'default.jpg',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

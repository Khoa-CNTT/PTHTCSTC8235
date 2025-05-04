<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('khach_hangs')->delete();
        DB::table('khach_hangs')->insert([
            [
                'id' => 1,
                'ho_va_ten' => 'Nguyen Thi A',
                'email' => 'nguyenthia@example.com',
                'password' => bcrypt('password123'), // Mã hóa mật khẩu
                'so_dien_thoai' => '0123456789',
                'ngay_sinh' => '1995-03-25',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
                'hash_reset' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'ho_va_ten' => 'Tran Minh B',
                'email' => 'tranminhb@example.com',
                'password' => bcrypt('password456'),
                'so_dien_thoai' => '0987654321',
                'ngay_sinh' => '1990-06-15',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'ho_va_ten' => 'Le Thanh C',
                'email' => 'lethanhc@example.com',
                'password' => bcrypt('password789'),
                'so_dien_thoai' => '0912345678',
                'ngay_sinh' => '1985-11-20',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'ho_va_ten' => 'Nguyễn Công Đoàn',
                'email' => 'doancarat@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0368047839',
                'ngay_sinh' => '2003-05-08',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

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
                'ho_va_ten' => 'Lương Văn Ái',
                'email' => 'linkcrdy@gmail.com',
                'password' => bcrypt('Luongvan@i1019'), // Mã hóa mật khẩu
                'so_dien_thoai' => '0123456789',
                'ngay_sinh' => '2003-04-30',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
                'hash_reset' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'ho_va_ten' => 'Huỳnh Nguyễn Cao Đức',
                'email' => 'tthello123@gmail.com',
                'password' => bcrypt('Huynhvanduc284@'), // Mã hóa mật khẩu
                'so_dien_thoai' => '0123456789',
                'ngay_sinh' => '2003-04-28',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
                'hash_reset' => Str::random(40), // Tạo chuỗi hash ngẫu nhiên
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

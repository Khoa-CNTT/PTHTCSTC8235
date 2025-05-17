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
            [
                'id' => 5,
                'ho_va_ten' => 'Pham Ha',
                'email' => 'phamthithuha3112@gmail.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0964810993',
                'ngay_sinh' => '2003-10-04',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'ho_va_ten' => 'Trần Văn Tài',
                'email' => 'tai.tran@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000001',
                'ngay_sinh' => '1995-08-12',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'ho_va_ten' => 'Nguyễn Hữu Phúc',
                'email' => 'phuc.nguyen@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000002',
                'ngay_sinh' => '1997-09-10',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 8,
                'ho_va_ten' => 'Lê Minh Thư',
                'email' => 'thu.le@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000003',
                'ngay_sinh' => '2000-01-01',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 9,
                'ho_va_ten' => 'Đỗ Quỳnh Anh',
                'email' => 'quynhanh.do@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000004',
                'ngay_sinh' => '1998-03-20',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 10,
                'ho_va_ten' => 'Bùi Văn Duy',
                'email' => 'duy.bui@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000005',
                'ngay_sinh' => '1999-06-15',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 11,
                'ho_va_ten' => 'Ngô Thanh Hương',
                'email' => 'huong.ngo@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000006',
                'ngay_sinh' => '1996-12-01',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 12,
                'ho_va_ten' => 'Võ Nhật Nam',
                'email' => 'nam.vo@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000007',
                'ngay_sinh' => '2002-02-02',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'ho_va_ten' => 'Phan Thanh Tùng',
                'email' => 'tung.phan@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000008',
                'ngay_sinh' => '1995-05-05',
                'is_active' => 1,
                'is_block' => 0,
                'hash_active' => Str::random(40),
                'hash_reset' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 14,
                'ho_va_ten' => 'Mai Thị Lan',
                'email' => 'lan.mai@example.com',
                'password' => bcrypt('123456'),
                'so_dien_thoai' => '0911000009',
                'ngay_sinh' => '1994-07-07',
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

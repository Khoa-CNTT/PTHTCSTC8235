<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HoaDonSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo hóa đơn từ ngày 15/6/2024 đến hiện tại
        $startMonth = 6; // Tháng 6
        $startYear = 2024;
        $startDay = 15; // Bắt đầu từ ngày 15/6/2024
        $endDate = now(); // Ngày hiện tại

        $currentMonth = Carbon::create($startYear, $startMonth, $startDay);
        
        // Lặp qua các tháng từ 6/2024 đến hiện tại
        while ($currentMonth->lte($endDate)) {
            $year = $currentMonth->year;
            $month = $currentMonth->month;
            
            // Bỏ qua tháng 6/2024 nếu ngày nhỏ hơn 15 (cho tháng đầu tiên)
            if ($year == $startYear && $month == $startMonth && $currentMonth->day < $startDay) {
                // Chuyển sang tháng tiếp theo
                $currentMonth->addMonth();
                continue;
            }
            
            // Tạo 3 hóa đơn cho mỗi tháng
            $soHoaDon = 3;
            
            for ($i = 1; $i <= $soHoaDon; $i++) {
                // Tình trạng thanh toán:
                // - Các hóa đơn ở tháng 5/2025 có tình trạng = 0 (chưa thanh toán)
                // - Tất cả hóa đơn khác đều có tình trạng = 1 (đã thanh toán)
                $tinhTrang = 1; // Mặc định đã thanh toán
                if ($year == 2025 && $month == 5) {
                    $tinhTrang = 0; // Chưa thanh toán cho tháng 5/2025
                }
                
                $this->taoHoaDon($year, $month, $tinhTrang, $i);
            }
            
            // Chuyển sang tháng tiếp theo
            $currentMonth->addMonth();
        }
    }

    private function taoHoaDon($year, $month, $tinhTrang, $soThuTu)
    {
        // 1. Lấy lịch hẹn có khách + có dịch vụ
        $lichHen = DB::table('lich_hen_pets')
            ->whereNotNull('id_kh')
            ->whereNotNull('id_dv')
            ->inRandomOrder()
            ->first();

        if (!$lichHen) return;

        // 2. Lấy thuốc
        $thuoc = DB::table('thuocs')->where('gia_ban', '>', 0)->inRandomOrder()->first();
        if (!$thuoc) return;

        $soLuong = rand(1, 5);
        $idDonThuoc = DB::table('don_thuocs')->insertGetId([
            'ngay_ke_don' => now()->subDays(rand(0, 10)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 3. Tạo dòng chi tiết đơn thuốc
        DB::table('don_thuoc_chi_tiets')->insert([
            'id_thuoc' => $thuoc->id,
            'id_don_thuoc' => $idDonThuoc,
            'so_luong' => $soLuong,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idCtDonThuoc = DB::getPdo()->lastInsertId();

        // 4. Chọn nhân viên thu ngân và khách hàng (đảm bảo có khách hàng)
        $idNV = DB::table('nhan_viens')->where('id_chucvu', 2)->inRandomOrder()->value('id');
        
        // Danh sách khách hàng có sẵn để đảm bảo có tên đầy đủ
        $danhSachKhachHang = [
            ['id' => 1, 'ten' => 'Nguyen Thi A'],
            ['id' => 2, 'ten' => 'Tran Minh B'],
            ['id' => 3, 'ten' => 'Le Thanh C'],
            ['id' => 4, 'ten' => 'Pham Van D'],
            ['id' => 5, 'ten' => 'Hoang Thi E'],
            ['id' => 6, 'ten' => 'Nguyen Cong Doan'],
        ];
        
        // Trước tiên, thử tìm trong CSDL
        $khachHang = DB::table('khach_hangs')
            ->whereRaw("TRIM(ten_kh) != ''") // Đảm bảo tên không rỗng sau khi loại bỏ khoảng trắng
            ->whereNotNull('ten_kh') // Đảm bảo có tên khách hàng
            ->inRandomOrder()
            ->first();
            
        if (!$khachHang) {
            // Nếu không tìm thấy khách hàng phù hợp, tạo hoặc sử dụng khách hàng cố định
            $khachHangCoDinh = $danhSachKhachHang[array_rand($danhSachKhachHang)];
            
            // Kiểm tra xem ID khách hàng có tồn tại không
            $khachHangTonTai = DB::table('khach_hangs')->where('id', $khachHangCoDinh['id'])->first();
            
            if ($khachHangTonTai) {
                // Nếu tồn tại, sử dụng id đó
                $idKH = $khachHangTonTai->id;
            } else {
                // Nếu không, thêm mới khách hàng
                $idKH = DB::table('khach_hangs')->insertGetId([
                    'id' => $khachHangCoDinh['id'],
                    'ten_kh' => $khachHangCoDinh['ten'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            $idKH = $khachHang->id;
        }
        
        // Tạo ngày trong tháng - phân bố đều trong tháng
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        
        // Phân bố ngày theo thứ tự của hóa đơn trong tháng
        $dayRangeStart = 1 + (int)(($soThuTu - 1) * $daysInMonth / 3); // Chia cho 3 vì có 3 hóa đơn
        $dayRangeEnd = (int)($soThuTu * $daysInMonth / 3);
        $randomDay = rand($dayRangeStart, $dayRangeEnd);
        
        $randomDate = Carbon::create($year, $month, $randomDay);
        
        // 5. Tạo hóa đơn
        $hoaDonId = DB::table('hoa_dons')->insertGetId([
            'id_nv' => $idNV,
            'id_kh' => $idKH,
            'phuong_thuc' => rand(0, 1), // 0: tiền mặt, 1: chuyển khoản
            'ngay_xuat_hoa_don' => $randomDate,
            'tinh_trang' => $tinhTrang, // Áp dụng tình trạng đã truyền vào
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Tạo dòng chi tiết hóa đơn
        DB::table('hoa_don_chi_tiets')->insert([
            'id_hoadon' => $hoaDonId,
            'id_lich_hen_pet' => $lichHen->id,
            'id_ct_don_thuoc' => $idCtDonThuoc,
            'tien_kham' => rand(50000, 100000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

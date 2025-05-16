<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoanhThuController extends Controller
{
    public function tongHop(Request $request)
    {
        Log::info('GỌI API /doanh-thu/tong-hop', [$request->all()]);
        $year = $request->query('year');
        $month = $request->query('month');

        // Doanh thu dịch vụ
        $queryDichVu = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
            ->whereNull('hoa_don_chi_tiets.id_ct_don_thuoc') // Chỉ lấy các bản ghi không có don_thuoc_chi_tiet
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);

        if ($month && $month > 0) {
            $queryDichVu->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $dataDichVu = $queryDichVu
            ->selectRaw('
                SUM(hoa_don_chi_tiets.tien_kham) as tong_kham,
                SUM(dich_vus.gia) as tong_dich_vu,
                COUNT(DISTINCT hoa_dons.id) as tong_hoa_don
            ')
            ->first();

        // Doanh thu thuốc
        $queryThuoc = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->whereNotNull('hoa_don_chi_tiets.id_ct_don_thuoc') // Chỉ lấy các bản ghi có don_thuoc_chi_tiet
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);
            
        if ($month && $month > 0) {
            $queryThuoc->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $dataThuoc = $queryThuoc
            ->selectRaw('SUM(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban) as tong_thuoc')
            ->first();

        return response()->json([
            'tong_tien' => ($dataDichVu->tong_dich_vu ?? 0) + ($dataThuoc->tong_thuoc ?? 0),
            'tong_dich_vu' => $dataDichVu->tong_dich_vu ?? 0,
            'tong_thuoc' => $dataThuoc->tong_thuoc ?? 0,
            'tong_hoa_don' => $dataDichVu->tong_hoa_don ?? 0,
        ]);
    }

    public function bieuDo(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        // TRƯỜNG HỢP 1: LỌC THEO THÁNG
        if ($month && $month > 0) {
            // Doanh thu dịch vụ trong tháng
            $dataDichVu = DB::table('hoa_dons')
                ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
                ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month)
                ->whereNull('hoa_don_chi_tiets.id_ct_don_thuoc')
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(dich_vus.gia) as tong_dich_vu,
                    COUNT(DISTINCT hoa_dons.id) as so_hoa_don
                ')
                ->groupBy('thang')
                ->first();

            // Doanh thu thuốc trong tháng 
            $dataThuoc = DB::table('hoa_dons')
                ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
                ->join('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
                ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month)
                ->whereNotNull('hoa_don_chi_tiets.id_ct_don_thuoc')
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban) as tong_thuoc
                ')
                ->groupBy('thang')
                ->first();

            // Tổng số hóa đơn không phân biệt loại
            $soHoaDon = DB::table('hoa_dons')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month)
                ->count();

            $result = [
                [
                    'thang' => $month,
                    'tong_dich_vu' => $dataDichVu->tong_dich_vu ?? 0,
                    'tong_thuoc' => $dataThuoc->tong_thuoc ?? 0,
                    'so_hoa_don' => $soHoaDon
                ]
            ];

            return response()->json($result);
        }
        
        // TRƯỜNG HỢP 2: THỐNG KÊ THEO CẢ NĂM, NHÓM THEO THÁNG
        else {
            // Dịch vụ
            $dataDichVu = DB::table('hoa_dons')
                ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
                ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereNull('hoa_don_chi_tiets.id_ct_don_thuoc')
                ->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(dich_vus.gia) as tong_dich_vu
                ')
                ->get();

            // Thuốc
            $dataThuoc = DB::table('hoa_dons')
                ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
                ->join('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
                ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereNotNull('hoa_don_chi_tiets.id_ct_don_thuoc')
                ->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban) as tong_thuoc
                ')
                ->get();
                
            // Số hóa đơn theo tháng
            $dataHoaDon = DB::table('hoa_dons')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    COUNT(id) as so_hoa_don
                ')
                ->get();

            // Tạo mảng các tháng trong năm
            $months = range(1, 12);
            $result = [];
            
            foreach ($months as $month) {
                // Tìm dữ liệu mỗi loại theo tháng
                $dv = $dataDichVu->firstWhere('thang', $month);
                $thuoc = $dataThuoc->firstWhere('thang', $month);
                $hd = $dataHoaDon->firstWhere('thang', $month);
                
                // Thêm vào kết quả
                $result[] = [
                    'thang' => $month,
                    'tong_dich_vu' => $dv ? $dv->tong_dich_vu : 0,
                    'tong_thuoc' => $thuoc ? $thuoc->tong_thuoc : 0,
                    'so_hoa_don' => $hd ? $hd->so_hoa_don : 0
                ];
            }

            return response()->json($result);
        }
    }

    public function hoaDonDaThanhToan(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        $query = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'hoa_dons.id_nv', '=', 'nhan_viens.id')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);

        if ($month && $month > 0) {
            $query->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $ds = $query->select(
            'hoa_dons.id as ma_hd',
            'khach_hangs.ho_va_ten as ten_khach',
            DB::raw('DATE_FORMAT(hoa_dons.ngay_xuat_hoa_don, "%d/%m/%Y") as ngay_tt'),
            'hoa_dons.tong_tien',
            'nhan_viens.ten_nv',
            'hoa_dons.ghi_chu'
        )->get();

        return response()->json($ds);
    }
}

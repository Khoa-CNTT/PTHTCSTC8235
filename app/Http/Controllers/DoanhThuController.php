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

        // Doanh thu dịch vụ (KHÔNG còn whereNull nữa)
        $queryDichVu = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);

        if ($month && $month > 0) {
            $queryDichVu->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $dataDichVu = $queryDichVu
            ->selectRaw('
            SUM(hoa_don_chi_tiets.tien_kham) as tong_dich_vu,
            COUNT(DISTINCT hoa_dons.id) as tong_hoa_don
        ')
            ->first();

        // Doanh thu thuốc (giữ nguyên)
        $queryThuoc = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->whereNotNull('hoa_don_chi_tiets.id_ct_don_thuoc')
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
            // Doanh thu dịch vụ trong tháng (tính theo tiền khám)
            $dataDichVu = DB::table('hoa_dons')
                ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
                ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
                ->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month)
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(hoa_don_chi_tiets.tien_kham) as tong_dich_vu
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
                ->selectRaw('
                    MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                    SUM(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban) as tong_thuoc
                ')
                ->groupBy('thang')
                ->first();

            $soHoaDon = DB::table('hoa_dons')
                ->whereYear('ngay_xuat_hoa_don', $year)
                ->whereMonth('ngay_xuat_hoa_don', $month)
                ->count();

            return response()->json([
                [
                    'thang' => $month,
                    'tong_dich_vu' => $dataDichVu->tong_dich_vu ?? 0,
                    'tong_thuoc' => $dataThuoc->tong_thuoc ?? 0,
                    'so_hoa_don' => $soHoaDon
                ]
            ]);
        }

        // TRƯỜNG HỢP 2: THỐNG KÊ CẢ NĂM
        $dataDichVu = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
            ->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->selectRaw('
                MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                SUM(hoa_don_chi_tiets.tien_kham) as tong_dich_vu
            ')
            ->get();

        $dataThuoc = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year)
            ->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->selectRaw('
                MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
                SUM(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban) as tong_thuoc
            ')
            ->get();

        $dataHoaDon = DB::table('hoa_dons')
            ->whereYear('ngay_xuat_hoa_don', $year)
            ->groupBy(DB::raw('MONTH(ngay_xuat_hoa_don)'))
            ->orderBy(DB::raw('MONTH(ngay_xuat_hoa_don)'))
            ->selectRaw('
                MONTH(ngay_xuat_hoa_don) as thang,
                COUNT(id) as so_hoa_don
            ')
            ->get();

        // Gộp dữ liệu
        $result = [];
        foreach (range(1, 12) as $thang) {
            $dv = $dataDichVu->firstWhere('thang', $thang);
            $thuoc = $dataThuoc->firstWhere('thang', $thang);
            $hd = $dataHoaDon->firstWhere('thang', $thang);

            $result[] = [
                'thang' => $thang,
                'tong_dich_vu' => $dv?->tong_dich_vu ?? 0,
                'tong_thuoc' => $thuoc?->tong_thuoc ?? 0,
                'so_hoa_don' => $hd?->so_hoa_don ?? 0
            ];
        }

        return response()->json($result);
    }
}

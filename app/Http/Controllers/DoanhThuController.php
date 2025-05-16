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

        $query = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
            ->leftJoin('ho_so_benh_ans', 'lich_hen_pets.id', '=', 'ho_so_benh_ans.id_lich_hen_pet')
            ->leftJoin('don_thuoc_chi_tiets as ct', 'ho_so_benh_ans.id_don_thuoc', '=', 'ct.id_don_thuoc')
            ->leftJoin('thuocs as t', 'ct.id_thuoc', '=', 't.id')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);

        if ($month && $month > 0) {
            $query->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $data = $query
            ->selectRaw('
            SUM(hoa_don_chi_tiets.tien_kham) as tong_kham,
            SUM(lich_hen_pets.tien_coc) as tong_coc,
            SUM(dich_vus.gia) as tong_dich_vu,
            SUM(ct.so_luong * t.gia_ban) as tong_thuoc,
            COUNT(DISTINCT hoa_dons.id) as tong_hoa_don
        ')
            ->first();

        return response()->json([
            'tong_tien' => ($data->tong_dich_vu) + ($data->tong_thuoc ?? 0),
            'tong_dich_vu' => $data->tong_dich_vu,
            'tong_thuoc' => $data->tong_thuoc ?? 0,
            'tong_hoa_don' => $data->tong_hoa_don,
        ]);
    }

    public function bieuDo(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month'); // thêm phần lấy tháng nếu có

        $query = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->join('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
            ->leftJoin('ho_so_benh_ans', 'lich_hen_pets.id', '=', 'ho_so_benh_ans.id_lich_hen_pet')
            ->leftJoin('don_thuoc_chi_tiets as ct', 'ho_so_benh_ans.id_don_thuoc', '=', 'ct.id_don_thuoc')
            ->leftJoin('thuocs as t', 'ct.id_thuoc', '=', 't.id')
            ->whereYear('hoa_dons.ngay_xuat_hoa_don', $year);

        // Nếu có chọn tháng cụ thể thì chỉ lấy đúng tháng đó
        if ($month && $month > 0) {
            $query->whereMonth('hoa_dons.ngay_xuat_hoa_don', $month);
        }

        $query->groupBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->orderBy(DB::raw('MONTH(hoa_dons.ngay_xuat_hoa_don)'))
            ->selectRaw('
            MONTH(hoa_dons.ngay_xuat_hoa_don) as thang,
            SUM(hoa_don_chi_tiets.tien_kham) as tong_kham,
            SUM(dich_vus.gia) as tong_dich_vu,
            SUM(ct.so_luong * t.gia_ban) as tong_thuoc,
            COUNT(DISTINCT hoa_dons.id) as so_hoa_don
        ');

        $data = $query->get();

        return response()->json($data);
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

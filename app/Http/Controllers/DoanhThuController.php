<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoanhThuController extends Controller
{
    public function doanhThuTheoThoiGian(Request $request)
    {
        $query = DB::table('hoa_dons')
            ->join('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->where('hoa_dons.tinh_trang', 1);

        // Lọc theo tháng nếu có
        if ($request->has('thang') && $request->thang > 0) {
            $query->whereMonth('hoa_dons.ngay_xuat_hoa_don', $request->thang);
        }

        // Lọc theo khoảng ngày nếu có
        if ($request->filled('tu_ngay') && $request->filled('den_ngay')) {
            $query->whereBetween('hoa_dons.ngay_xuat_hoa_don', [$request->tu_ngay, $request->den_ngay]);
        }

        $hoaDons = $query->select(
            'hoa_dons.id',
            'hoa_dons.ngay_xuat_hoa_don',
            'hoa_dons.phuong_thuc',
            'hoa_dons.id_nv',
            'hoa_don_chi_tiets.tien_kham',
            'hoa_don_chi_tiets.id_lich_hen_pet'
        )->get();

        // Tính doanh thu theo hóa đơn
        $ketQua = $hoaDons->map(function ($hd) {
            // Lấy dịch vụ & cọc
            $dv = DB::table('lich_hen_pets')->where('id', $hd->id_lich_hen_pet)->first();
            $giaDv = $dv?->id_dv ? DB::table('dich_vus')->where('id', $dv->id_dv)->value('gia') ?? 0 : 0;
            $tienCoc = $dv->tien_coc ?? 0;

            // Lấy tiền thuốc
            $idDonThuoc = DB::table('ho_so_benh_ans')->where('id_lich_hen_pet', $hd->id_lich_hen_pet)->value('id_don_thuoc');
            $tienThuoc = $idDonThuoc ? DB::table('don_thuoc_chi_tiets as ct')
                ->join('thuocs as t', 'ct.id_thuoc', '=', 't.id')
                ->where('ct.id_don_thuoc', $idDonThuoc)
                ->selectRaw('SUM(ct.so_luong * t.gia_ban) as tong')
                ->value('tong') ?? 0 : 0;

            return [
                'id' => $hd->id,
                'ngay' => $hd->ngay_xuat_hoa_don,
                'tien_kham' => $hd->tien_kham ?? 0,
                'doanh_thu_dich_vu' => $giaDv,
                'doanh_thu_thuoc' => $tienThuoc,
                'tien_coc' => $tienCoc,
                'tong_doanh_thu' => ($hd->tien_kham ?? 0) + $giaDv + $tienThuoc - $tienCoc,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $ketQua
        ]);
    }
}

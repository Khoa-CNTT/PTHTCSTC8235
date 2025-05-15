<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoanhThuController extends Controller
{
    public function tongHop()
    {
        $tongDoanhThu = DB::table('hoa_dons')->where('trang_thai', '1')->sum('tong_tien');
        $tongDichVu   = DB::table('hoa_dons')->where('trang_thai', '1')->sum('tien_dich_vu');
        $tongThuoc    = DB::table('hoa_dons')->where('trang_thai', '1')->sum('tien_thuoc');
        $soHoaDon     = DB::table('hoa_dons')->where('trang_thai', '1')->count();

        return response()->json([
            'tong_doanh_thu' => $tongDoanhThu,
            'tong_dich_vu'   => $tongDichVu,
            'tong_thuoc'     => $tongThuoc,
            'so_hoa_don'     => $soHoaDon,
        ]);
    }
    public function hoaDonDaThanhToan()
    {
        $data = DB::table('hoa_dons')
            ->join('khach_hangs', 'hoa_dons.id_khach', '=', 'khach_hangs.id')
            ->join('nhan_viens', 'hoa_dons.id_nv', '=', 'nhan_viens.id')
            ->where('hoa_dons.trang_thai', 'đã thanh toán')
            ->select([
                'hoa_dons.id as ma_hd',
                'khach_hangs.ten_khach as ten_khach',
                'hoa_dons.ngay_thanh_toan as ngay_tt',
                'hoa_dons.tong_tien',
                'nhan_viens.ten_nv',
                'hoa_dons.ghi_chu'
            ])
            ->orderByDesc('hoa_dons.ngay_thanh_toan')
            ->get();

        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    // Lấy danh sách hóa đơn
    public function danhSach()
    {
        $data = DB::table('hoa_dons')
            ->join('khach_hangs', 'hoa_dons.id_kh', '=', 'khach_hangs.id')
            ->join('pets', 'hoa_dons.id_pet', '=', 'pets.id')
            ->select(
                'hoa_dons.*',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'pets.ten_pet'
            )
            ->orderByDesc('hoa_dons.id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // Lấy chi tiết hóa đơn
    public function chiTiet($id)
    {
        $hoa_don = DB::table('hoa_dons')->where('id', $id)->first();

        if (!$hoa_don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ]);
        }

        $chi_tiet = DB::table('hoa_don_chi_tiets')
            ->join('thuocs', 'hoa_don_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->where('hoa_don_chi_tiets.id_hoa_don', $id)
            ->select('thuocs.ten_thuoc', 'hoa_don_chi_tiets.*')
            ->get();

        return response()->json([
            'status' => true,
            'hoa_don' => $hoa_don,
            'chi_tiet' => $chi_tiet
        ]);
    }

    // Cập nhật hóa đơn
    public function update(Request $request)
    {
        $hoa_don = DB::table('hoa_dons')->where('id', $request->id)->first();

        if (!$hoa_don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ]);
        }

        DB::table('hoa_dons')
            ->where('id', $request->id)
            ->update([
                'phuong_thuc' => $request->phuong_thuc,
                'id_nv' => $request->id_nv,
                'tien_kham' => $request->tien_kham,
                'updated_at' => now()
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật hóa đơn thành công'
        ]);
    }


    // Xóa hóa đơn
    public function xoa(Request $request)
    {
        $hoa_don = DB::table('hoa_dons')->where('id', $request->id)->first();

        if (!$hoa_don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ]);
        }

        DB::table('hoa_don_chi_tiets')->where('id_hoa_don', $request->id)->delete();
        DB::table('hoa_dons')->where('id', $request->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa hóa đơn thành công'
        ]);
    }
}

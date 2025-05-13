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
    public function thanhToan(Request $request)
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
                'id_nv' => $request->id_nv,
                'phuong_thuc' => $request->phuong_thuc,
                'tien_kham' => $request->tien_kham,
                'tinh_trang' => 1, // Đánh dấu là đã thanh toán
                'updated_at' => now()
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Thanh toán thành công'
        ]);
    }

    // Lấy chi tiết hóa đơn
    public function chiTietTien($id)
    {
        // Lấy id_don_thuoc từ ho_so_benh_ans
        $idDonThuoc = DB::table('hoa_dons')
            ->join('ho_so_benh_ans', 'hoa_dons.id_lich_pet', '=', 'ho_so_benh_ans.id_lich_hen_pet')
            ->where('hoa_dons.id', $id)
            ->value('ho_so_benh_ans.id_don_thuoc');

        // Lấy id_dv và tien_coc từ bảng lich_hen_pets
        $lichHenInfo = DB::table('hoa_dons')
            ->join('lich_hen_pets', 'hoa_dons.id_lich_pet', '=', 'lich_hen_pets.id')
            ->where('hoa_dons.id', $id)
            ->select('lich_hen_pets.id_dv', 'lich_hen_pets.tien_coc')
            ->first();

        // Lấy giá dịch vụ từ bảng dich_vus
        $tienDichVu = 0;
        if ($lichHenInfo && $lichHenInfo->id_dv) {
            $tienDichVu = DB::table('dich_vus')
                ->where('id', $lichHenInfo->id_dv)
                ->value('gia') ?? 0;
        }

        // Tính tiền đơn thuốc nếu có
        $tienDonThuoc = 0;
        if ($idDonThuoc) {
            $tienDonThuoc = DB::table('don_thuoc_chi_tiets as ct')
                ->join('thuocs as t', 'ct.id_thuoc', '=', 't.id')
                ->where('ct.id_don_thuoc', $idDonThuoc)
                ->selectRaw('SUM(ct.so_luong * t.gia_ban) as tong')
                ->value('tong') ?? 0;
        }

        // Lấy tiền khám từ hóa đơn chi tiết (có thể có nhiều dòng, lấy tổng)
        $tienKham = DB::table('hoa_don_chi_tiets')
            ->where('id_hoadon', $id)
            ->sum('tien_kham');

        return response()->json([
            'status' => true,
            'data' => (object)[
                'tien_don_thuoc'     => $tienDonThuoc,
                'tien_dich_vu'       => $tienDichVu,
                'tien_kham'          => $tienKham,
                'tien_coc_dich_vu'   => $lichHenInfo->tien_coc ?? 0,
            ]
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

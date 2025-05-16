<?php

namespace App\Http\Controllers;

use App\Http\Requests\HoaDonRequest;
use App\Models\HoaDonChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    // Lấy danh sách hóa đơn
    public function danhSach()
    {
        $ds = DB::table('hoa_dons')
            ->leftJoin('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->leftJoin('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
            ->leftJoin('don_thuoc_chi_tiets', 'hoa_don_chi_tiets.id_ct_don_thuoc', '=', 'don_thuoc_chi_tiets.id')
            ->leftJoin('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->leftJoin('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'hoa_dons.id_nv', '=', 'nhan_viens.id')
            ->select(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'hoa_dons.tinh_trang',
                'hoa_dons.id_nv',
                'nhan_viens.ten_nv',
                'khach_hangs.ho_va_ten',
                DB::raw('COALESCE(dich_vus.gia, 0) as tien_dich_vu'),
                DB::raw('COALESCE(lich_hen_pets.tien_coc, 0) as tien_coc'),
                DB::raw('COALESCE(don_thuoc_chi_tiets.so_luong * thuocs.gia_ban, 0) as tien_thuoc')
            )
            ->groupBy(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'hoa_dons.tinh_trang',
                'hoa_dons.id_nv',
                'nhan_viens.ten_nv',
                'khach_hangs.ho_va_ten',
                'hoa_don_chi_tiets.tien_kham',
                'dich_vus.gia',
                'lich_hen_pets.tien_coc',
                'don_thuoc_chi_tiets.so_luong',
                'thuocs.gia_ban'
            )
            ->orderBy('hoa_dons.ngay_xuat_hoa_don', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $ds
        ]);
    }
    public function inHoaDon($id)
    {
        $hoaDon = DB::table('hoa_dons')
            ->leftJoin('nhan_viens', 'hoa_dons.id_nv', '=', 'nhan_viens.id')
            ->leftJoin('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->leftJoin('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('khach_hangs', '=', 'khach_hangs.id')
            ->where('hoa_dons.id', $id)
            ->select(
                'hoa_dons.id as ma_hoa_don',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'ten_nv',
                DB::raw('SUM(hoa_don_chi_tiets.tien_kham) as tong_kham')
            )
            ->groupBy(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'ten_nv',
                'ho_va_ten',
            )
            ->first();

        if (!$hoaDon) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ]);
        }

        $chiTiet = app(HoaDonController::class)->chiTietTien($id)->getData()->data;

        return response()->json([
            'status' => true,
            'data' => [
                'hoa_don' => $hoaDon,
                'chi_tiet' => $chiTiet
            ]
        ]);
    }

    public function thanhToan(HoaDonRequest $request)
    {
        $hoa_don = DB::table('hoa_dons')->where('id', $request->id)->first();

        if (!$hoa_don) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ]);
        }

        DB::beginTransaction();
        try {
            // Cập nhật bảng hoa_dons
            DB::table('hoa_dons')
                ->where('id', $request->id)
                ->update([
                    'id_nv' => $request->id_nv,
                    'phuong_thuc' => $request->phuong_thuc,
                    'tinh_trang' => 1,
                    'updated_at' => now()
                ]);

            DB::table('hoa_don_chi_tiets')
                ->where('id_hoadon', $request->id)
                ->update([
                    'tien_kham' => $request->tien_kham,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thanh toán thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Lấy chi tiết hóa đơn
    public function chiTietTien($id)
    {
        // Lấy id_lich_hen_pet từ bảng hoa_don_chi_tiets (vì hoa_dons không có cột này)
        $idLichHen = DB::table('hoa_don_chi_tiets')
            ->where('id_hoadon', $id)
            ->value('id_lich_hen_pet');

        // Lấy id_don_thuoc từ ho_so_benh_ans
        $idDonThuoc = DB::table('ho_so_benh_ans')
            ->where('id_lich_hen_pet', $idLichHen)
            ->value('id_don_thuoc');

        // Lấy id_dv và tien_coc từ bảng lich_hen_pets
        $lichHenInfo = DB::table('lich_hen_pets')
            ->where('id', $idLichHen)
            ->select('id_dv', 'tien_coc')
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

        // Lấy tiền khám từ hóa đơn chi tiết (có thể có nhiều dòng)
        $tienKham = DB::table('hoa_don_chi_tiets')
            ->where('id_hoadon', $id)
            ->sum('tien_kham') ?? 0;

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

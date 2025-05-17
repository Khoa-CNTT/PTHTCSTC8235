<?php

namespace App\Http\Controllers;

use App\Http\Requests\HoaDonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    // Lấy danh sách hóa đơn
    public function danhSach()
    {
        $ds = DB::table('hoa_dons')
            ->leftJoin('khach_hangs', 'hoa_dons.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'hoa_dons.id_nv', '=', 'nhan_viens.id')
            ->select(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'hoa_dons.tinh_trang',
                'hoa_dons.id_nv',
                'ten_nv',
                'khach_hangs.ho_va_ten',
            )
            ->groupBy(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'hoa_dons.tinh_trang',
                'hoa_dons.id_nv',
                'ten_nv',
                'khach_hangs.ho_va_ten'
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
            ->leftJoin('khach_hangs', 'hoa_dons.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('hoa_don_chi_tiets', 'hoa_dons.id', '=', 'hoa_don_chi_tiets.id_hoadon')
            ->leftJoin('lich_hen_pets', 'hoa_don_chi_tiets.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->where('hoa_dons.id', $id)
            ->select(
                'hoa_dons.id as ma_hoa_don',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'ten_nv',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'pets.ten_pet',
                DB::raw('SUM(hoa_don_chi_tiets.tien_kham) as tong_kham')
            )
            ->groupBy(
                'hoa_dons.id',
                'hoa_dons.ngay_xuat_hoa_don',
                'hoa_dons.phuong_thuc',
                'ten_nv',
                'khach_hangs.ho_va_ten',
                'pets.ten_pet'
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
        // 1. Kiểm tra hóa đơn tồn tại
        $hoaDon = DB::table('hoa_dons')->where('id', $id)->first();
        if (!$hoaDon) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        // 2. Lấy chi tiết hóa đơn
        $chiTiet = DB::table('hoa_don_chi_tiets')
            ->where('id_hoadon', $id)
            ->select('id_lich_hen_pet', 'id_ct_don_thuoc')
            ->first();

        $idLichHen = $chiTiet->id_lich_hen_pet ?? null;

        // 3. Lấy thông tin lịch hẹn (dịch vụ và tiền cọc)
        $lichHenInfo = $idLichHen
            ? DB::table('lich_hen_pets')->where('id', $idLichHen)->select('id_dv', 'tien_coc')->first()
            : null;

        // 4. Lấy giá dịch vụ
        $tienDichVu = $lichHenInfo?->id_dv
            ? DB::table('dich_vus')->where('id', $lichHenInfo->id_dv)->value('gia') ?? 0
            : 0;

        // 5. Tính tiền đơn thuốc (cách 1: từ id_ct_don_thuoc trong chi tiết hóa đơn)
        $idCtDonThuocs = DB::table('hoa_don_chi_tiets')
            ->where('id_hoadon', $id)
            ->whereNotNull('id_ct_don_thuoc')
            ->pluck('id_ct_don_thuoc')
            ->toArray();

        $tienDonThuoc = 0;
        if (!empty($idCtDonThuocs)) {
            $tienDonThuoc = DB::table('don_thuoc_chi_tiets as ct')
                ->join('thuocs as t', 'ct.id_thuoc', '=', 't.id')
                ->whereIn('ct.id', $idCtDonThuocs)
                ->selectRaw('SUM(ct.so_luong * t.gia_ban) as tong')
                ->value('tong') ?? 0;
        }

        // 6. Nếu không có, tính từ hồ sơ bệnh án (cách 2)
        if ($tienDonThuoc == 0 && $idLichHen) {
            $idDonThuoc = DB::table('ho_so_benh_ans')
                ->where('id_lich_hen_pet', $idLichHen)
                ->value('id_don_thuoc');

            if ($idDonThuoc) {
                $tienDonThuoc = DB::table('don_thuoc_chi_tiets as ct')
                    ->join('thuocs as t', 'ct.id_thuoc', '=', 't.id')
                    ->where('ct.id_don_thuoc', $idDonThuoc)
                    ->selectRaw('SUM(ct.so_luong * t.gia_ban) as tong')
                    ->value('tong') ?? 0;
            }
        }

        // 7. Tính tổng tiền khám từ chi tiết hóa đơn
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

    // Thêm mới hóa đơn
    public function them(Request $request)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            $request->validate([
                'id_kh' => 'required',
                'id_thu_cung' => 'required',
                'id_nv' => 'required',
                'tien_don_thuoc' => 'required|numeric|min:0',
                'tien_dich_vu' => 'required|numeric|min:0',
                'tien_kham' => 'required|numeric|min:0',
                'tien_coc_dich_vu' => 'required|numeric|min:0',
                'phuong_thuc' => 'required|in:0,1',
                'tinh_trang' => 'required|in:0,1'
            ]);

            DB::beginTransaction();

            // Thêm mới hóa đơn
            $idHoaDon = DB::table('hoa_dons')->insertGetId([
                'id_kh' => $request->id_kh,
                'id_nv' => $request->id_nv,
                'phuong_thuc' => $request->phuong_thuc,
                'tinh_trang' => $request->tinh_trang,
                'ngay_xuat_hoa_don' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Thêm chi tiết hóa đơn
            DB::table('hoa_don_chi_tiets')->insert([
                'id_hoadon' => $idHoaDon,
                'tien_kham' => $request->tien_kham,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Thêm hóa đơn thành công',
                'id_hoa_don' => $idHoaDon
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

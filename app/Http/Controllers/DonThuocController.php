<?php

namespace App\Http\Controllers;

use App\Models\Thuoc;
use App\Models\DonThuoc;
use App\Models\DonThuocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonThuocController extends Controller
{
    public function loadThuoc()
    {
        $thuocs = Thuoc::where('tinh_trang', 1)->get();

        return response()->json([
            'status' => true,
            'thuoc' => $thuocs,
        ]);
    }

    public function them(Request $request)
    {
        try {
            DB::beginTransaction();

            // Tạo đơn thuốc trước
            $don_thuoc = new DonThuoc();
            $don_thuoc->ngay_ke_don = now();
            $don_thuoc->save();

            // Gán đơn thuốc này vào hồ sơ bệnh án
            DB::table('ho_so_benh_ans')
                ->where('id', $request->id_hsba)
                ->update(['id_don_thuoc' => $don_thuoc->id]);

            // Lưu chi tiết đơn thuốc
            foreach ($request->chi_tiet as $item) {
                $chi_tiet = new DonThuocChiTiet();
                $chi_tiet->id_don_thuoc = $don_thuoc->id;
                $chi_tiet->id_thuoc = $item['id_thuoc'];
                $chi_tiet->so_luong = $item['so_luong'];
                $chi_tiet->lieu_luong = $item['lieu_luong'];
                $chi_tiet->tinh_trang = '1';
                $chi_tiet->save();
            }

            DB::commit();

            // Load lại đơn thuốc vừa thêm để trả về cho frontend
            $newDonThuoc = DB::table('don_thuocs')
                ->leftJoin('ho_so_benh_ans', 'don_thuocs.id', '=', 'ho_so_benh_ans.id_don_thuoc') // đổi lại join
                ->leftJoin('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->leftJoin('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
                ->leftJoin('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
                ->leftJoin('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
                ->select(
                    'don_thuocs.id',
                    'khach_hangs.ho_va_ten as ten_khach_hang',
                    'nhan_viens.ten_nv as ten_bac_si',
                    'pets.ten_pet',
                    'don_thuocs.created_at'
                )
                ->where('don_thuocs.id', $don_thuoc->id)
                ->first();

            return response()->json([
                'status' => 1,
                'message' => 'Thêm đơn thuốc thành công',
                'data' => $newDonThuoc
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Thêm đơn thuốc thất bại: ' . $e->getMessage()
            ]);
        }
    }

    public function load()
    {
        $don_thuoc = DB::table('don_thuocs')
            ->leftJoin('ho_so_benh_ans', 'ho_so_benh_ans.id_don_thuoc', '=', 'don_thuocs.id')
            ->leftJoin('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->leftJoin('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->select(
                'don_thuocs.id',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'nhan_viens.ten_nv as ten_bac_si',
                'pets.ten_pet',
                'don_thuocs.created_at'
            )
            ->orderBy('don_thuocs.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $don_thuoc
        ]);
    }

    public function xoa(Request $request)
    {
        try {
            DB::beginTransaction();

            $don_thuoc = DonThuoc::find($request->id);
            if ($don_thuoc) {
                DonThuocChiTiet::where('id_don_thuoc', $don_thuoc->id)->delete();
                $don_thuoc->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Xóa đơn thuốc thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Xóa đơn thuốc thất bại: ' . $e->getMessage()
            ]);
        }
    }

    public function getAll()
    {
        $don_thuoc = DB::table('don_thuocs')
            ->leftJoin('ho_so_benh_ans', 'ho_so_benh_ans.id_don_thuoc', '=', 'don_thuocs.id')
            ->leftJoin('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->leftJoin('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->select(
                'don_thuocs.id',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'nhan_viens.ten_nv as ten_bac_si',
                'pets.ten_pet',
                'don_thuocs.ngay_ke_don',
                'don_thuocs.created_at',
                DB::raw('(SELECT MIN(tinh_trang) FROM don_thuoc_chi_tiets WHERE id_don_thuoc = don_thuocs.id) as tinh_trang')
            )
            ->orderBy('don_thuocs.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $don_thuoc
        ]);
    }
    public function layPetsDangDieuTriTheoKhach($id_kh)
    {
        $pets = DB::table('ho_so_benh_ans')
            ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->where('lich_hen_pets.id_kh', $id_kh)
            ->where('ho_so_benh_ans.tinh_trang', 1)
            ->select(
                'pets.id as id_pet',
                'pets.ten_pet',
                'ho_so_benh_ans.id as id_hsba',
                'ho_so_benh_ans.id_nv'
            )
            ->distinct()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $pets
        ]);
    }

    public function chiTiet($id)
    {
        $chi_tiet = DB::table('don_thuoc_chi_tiets')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->where('don_thuoc_chi_tiets.id_don_thuoc', $id)
            ->select(
                'don_thuoc_chi_tiets.id_ctthuoc',
                'don_thuoc_chi_tiets.id_thuoc',
                'thuocs.ten_thuoc',
                'don_thuoc_chi_tiets.so_luong',
                'don_thuoc_chi_tiets.lieu_luong',
                'don_thuoc_chi_tiets.tinh_trang'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $chi_tiet
        ]);
    }

    public function getChiTietInDon($id)
    {
        $don_thuoc = DB::table('don_thuocs')
            ->leftJoin('ho_so_benh_ans', 'ho_so_benh_ans.id_don_thuoc', '=', 'don_thuocs.id')
            ->leftJoin('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->leftJoin('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->leftJoin('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->where('don_thuocs.id', $id)
            ->select(
                'don_thuocs.id',
                'don_thuocs.ngay_ke_don',
                'khach_hangs.ho_va_ten as ten_benh_nhan',
                'lich_hen_pets.ngay as ngay_kham',
                'ho_so_benh_ans.chuan_doan',
                'nhan_viens.ten_nv as ten_bac_si',
                'pets.ten_pet'
            )
            ->first();

        if (!$don_thuoc) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thông tin đơn thuốc.'
            ]);
        }

        $chi_tiet = DB::table('don_thuoc_chi_tiets')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->where('don_thuoc_chi_tiets.id_don_thuoc', $id)
            ->where('don_thuoc_chi_tiets.tinh_trang', '1')
            ->select(
                'thuocs.ten_thuoc',
                'don_thuoc_chi_tiets.so_luong',
                'don_thuoc_chi_tiets.lieu_luong'
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'don_thuoc' => $don_thuoc,
                'chi_tiet' => $chi_tiet
            ]
        ]);
    }
    public function toggleTinhTrang(Request $request)
    {
        try {
            $chiTiet = DonThuocChiTiet::find($request->id_ctthuoc);
            if (!$chiTiet) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy chi tiết đơn thuốc'
                ]);
            }

            // Toggle status between '1' (đang sử dụng) and '0' (dừng sử dụng)
            $chiTiet->tinh_trang = $chiTiet->tinh_trang === '1' ? '0' : '1';
            $chiTiet->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật tình trạng thành công',
                'data' => [
                    'id_ctthuoc' => $chiTiet->id_ctthuoc,
                    'tinh_trang' => $chiTiet->tinh_trang
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật tình trạng: ' . $e->getMessage()
            ]);
        }
    }
    public function loadKhachHangDangDieuTri()
    {
        try {
            $khachHang = DB::table('ho_so_benh_ans')
                ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->join('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
                ->where('ho_so_benh_ans.tinh_trang', 1)
                ->select('khach_hangs.id', 'khach_hangs.ho_va_ten')
                ->distinct()
                ->get();

            return response()->json([
                'status' => true,
                'data' => $khachHang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải danh sách khách hàng: ' . $e->getMessage()
            ]);
        }
    }
}

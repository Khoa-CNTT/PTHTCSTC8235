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

            $don_thuoc = new DonThuoc();
            $don_thuoc->id_hsba = $request->id_hsba;
            $don_thuoc->ngay_ke_don = now();
            $don_thuoc->save();

            foreach ($request->chi_tiet as $item) {
                $chi_tiet = new DonThuocChiTiet();
                $chi_tiet->id_don_thuoc = $don_thuoc->id;
                $chi_tiet->id_thuoc = $item['id_thuoc'];
                $chi_tiet->so_luong = $item['so_luong'];
                $chi_tiet->lieu_luong = $item['lieu_luong'];
                $chi_tiet->save();
            }

            DB::commit();

            // Lấy lại đơn thuốc vừa thêm
            $newDonThuoc = \DB::table('don_thuocs')
                ->leftJoin('ho_so_benh_ans', 'don_thuocs.id_hsba', '=', 'ho_so_benh_ans.id')
                ->leftJoin('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
                ->leftJoin('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
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
        $don_thuoc = \DB::table('don_thuocs')
            ->leftJoin('ho_so_benh_ans', 'don_thuocs.id_hsba', '=', 'ho_so_benh_ans.id')
            ->leftJoin('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
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
        $don_thuoc = \DB::table('don_thuocs')
            ->leftJoin('ho_so_benh_ans', 'don_thuocs.id_hsba', '=', 'ho_so_benh_ans.id')
            ->leftJoin('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
            ->leftJoin('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->leftJoin('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->select(
                'don_thuocs.id',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'nhan_viens.ten_nv as ten_bac_si',
                'pets.ten_pet',
                'don_thuocs.ngay_ke_don',
                'don_thuocs.created_at'
            )
            ->orderBy('don_thuocs.created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $don_thuoc
        ]);
    }

    public function chiTiet($id)
    {
        $chi_tiet = \DB::table('don_thuoc_chi_tiets')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->where('don_thuoc_chi_tiets.id_don_thuoc', $id)
            ->select(
                'don_thuoc_chi_tiets.id_ctthuoc',
                'don_thuoc_chi_tiets.id_thuoc',
                'thuocs.ten_thuoc',
                'don_thuoc_chi_tiets.so_luong',
                'don_thuoc_chi_tiets.lieu_luong'
            )
            ->get();
        return response()->json([
            'status' => true,
            'data' => $chi_tiet
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Thuoc;
use App\Models\DonThuoc;
use App\Models\ChiTietDonThuoc;
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
            $don_thuoc->id_khach_hang = $request->id_khach_hang;
            $don_thuoc->id_nhan_vien = $request->id_nhan_vien;
            $don_thuoc->ngay_ke = now();
            $don_thuoc->tinh_trang = 1;
            $don_thuoc->save();

            foreach ($request->chi_tiet as $item) {
                $chi_tiet = new ChiTietDonThuoc();
                $chi_tiet->id_don_thuoc = $don_thuoc->id;
                $chi_tiet->id_thuoc = $item['id_thuoc'];
                $chi_tiet->so_luong = $item['so_luong'];
                $chi_tiet->cach_dung = $item['cach_dung'];
                $chi_tiet->save();
            }

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Thêm đơn thuốc thành công'
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
        $don_thuoc = DonThuoc::with(['chiTietDonThuoc.thuoc', 'khachHang', 'nhanVien'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'don_thuoc' => $don_thuoc
        ]);
    }

    public function xoa(Request $request)
    {
        try {
            DB::beginTransaction();

            $don_thuoc = DonThuoc::find($request->id);
            if ($don_thuoc) {
                ChiTietDonThuoc::where('id_don_thuoc', $don_thuoc->id)->delete();
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
}

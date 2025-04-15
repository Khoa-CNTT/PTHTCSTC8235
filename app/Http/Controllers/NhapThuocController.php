<?php

namespace App\Http\Controllers;

use App\Http\Requests\NhapThuocRequest;
use App\Models\Kho;
use App\Models\NhaCungCap;
use App\Models\PhieuNhap;
use App\Models\PhieuNhapChiTiet;
use App\Models\Thuoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhapThuocController extends Controller
{
    public function loadKhoNCCThuoc()
    {
        return response()->json([
            'status' => true,
            'kho' => Kho::where('tinh_trang', 1)->get(),
            'ncc' => NhaCungCap::where('tinh_trang', 1)->get(),
            'thuoc' => Thuoc::where('tinh_trang', 1)->get(),
        ]);
    }

    // Tạo phiếu nhập thuốc
    public function tao(NhapThuocRequest $request)
    {
        $data = $request->validated();

        // Kiểm tra không trùng thuốc trong chi tiết
        $thuocIds = array_column($data['chi_tiet'], 'id_thuoc');
        if (count($thuocIds) !== count(array_unique($thuocIds))) {
            return response()->json([
                'status' => false,
                'message' => 'Không được chọn trùng thuốc trong phiếu!',
            ]);
        }

        DB::beginTransaction();
        try {
            // Tạo phiếu
            $phieu = PhieuNhap::create([
                'id_kho' => $data['id_kho'],
                'id_ncc' => $data['id_ncc'],
                'ngay_nhap' => $data['ngay_nhap'],
            ]);

            // Tạo chi tiết
            foreach ($data['chi_tiet'] as $item) {
                PhieuNhapChiTiet::create([
                    'id_phieu_nhap' => $phieu->id,
                    'id_thuoc' => $item['id_thuoc'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'han_su_dung' => $item['han_su_dung'],
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Lưu phiếu nhập thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Load danh sách phiếu nhập (nếu bạn cần hiển thị)
    public function load()
    {
        $data = PhieuNhap::with(['kho', 'ncc', 'chiTiet'])->orderByDesc('id')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}

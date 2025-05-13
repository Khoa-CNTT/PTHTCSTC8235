<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhieuNhapRequest;
use App\Models\Kho;
use App\Models\NhaCungCap;
use App\Models\PhanQuyen;
use App\Models\PhieuNhap;
use App\Models\PhieuNhapChiTiet;
use App\Models\Thuoc;
use App\Models\ThuocKho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuNhapController extends Controller
{
    public function loadKhovaNCCvaThuoc()
    {
        $khos = Kho::where('tinh_trang', 1)->get();
        $nccs = NhaCungCap::where('tinh_trang', 1)->get();
        $thuocs = Thuoc::where('tinh_trang', 1)->get();

        return response()->json([
            'status' => true,
            'kho' => $khos,
            'ncc' => $nccs,
            'thuoc' => $thuocs,
        ]);
    }
    public function loc(Request $request)
    {

        $query = PhieuNhap::with(['kho', 'ncc', 'chiTiet']);

        if ($request->filled('tu_ngay')) {
            $query->whereDate('ngay_nhap', '>=', $request->tu_ngay);
        }

        if ($request->filled('den_ngay')) {
            $query->whereDate('ngay_nhap', '<=', $request->den_ngay);
        }

        $ds = $query->orderByDesc('id')->get();

        return response()->json([
            'status' => true,
            'data' => $ds
        ]);
    }

    public function load()
    {
        $phieuNhaps = PhieuNhap::with(['kho', 'ncc', 'chiTiet'])->orderByDesc('id')->get();

        return response()->json([
            'status' => 1,
            'data' => $phieuNhaps,
        ]);
    }
    public function delete(Request $request)
    {

        $id = $request->id;

        try {
            // Xoá chi tiết thuốc trước
            PhieuNhapChiTiet::where('id_phieu_nhap', $id)->delete();

            // Xoá phiếu nhập
            PhieuNhap::where('id', $id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Đã xoá phiếu nhập thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xoá: ' . $e->getMessage(),
            ]);
        }
    }
    public function update(Request $request)
    {

        $data = $request->all();

        foreach ($data['chi_tiet'] as $chiTietData) {
            $chiTiet = PhieuNhapChiTiet::find($chiTietData['id']);
            $thuocKho = ThuocKho::find($chiTietData['id']);
            if ($chiTiet) {
                $chiTiet->update($chiTietData);
                $thuocKho->update($chiTietData);
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật chi tiết phiếu nhập thành công'
        ]);
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';

        $data = PhieuNhap::join('khos', 'phieu_nhaps.id_kho', '=', 'khos.id')
            ->join('nha_cung_caps', 'phieu_nhaps.id_ncc', '=', 'nha_cung_caps.id')
            ->where('khos.ten_kho', 'like', $noi_dung)
            ->orWhere('nha_cung_caps.ten_ncc', 'like', $noi_dung)
            ->with(['kho', 'ncc', 'chiTiet'])
            ->orderByDesc('phieu_nhaps.id')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function tao(PhieuNhapRequest $request)
    {
        try {
            DB::beginTransaction();

            // Tạo phiếu nhập (mặc định da_nhap_kho = false)
            $phieu = PhieuNhap::create([
                'id_kho' => $request->id_kho,
                'id_ncc' => $request->id_ncc,
                'ngay_nhap' => $request->ngay_nhap,
                'da_nhap_kho' => false,
            ]);

            // Lưu từng dòng chi tiết
            foreach ($request->chi_tiet as $ct) {
                PhieuNhapChiTiet::create([
                    'id_phieu_nhap' => $phieu->id,
                    'id_thuoc' => $ct['id_thuoc'],
                    'so_luong' => $ct['so_luong'],
                    'gia_nhap' => $ct['gia_nhap'],
                    'gia_ban' => $ct['gia_ban'],
                    'han_su_dung' => $ct['han_su_dung'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Tạo phiếu nhập thành công!',
                'id_phieunhap' => $phieu->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo phiếu nhập: ' . $e->getMessage()
            ], 500);
        }
    }
    public function nhapKho(Request $request)
    {
        $phieu = PhieuNhap::find($request->id);

        if (!$phieu || $phieu->da_nhap_kho) {
            return response()->json([
                'status' => false,
                'message' => !$phieu ? 'Phiếu nhập không tồn tại' : 'Phiếu đã được nhập kho'
            ]);
        }

        $chiTiet = PhieuNhapChiTiet::where('id_phieu_nhap', $phieu->id)->get();

        $dataThuocKho = [];

        foreach ($chiTiet as $ct) {
            $dataThuocKho[] = [
                'id_kho' => $phieu->id_kho,
                'id_thuoc' => $ct->id_thuoc,
                'gia_nhap' => $ct->gia_nhap,
                'gia_ban' => $ct->gia_ban,
                'so_luong_ton_kho' => $ct->so_luong,
                'han_su_dung' => $ct->han_su_dung,
                'id_phieu_nhap_CT' => $ct->id,
                'tinh_trang' => 1,
            ];
            // lấy giá bán cao nhất
            $thuoc = Thuoc::find($ct->id_thuoc);
            if ($thuoc) {
                $giaBanMoi = $ct->gia_ban;

                // Nếu giá hiện tại chưa có hoặc thấp hơn thì cập nhật
                if (is_null($thuoc->gia_ban) || $thuoc->gia_ban < $giaBanMoi) {
                    $thuoc->gia_ban = $giaBanMoi;
                    $thuoc->save();
                }
            }
        }

        ThuocKho::insert($dataThuocKho);

        $phieu->update(['da_nhap_kho' => true]);

        return response()->json(['status' => true, 'message' => 'Đã nhập kho thành công']);
    }
}

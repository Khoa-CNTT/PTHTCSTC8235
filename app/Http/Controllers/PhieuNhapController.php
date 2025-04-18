<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhieuNhapRequest;
use App\Models\Kho;
use App\Models\NhaCungCap;
use App\Models\PhieuNhap;
use App\Models\PhieuNhapChiTiet;
use App\Models\Thuoc;
use App\Models\ThuocKho;
use Illuminate\Http\Request;
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
            'status' => true,
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

        $phieu = PhieuNhap::find($data['id']);
        if (!$phieu) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phiếu nhập!'
            ]);
        }

        DB::beginTransaction();
        try {
            // Xóa chi tiết cũ
            PhieuNhapChiTiet::where('id_phieu_nhap', $phieu->id)->delete();

            // Ghi lại chi tiết mới
            foreach ($data['chi_tiet'] as $item) {
                PhieuNhapChiTiet::create([
                    'id_phieu_nhap' => $phieu->id,
                    'id_thuoc' => $item['id_thuoc'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'han_su_dung' => $item['han_su_dung'],
                ]);
            }

            // Cập nhật lại tồn kho bằng cách tổng hợp từ chi tiết
            foreach ($data['chi_tiet'] as $item) {
                $tong_so_luong = DB::table('phieu_nhap_chi_tiets')
                    ->join('phieu_nhaps', 'phieu_nhap_chi_tiets.id_phieu_nhap', '=', 'phieu_nhaps.id')
                    ->where('phieu_nhaps.id_kho', $phieu->id_kho)
                    ->where('phieu_nhap_chi_tiets.id_thuoc', $item['id_thuoc'])
                    ->sum('phieu_nhap_chi_tiets.so_luong');

                DB::table('thuoc_khos')->updateOrInsert(
                    [
                        'id_kho' => $phieu->id_kho,
                        'id_thuoc' => $item['id_thuoc'],
                    ],
                    [
                        'so_luong_ton_kho' => $tong_so_luong,
                        'gia_nhap' => $item['gia_nhap'],
                        'han_su_dung' => $item['han_su_dung'],
                        'ngay_nhap' => now(),
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật phiếu nhập thành công!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()
            ]);
        }
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
        $data = $request->validated();

        // Kiểm tra trùng thuốc trong chi tiết
        $thuocIds = array_column($data['chi_tiet'], 'id_thuoc');
        if (count($thuocIds) !== count(array_unique($thuocIds))) {
            return response()->json([
                'status' => false,
                'message' => 'Không được chọn trùng thuốc trong phiếu!',
            ]);
        }

        DB::beginTransaction();
        try {
            // Tạo phiếu nhập
            $phieu = PhieuNhap::create([
                'id_kho' => $data['id_kho'],
                'id_ncc' => $data['id_ncc'],
                'ngay_nhap' => $data['ngay_nhap'],
            ]);

            // Duyệt chi tiết và cập nhật bảng tồn kho
            foreach ($data['chi_tiet'] as $item) {
                // Lưu chi tiết phiếu nhập
                PhieuNhapChiTiet::create([
                    'id_phieu_nhap' => $phieu->id,
                    'id_thuoc' => $item['id_thuoc'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'han_su_dung' => $item['han_su_dung'],
                ]);

                // Cập nhật hoặc thêm mới trong bảng tồn kho
                $tonKho = ThuocKho::where([
                    ['id_kho', '=', $data['id_kho']],
                    ['id_thuoc', '=', $item['id_thuoc']],
                    ['gia_nhap', '=', $item['gia_nhap']],
                    ['han_su_dung', '=', $item['han_su_dung']],
                ])->first();

                if ($tonKho) {
                    $tonKho->so_luong_ton_kho += $item['so_luong'];
                    $tonKho->save();
                } else {
                    ThuocKho::create([
                        'id_kho' => $data['id_kho'],
                        'id_thuoc' => $item['id_thuoc'],
                        'gia_nhap' => $item['gia_nhap'],
                        'so_luong_ton_kho' => $item['so_luong'],
                        'han_su_dung' => $item['han_su_dung'],
                        'ngay_nhap' => $data['ngay_nhap'],
                    ]);
                }
                // 5. Tính tổng tồn kho tất cả lô thuốc của thuốc đó trong kho
                $tongSoLuong = ThuocKho::where([
                    ['id_kho', '=', $data['id_kho']],
                    ['id_thuoc', '=', $item['id_thuoc']],
                ])->sum('so_luong_ton_kho');

                // 6. Nếu tổng tồn kho bằng chính lô đang nhập → cập nhật giá bán
                if ($tongSoLuong == $item['so_luong']) {
                    DB::table('thuocs')
                        ->where('id', $item['id_thuoc'])
                        ->update([
                            'gia_ban' => round($item['gia_nhap'] * 1.2)
                        ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Lưu phiếu nhập và cập nhật tồn kho thành công!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\HoSoBenhAn;
use App\Models\ThuCung;
use App\Models\KhachHang;
use App\Models\DonThuoc;
use App\Models\DonThuocChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoSoBenhAnController extends Controller
{
    public function load()
    {
        $ho_so_benh_an = DB::table('ho_so_benh_ans')
            ->join('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
            ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->select(
                'ho_so_benh_ans.*',
                'pets.ten_pet as ten_thu_cung',
                'pets.id_kh',
                'khach_hangs.ho_va_ten as ten_chu',
                'khach_hangs.so_dien_thoai as sdt',
                'nhan_viens.ten_nv as ten_bac_si'
            )
            ->orderBy('ho_so_benh_ans.ngay_kham', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $ho_so_benh_an
        ]);
    }

    public function chiTiet($id)
    {
        $ho_so = DB::table('ho_so_benh_ans')
            ->join('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
            ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->where('ho_so_benh_ans.id', $id)
            ->select(
                'ho_so_benh_ans.*',
                'pets.ten_pet as ten_thu_cung',
                'pets.tuoi',
                'pets.can_nang',
                'pets.chung_loai',
                'pets.gioi_tinh as gioi_tinh_pet',
                'khach_hangs.ho_va_ten as ten_chu',
                'nhan_viens.ten_nv as ten_bac_si'
            )
            ->first();

        // Lấy danh sách thuốc của hồ sơ này (nếu có đơn thuốc)
        $thuoc = DB::table('don_thuocs')
            ->join('don_thuoc_chi_tiets', 'don_thuocs.id', '=', 'don_thuoc_chi_tiets.id_don_thuoc')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->where('don_thuocs.id_hsba', $id)
            ->where('don_thuoc_chi_tiets.tinh_trang', '1') // Chỉ lấy thuốc đang sử dụng
            ->select(
                'thuocs.ten_thuoc',
                'don_thuoc_chi_tiets.so_luong',
                'don_thuoc_chi_tiets.lieu_luong'
            )
            ->get();

        return response()->json([
            'status' => true,
            'ho_so' => $ho_so,
            'thuoc' => $thuoc
        ]);
    }


    public function update(Request $request)
    {
        try {
            $ho_so_benh_an = HoSoBenhAn::find($request->id);
            if (!$ho_so_benh_an) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hồ sơ bệnh án'
                ]);
            }

            // Nếu chỉ cập nhật tình trạng
            if ($request->has('tinh_trang') && !$request->has('chuan_doan')) {
                $ho_so_benh_an->tinh_trang = $request->tinh_trang;
            } else {
                // Cập nhật đầy đủ thông tin
                $ho_so_benh_an->chuan_doan = $request->chuan_doan;
                $ho_so_benh_an->tinh_trang = $request->tinh_trang;
            }
            
            $ho_so_benh_an->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật hồ sơ bệnh án thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $ho_so_benh_an = HoSoBenhAn::find($request->id);
            if (!$ho_so_benh_an) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hồ sơ bệnh án'
                ]);
            }

            // Delete related prescriptions and their details
            $don_thuocs = DonThuoc::where('id_hsba', $ho_so_benh_an->id)->get();
            foreach ($don_thuocs as $don_thuoc) {
                DonThuocChiTiet::where('id_don_thuoc', $don_thuoc->id)->delete();
                $don_thuoc->delete();
            }

            // Delete the medical record
            $ho_so_benh_an->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Xóa hồ sơ bệnh án và các đơn thuốc liên quan thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function timkiem(Request $request)
    {
        try {
            $ho_so_benh_an = DB::table('ho_so_benh_ans')
                ->join('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
                ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
                ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
                ->where(function($query) use ($request) {
                    $query->where('pets.ten_pet', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('ho_so_benh_ans.chuan_doan', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.ho_va_ten', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.so_dien_thoai', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('nhan_viens.ten_nv', 'like', '%' . $request->noi_dung . '%');
                })
                ->select(
                    'ho_so_benh_ans.*',
                    'pets.ten_pet as ten_thu_cung',
                    'khach_hangs.ho_va_ten as ten_chu',
                    'khach_hangs.so_dien_thoai as sdt',
                    'nhan_viens.ten_nv as ten_bac_si'
                )
                ->orderBy('ho_so_benh_ans.ngay_kham', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $ho_so_benh_an
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function locTheoBacSi($id)
    {
        try {
            $ho_so_benh_an = DB::table('ho_so_benh_ans')
                ->join('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
                ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
                ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
                ->where('ho_so_benh_ans.id_nv', $id)
                ->select(
                    'ho_so_benh_ans.*',
                    'pets.ten_pet as ten_thu_cung',
                    'khach_hangs.ho_va_ten as ten_chu',
                    'khach_hangs.so_dien_thoai as sdt',
                    'nhan_viens.ten_nv as ten_bac_si'
                )
                ->orderBy('ho_so_benh_ans.ngay_kham', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $ho_so_benh_an
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}

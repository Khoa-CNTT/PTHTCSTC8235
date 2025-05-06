<?php

namespace App\Http\Controllers;

use App\Models\HoSoBenhAn;
use App\Models\ThuCung;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoSoBenhAnController extends Controller
{
    public function load()
    {
        $ho_so_benh_an = DB::table('ho_so_benh_ans')
            ->join('pets', 'ho_so_benh_ans.id_pet', '=', 'pets.id')
            ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->select(
                'ho_so_benh_ans.*',
                'pets.ten_pet as ten_thu_cung',
                'khach_hangs.ho_va_ten as ten_chu',
                'khach_hangs.so_dien_thoai as sdt'
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

    public function them(Request $request)
    {
        try {
            // Kiểm tra số điện thoại khách hàng
            $khach_hang = KhachHang::where('so_dien_thoai', $request->sdt)->first();
            if (!$khach_hang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy khách hàng với số điện thoại này'
                ]);
            }

            // Lấy thú cưng của khách hàng
            $thu_cung = ThuCung::where('id_kh', $khach_hang->id)->first();
            if (!$thu_cung) {
                return response()->json([
                    'status' => false,
                    'message' => 'Khách hàng này chưa có thú cưng nào'
                ]);
            }

            // Tạo hồ sơ bệnh án mới
            $ho_so_benh_an = new HoSoBenhAn();
            $ho_so_benh_an->id_pet = $thu_cung->id;
            $ho_so_benh_an->id_nv = auth()->id(); // Lấy ID nhân viên đang đăng nhập
            $ho_so_benh_an->ngay_kham = now();
            $ho_so_benh_an->chuan_doan = $request->chuan_doan;
            $ho_so_benh_an->tinh_trang = $request->tinh_trang;
            $ho_so_benh_an->save();

            return response()->json([
                'status' => true,
                'message' => 'Thêm hồ sơ bệnh án thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
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
            $ho_so_benh_an = HoSoBenhAn::find($request->id);
            if (!$ho_so_benh_an) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hồ sơ bệnh án'
                ]);
            }

            $ho_so_benh_an->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa hồ sơ bệnh án thành công'
            ]);
        } catch (\Exception $e) {
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
                ->where(function($query) use ($request) {
                    $query->where('pets.ten_pet', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('ho_so_benh_ans.chuan_doan', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.ho_va_ten', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.so_dien_thoai', 'like', '%' . $request->noi_dung . '%');
                })
                ->select(
                    'ho_so_benh_ans.*',
                    'pets.ten_pet as ten_thu_cung',
                    'khach_hangs.ho_va_ten as ten_chu',
                    'khach_hangs.so_dien_thoai as sdt'
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

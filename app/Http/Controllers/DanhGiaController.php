<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use Illuminate\Http\Request;

class DanhGiaController extends Controller
{
    public function load()
    {
        $data = DanhGia::join('khach_hangs', 'khach_hangs.id', '=', 'danh_gias.id_kh')
            ->select('danh_gias.*', 'khach_hangs.ho_va_ten')
            ->orderBy('danh_gias.ngay_tao', 'desc') 
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function load2()
    {
        $data = DanhGia::join('khach_hangs', 'khach_hangs.id', '=', 'danh_gias.id_kh')
            ->select('danh_gias.*', 'khach_hangs.ho_va_ten')
            ->where('danh_gias.tinh_trang', 1)
            ->orderBy('danh_gias.ngay_tao', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function timkiem(Request $request)
    {
        $keyword = '%' . $request->noi_dung . '%';

        $data = DanhGia::join('khach_hangs', 'khach_hangs.id', '=', 'danh_gias.id_kh')
            ->where(function ($query) use ($keyword) {
                $query->where('danh_gias.noi_dung', 'like', $keyword)
                    ->orWhere('khach_hangs.ho_va_ten', 'like', $keyword);
            })
            ->select('danh_gias.*', 'khach_hangs.ho_va_ten')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function doi(Request $request)
    {
        $data = DanhGia::find($request->id);
        if ($data) {
            $data->tinh_trang = $data->tinh_trang ? 0 : 1;
            $data->save();

            return response()->json([
                "status" => true,
                "message" => "Đổi trạng thái thành công"
            ]);
        }

        return response()->json(["status" => false, "message" => "Không tìm thấy đánh giá"]);
    }

    public function delete(Request $request)
    {
        $check = DanhGia::find($request->id);
        if ($check) {
            $check->delete();
            return response()->json([
                "status" => true,
                "message" => "Xóa thành công"
            ]);
        }

        return response()->json(["status" => false, "message" => "Không tìm thấy đánh giá"]);
    }
    public function them(Request $request)
    {
        $data = $request->all();
        DanhGia::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới đánh giá thành công",
        ]);
    }
    public function them2(Request $request)
    {
        $data = $request->only(['id_kh', 'noi_dung', 'ngay_tao']);
        $data['tinh_trang'] = 1;

        if (empty($data['id_kh'])) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để gửi đánh giá',
            ], 400); // Bad Request
        }

        DanhGia::create($data);

        return response()->json([
            'status' => 1,
            'message' => 'Thêm mới đánh giá thành công',
        ]);
    }
}

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
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';

        $data = DanhGia::join('khach_hangs', 'khach_hangs.id', '=', 'danh_gias.id_kh')
            ->where('danh_gias.noi_dung', 'like', $noi_dung)
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
}

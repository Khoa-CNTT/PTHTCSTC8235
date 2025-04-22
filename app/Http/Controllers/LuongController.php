<?php

namespace App\Http\Controllers;

use App\Models\Luong;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LuongController extends Controller
{
    private $id_chuc_nang = 10;
    public function Load()
    {
        $data = Luong::get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function LoadLuong()
    {
        $data = Luong::join('nhan_viens', 'luongs.id_nv', '=', 'nhan_viens.id')
            ->select('luongs.*', 'nhan_viens.ten_nv')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function Doitt(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = Luong::find($request->id);
            if ($data->tinh_trang == 0) {
                $data->tinh_trang = 1;
                $data->save();
            } else {
                $data->tinh_trang = 0;
                $data->save();
            }
            return response()->json([
                'status' => 1,
                'message' => 'Sửa đổi trạng thái thành công'
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền thêm lương",
            ]);
        }
    }
    public function Them(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            Luong::create($request->all());
            return response()->json([
                'status' => 1,
                'message' => 'Thêm mới lương thành công'
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền thêm lương",
            ]);
        }


    }
    public function TimKiem(Request $request)
    {
        $content = '%' . $request->noi_dung . '%';
        $data = Luong::where('ten_nv', 'like', $content)->get();
        return response()->json([
            'data' => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhaCungCapController extends Controller
{
    private $id_chuc_nang = 9;
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = NhaCungCap::where('ten_ncc', 'like', $noi_dung)
            ->orwhere('email', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = $request->all();
            NhaCungCap::create($data);
            return response()->json([
                'status' => '1',
                "message" => "Thêm mới nhà cung cấp thành công",
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền thêm nhà cung cấp",
            ]);
        }
    }
    public function load()
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = NhaCungCap::get();
            return response()->json([
                "data" => $data
            ]);
        } else {
            return response()->json([
                "message" => 'Bạn không có truy cập vào chức năng này',
            ]);
        }
    }
    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = $request->all();
            NhaCungCap::find($request->id)->update($data);
            return response()->json([
                "status" => '1',
                "message" => "cập nhật nhà cung cấp thành công"
            ]);
        } else {
            return response()->json([
                "message" => 'Bạn không có quyền cập nhật nhà cung cấp',
            ]);
        }
    }
    public function doi(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = NhaCungCap::find($request->id);
            if ($data->tinh_trang == 1) {
                $data->tinh_trang = 0;
                $data->save();
            } else {
                $data->tinh_trang = 1;
                $data->save();
            }
            return response()->json([
                "status" => '1',
                "message" => "Đổi trạng thái nhà cung cấp thành công"
            ]);
        } else {
            return response()->json([
                "message" => 'Bạn không có quyền đổi trạng thái nhà cung cấp',
            ]);
        }
    }
    public function delete(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            NhaCungCap::where('id', $request->id)->delete();
            return response()->json([
                "status" => '1',
                "message" => "Xóa nhà cung cấp thành công"
            ]);
        } else {
            return response()->json([
                "message" => 'Bạn không có quyền xóa nhà cung cấp',
            ]);
        }
    }
}

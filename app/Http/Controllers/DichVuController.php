<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DichVuController extends Controller
{
    private $id_chuc_nang = 4;
    public function them(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = $request->all();
            DichVu::create($data);
            return response()->json([
                'status' => '1',
                "message" => "Thêm mới dịch vụ thành công",
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền thêm dich vụ",
            ]);
        }
    }

    public function loadTiemChung()
    {
        $dichVus = DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
            ->where('dich_vus.id_loaidv', 1)
            ->select('dich_vus.*', 'loai_dich_vus.ten_loaidv')
            ->get();

        return response()->json([
            'data' => $dichVus
        ]);
    }

    public function loadChamSoc()
    {
        $dichVus = DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
            ->where('dich_vus.id_loaidv', 2)
            ->select('dich_vus.*', 'loai_dich_vus.ten_loaidv')
            ->get();

        return response()->json([
            'data' => $dichVus
        ]);
    }

    public function load()
    {
        $dichVus = DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
            ->select('dich_vus.*', 'loai_dich_vus.ten_loaidv')
            ->get();

        return response()->json([
            'data' => $dichVus
        ]);
    }
    public function LoadDataChiTiet($id)
    {
        $data = DichVu::where('id', $id)
            ->first();
        return response()->json([
            'data' => $data
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $this->id_chuc_nang)
            ->first();
        if ($check) {
            $data = $request->all();
            DichVu::find($request->id)->update($data);

            return response()->json([
                "status" => '1',
                "message" => "Cập nhật dịch vụ thành công"
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền cập nhật dich vụ",
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
            $data = DichVu::find($request->id);
            if ($data->tinh_trang == 1) {
                $data->tinh_trang = 0;
                $data->save();
            } else {
                $data->tinh_trang = 1;
                $data->save();
            }
            return response()->json([
                "status" => '1',
                "message" => "Đổi trạng thái dịch vụ thành công"
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền đổi trạng thái dịch vụ",
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
            DichVu::where('id', $request->id)->delete();
            return response()->json([
                "status" => '1',
                "message" => "Xóa dịch vụ thành công"
            ]);
        } else {
            return response()->json([
                'status' => '0',
                "message" => "Bạn không có quyền xóa dịch vụ",
            ]);
        }
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = DichVu::where('ten_dv', 'like', $noi_dung)
            ->orwhere('mo_ta', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\NhanVien;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DichVuController extends Controller
{

    public function them(Request $request)
    {
            $data = $request->all();
            DichVu::create($data);
            return response()->json([
                'status' => '1',
                "message" => "Thêm mới dịch vụ thành công",
            ]);
    }

    public function loadTiemChung()
    {
        $this->kiemTraChucNang(4);
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
        $this->kiemTraChucNang(4);
        $dichVus = DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
            ->where('dich_vus.id_loaidv', 2)
            ->select('dich_vus.*', 'loai_dich_vus.ten_loaidv')
            ->get();

        return response()->json([
            'data' => $dichVus
        ]);
    }

    public function loadBacSi()
{
    $this->kiemTraChucNang(4);
    $bacSiList = NhanVien::join('chuc_vus', 'chuc_vus.id', '=', 'nhan_viens.id_chucvu')
        ->where('chuc_vus.ten_chuc_vu', 'Bác sĩ')
        ->select('nhan_viens.*', 'chuc_vus.ten_chuc_vu')
        ->get();

    return response()->json([
        'data' => $bacSiList
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

            $data = $request->all();
            DichVu::find($request->id)->update($data);

            return response()->json([
                "status" => '1',
                "message" => "Cập nhật dịch vụ thành công"
            ]);
    }
    public function doi(Request $request)
    {

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
    }
    public function delete(Request $request)
    {
            DichVu::where('id', $request->id)->delete();
            return response()->json([
                "status" => '1',
                "message" => "Xóa dịch vụ thành công"
            ]);
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = DichVu::where('ten_dv', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
}

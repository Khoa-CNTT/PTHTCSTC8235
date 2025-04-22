<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    private $id_chuc_nang = 18;
    public function load_chuc_nang(){
        $data = ChucNang::get();
        return response()->json([
            'data'=> $data,
        ]);
    }
    public function load_cap_quyen(Request $request)
    {
        $data = PhanQuyen::join('chuc_vus', 'chuc_vus.id', 'id_chuc_vu')
            ->join('chuc_nangs', 'chuc_nangs.id', "id_chuc_nang")
            ->where('id_chuc_vu', $request->id)
            ->select('phan_quyens.*', 'chuc_vus.ten_chuc_vu', 'chuc_nangs.ten_chuc_nang')
            ->get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function cap_quyen(Request $request){
        $check = PhanQuyen::where('id_chuc_vu', $request->id_chuc_vu)
            ->where('id_chuc_nang', $request->id_chuc_nang)
            ->first();
        if ($check) {
            return response()->json([
                "status" => '0',
                "message" => "Chức vụ đã tồn tại quyền này",
            ]);
        } else {
            PhanQuyen::create($request->all());
            return response()->json([
                "status" => '1',
                "message" => "Thêm quyền thành công!",
            ]);
        }
    }
    public function xoa(Request $request){
        PhanQuyen::where('id',$request->id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Xóa quyền thành công'
        ]);
    }
    public function tim_kiem_cn(Request $request){
        $noi_dung='%'.$request->noi_dung.'%';
        $data= ChucNang::where('ten_chuc_nang','like',$noi_dung)
                        ->get();

        return response()->json([
            'data' => $data
        ]);
    }
}

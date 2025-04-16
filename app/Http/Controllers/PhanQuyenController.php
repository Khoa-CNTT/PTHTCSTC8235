<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    public function load_chuc_nang(){
        $data = ChucNang::get();
        return response()->json([
            'data'=> $data ,
        ]);
    }
    public function cap_quyen(Request $request){
        PhanQuyen::create($request ->all());
        return response()->json([
            'status' => 1,
            'message' => 'Cấp quyền thành công'
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

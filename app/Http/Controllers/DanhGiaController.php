<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use Illuminate\Http\Request;

class DanhGiaController extends Controller
{
    public function timkiem(Request $request){
        $noi_dung='%'.$request->noi_dung.'%';
        $data= DanhGia::where('noi_dung','like',$noi_dung)
                        ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function load(){
        $data=DanhGia::join('khach_hangs','khach_hangs.id','danh_gias.id_kh')
                    ->select('danh_gias.*','khach_hangs.ho_va_ten')
                    ->get();
        return response()->json([
            "data"=> $data
        ]);
    }

    public function doi(Request $request){
        $data = DanhGia ::find($request->id);
        if($data->tinh_trang==1){
            $data->tinh_trang=0;
            $data->save();
        }else{
            $data->tinh_trang=1;
            $data->save();
        }
        return response()->json([
            "status" =>'1',
            "message" =>"Đổi trạng thái thành công"
        ]);
    }
    public function delete(Request $request){
        DanhGia::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa thành công"
        ]);
    }
}

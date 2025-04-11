<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;

class NhanVienController extends Controller
{
    public function timkiem(Request $request){
        $noi_dung='%'.$request->noi_dung.'%';
        $data= NhanVien::where('ten_nv','like',$noi_dung)
                        ->orwhere('email','like',$noi_dung)
                        ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request){
        $data = $request->all();
        NhanVien::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới thành công",
        ]);
    }
    public function load(){
        $data=NhanVien::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(Request $request){
        $data = $request->all();
        NhanVien ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật nhân viên thành công"
        ]);
    }
    public function doi(Request $request){
        $data = NhanVien ::find($request->id);
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
        NhanVien::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa thành công"
        ]);
    }
}

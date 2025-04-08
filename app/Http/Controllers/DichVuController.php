<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use Illuminate\Http\Request;

class DichVuController extends Controller
{
    public function them(Request $request){
        $data = $request->all();
        DichVu::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới dịch vụ thành công",
        ]);
    }
    public function load(){
        $data=DichVu::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(Request $request){
        $data = $request->all();
        DichVu ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật dịch vụ thành công"
        ]);
    }
    public function doi(Request $request){
        $data = DichVu ::find($request->id);
        if($data->tinh_trang==1){
            $data->tinh_trang=0;
            $data->save();
        }else{
            $data->tinh_trang=1;
            $data->save();
        }
        return response()->json([
            "status" =>'1',
            "message" =>"Đổi trạng thái dịch vụ thành công"
        ]);
    }
    public function delete(Request $request){
        DichVu::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa dịch vụ thành công"
        ]);
    }
}

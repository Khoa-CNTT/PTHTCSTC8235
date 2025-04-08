<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function them(Request $request){
        $data = $request->all();
        NhaCungCap::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới thành công",
        ]);
    }
    public function load(){
        $data=NhaCungCap::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(Request $request){
        $data = $request->all();
        NhaCungCap ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật dịch vụ thành công"
        ]);
    }
    public function doi(Request $request){
        $data = NhaCungCap ::find($request->id);
        if($data->tinh_trang==1){
            $data->tinh_trang=0;
            $data->save();
        }else{
            $data->tinh_trang=1;
            $data->save();
        }
        return response()->json([
            "status" =>'1',
            "message" =>"Đổi trạng tháithành công"
        ]);
    }
    public function delete(Request $request){
        NhaCungCap::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa thành công"
        ]);
    }
}

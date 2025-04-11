<?php

namespace App\Http\Controllers;

use App\Models\Kho;
use Illuminate\Http\Request;

class KhoController extends Controller
{
    public function them(Request $request){
        $data = $request->all();
        Kho::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới kho thành công",
        ]);
    }
    public function load(){
        $data=Kho::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(Request $request){
        $data = $request->all();
        Kho ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật kho thành công"
        ]);
    }
    public function doi(Request $request){
        $data = Kho ::find($request->id);
        if($data->tinh_trang==1){
            $data->tinh_trang=0;
            $data->save();
        }else{
            $data->tinh_trang=1;
            $data->save();
        }
        return response()->json([
            "status" =>'1',
            "message" =>"Đổi trạng thái kho thành công"
        ]);
    }
    public function delete(Request $request){
        Kho::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa kho thành công"
        ]);
    }
}

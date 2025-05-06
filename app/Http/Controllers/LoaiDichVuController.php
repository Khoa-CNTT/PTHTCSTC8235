<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatLoaiDichVuRequest;
use App\Http\Requests\ThemLoaiDichVuRequest;
use App\Models\LoaiDichVu;
use Illuminate\Http\Request;

class LoaiDichVuController extends Controller
{
    public function them(ThemLoaiDichVuRequest $request){
        $data = $request->all();
        LoaiDichVu::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới loại dịch vụ thành công",
        ]);
    }
    public function load(){
        $data=LoaiDichVu::get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function update(CapNhatLoaiDichVuRequest $request){
        $data = $request->all();
        LoaiDichVu ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật loại dịch vụ thành công"
        ]);
    }
    public function delete(Request $request){
        LoaiDichVu::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa loại dịch vụ thành công"
        ]);
    }
}

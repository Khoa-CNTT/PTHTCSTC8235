<?php

namespace App\Http\Controllers;

use App\Models\Thuoc;
use Illuminate\Http\Request;

class ThuocController extends Controller
{
    public function timkiem(Request $request){
        $noi_dung='%'.$request->noi_dung.'%';
        $data= Thuoc::where('ten_thuoc','like',$noi_dung)
                        ->orwhere('mo_ta','like',$noi_dung)
                        ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request){
        $data = $request->all();
        Thuoc::create($data);
        return response()->json([
            'status'=>'1',
            "message"=>"Thêm mới thuốc thành công",
        ]);
    }
    public function load(){
        $data=Thuoc::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(Request $request){
        $data = $request->all();
        Thuoc ::find($request->id)->update($data);

        return response()->json([
            "status" =>'1',
            "message" =>"Cập nhật thuốc thành công"
        ]);
    }
    public function doi(Request $request){
        $data = Thuoc ::find($request->id);
        if($data->tinh_trang==1){
            $data->tinh_trang=0;
            $data->save();
        }else{
            $data->tinh_trang=1;
            $data->save();
        }
        return response()->json([
            "status" =>'1',
            "message" =>"Đổi trạng thái thuốc thành công"
        ]);
    }
    public function delete(Request $request){
        Thuoc::where('id',$request->id)->delete();
        return response()->json([
            "status" =>'1',
            "message" =>"Xóa thông tin thuốc thành công"
        ]);
    }
}

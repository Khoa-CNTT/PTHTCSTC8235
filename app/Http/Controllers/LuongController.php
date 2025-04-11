<?php

namespace App\Http\Controllers;

use App\Models\Luong;
use Illuminate\Http\Request;

class LuongController extends Controller
{
    public function Load()
    {
        $data = Luong::get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function Doitt(Request $request)
    {
        $data = Luong::find($request->id);
        if ($data->tinh_trang == 0) {
            $data->tinh_trang = 1;
            $data->save();
        } else {
            $data->tinh_trang = 0;
            $data->save();
        }
        return response()->json([
            'status' => 1,
            'message' => 'Sửa đổi trạng thái thành công'
        ]);
    }
    public function Them(Request $request){
        Luong::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Thêm mới Luong thành công'
        ]);
    }
    public function TimKiem(Request $request){
        $content = '%'.$request->noi_dung.'%';
        $data= Luong::where('ten_nv', 'like', $content)->get();
        return response()->json([
            'data' => $data
        ]);
    }
}

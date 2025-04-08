<?php

namespace App\Http\Controllers;

use App\Models\pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function Load()
    {
        $data = pet::get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function Doitt(Request $request)
    {
        $data = pet::find($request->id);
        if ($data->tinh_trang == 1) {
            $data->tinh_trang = 0;
            $data->save();
        } else {
            $data->tinh_trang = 1;
            $data->save();
        }
        return response()->json([
            'status' => 1,
            'message' => 'Sửa đổi trạng thái thành công'
        ]);
    }
    public function Xoa(Request $request)
    {
        pet::where('id', $request->id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Xoá pet thành công'
        ]);
    }
    public function Sua(Request $request)
    {
        pet::find($request->id)->update($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Sửa thông tin pet thành công :3'
        ]);
    }
}

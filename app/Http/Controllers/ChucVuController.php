<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    public function load_chuc_vu(){
        $data = ChucVu::where('tinh_trang',1)
                        ->get();
        return response()->json([
            'data'=> $data ,
        ]);
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = ChucVu::where('ten_chuc_vu', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request)
    {
        $data = $request->all();
        ChucVu::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới thành công",
        ]);
    }
    public function load()
    {
        $data = ChucVu::get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function update(Request $request)
    {
        $data = $request->all();
        ChucVu::find($request->id)->update($data);

        return response()->json([
            "status" => '1',
            "message" => "Cập nhật thành công"
        ]);
    }
    public function doi(Request $request)
    {
        $data = ChucVu::find($request->id);
        if ($data->tinh_trang == 1) {
            $data->tinh_trang = 0;
            $data->save();
        } else {
            $data->tinh_trang = 1;
            $data->save();
        }
        return response()->json([
            "status" => '1',
            "message" => "Đổi trạng thái thành công"
        ]);
    }
    public function delete(Request $request)
    {
        ChucVu::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
}

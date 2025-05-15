<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatGioRequest;
use App\Http\Requests\ThemGioRequest;
use App\Models\LichHen;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LichHenController extends Controller
{
    public function loadkh()
    {
        $data = KhachHang::get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function load(){
        $data=LichHen::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function update(CapNhatGioRequest $request)
    {

            $data = $request->all();
            LichHen::find($request->id)->update($data);

            return response()->json([
                "status" => '1',
                "message" => "Cập nhật giờ thành công"
            ]);
    }
    public function doi(Request $request)
    {

            $data = LichHen::find($request->id);
            if ($data->tinh_trang == 1) {
                $data->tinh_trang = 0;
                $data->save();
            } else {
                $data->tinh_trang = 1;
                $data->save();
            }
            return response()->json([
                "status" => '1',
                "message" => "Đổi trạng thái giờ thành công"
            ]);
    }
    public function delete(Request $request)
    {
            LichHen::where('id', $request->id)->delete();
            return response()->json([
                "status" => '1',
                "message" => "Xóa giờ thành công"
            ]);
    }
    public function them(ThemGioRequest $request)
    {
            $data = $request->all();
            LichHen::create($data);
            return response()->json([
                'status' => '1',
                "message" => "Thêm mới giờ thành công",
            ]);
    }
}

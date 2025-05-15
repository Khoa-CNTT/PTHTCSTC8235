<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThanhToanLuongRequest;
use App\Models\Luong;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LuongController extends Controller
{
    private $id_chuc_nang = 10;
    public function Load()
    {
        $data = Luong::get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function LoadLuong()
    {
        $data = Luong::join('nhan_viens', 'luongs.id_nv', '=', 'nhan_viens.id')
                    ->select('luongs.*', 'nhan_viens.ten_nv')
                    ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function Doitt(Request $request)
    {
        $data = Luong::find($request->id);
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
    public function Them(ThanhToanLuongRequest $request){
        Luong::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Thêm mới Luong thành công'
        ]);
    }
    public function xoaLuong(Request $request)
    {
        Luong::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
    public function suaLuong(ThanhToanLuongRequest $request)
    {
        $data = $request->all();
        Luong::find($request->id)->update($data);
        return response()->json([
            "status" => '1',
            "message" => "Cập nhật lương thành công"
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

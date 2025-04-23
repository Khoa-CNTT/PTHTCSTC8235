<?php

namespace App\Http\Controllers;

use App\Models\HoSoBenhAn;
use Illuminate\Http\Request;

class HoSoBenhAnController extends Controller
{
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = HoSoBenhAn::where('id', 'like', $noi_dung)
            // ->orwhere('email', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request)
    {
        $data = $request->all();
        HoSoBenhAn::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới thành công",
        ]);
    }
    public function load()
    {
        $data = HoSoBenhAn::join("pets",'hosobenhans.id_pet', '=', 'pets.id')
        ->join('khachhangs', 'pets.id_kh', '=', 'khachhangs.id_kh')
        ->join('nhan_viens', 'hosobenhans.id_nv', '=', 'nhan_viens.id_nv')
        ->select(
        'hosobenhans.*',
        'pets.ten_pet',
        'khachhangs.ho_va_ten',
        'khachhangs.email',
        'nhan_viens.ten_nv',
        'nhan_viens.email as email_nv'
        )
        ->get();

        return response()->json([
            "data" => $data
        ]);
    }
    public function update(Request $request)
    {
        $data = $request->all();
        HoSoBenhAn::find($request->id)->update($data);

        return response()->json([
            "status" => '1',
            "message" => "Cập nhật thành công"
        ]);
    }
    public function doi(Request $request)
    {
        $data = HoSoBenhAn::find($request->id);
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
        HoSoBenhAn::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
}

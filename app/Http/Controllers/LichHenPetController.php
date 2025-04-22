<?php

namespace App\Http\Controllers;

use App\Models\LichHenPet;
use Illuminate\Http\Request;

class LichHenPetController extends Controller
{
    public function them(Request $request)
    {
        $data = $request->all();
        LichHenPet::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới thành công",
        ]);
    }

    public function load()
    {
        $data = LichHenPet::join('pets', 'pets.id', '=', 'lich_hen_pets.id_pet')
        ->join('nhan_viens', 'nhan_viens.id', '=', 'lich_hen_pets.id_nv')
        ->select('lich_hen_pets.*', 'pets.ten_pet', 'nhan_viens.ten_nv')
        ->get();
        return response()->json([
            "data" => $data
        ]);

    }
    public function update(Request $request)
    {
        $data = $request->all();
        LichHenPet::find($request->id)->update($data);

        return response()->json([
            "status" => '1',
            "message" => "Cập nhật nhân viên thành công"
        ]);
    }
    public function doi(Request $request)
    {
        $data = LichHenPet::find($request->id);
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
        LichHenPet::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
}

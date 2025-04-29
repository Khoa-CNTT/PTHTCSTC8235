<?php

namespace App\Http\Controllers;

use App\Models\LichHenPet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LichHenPetController extends Controller
{
    

public function them(Request $request)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json([
            'status' => '0',
            'message' => 'Không xác thực được người dùng',
        ], 401);
    }
    $lichHen = new LichHenPet();
    $lichHen->ten_dv = $request->ten_dv;
    $lichHen->id_lich = $request->id_lich;
    $lichHen->id_nv = $request->id_nv;
    $lichHen->id_pet = $request->id_pet;
    $lichHen->ngay = $request->ngay;
    $lichHen->gio = $request->gio;

    $lichHen->id_khach_hang = $user->id;
    $lichHen->ten_khach_hang = $user->name;

    $lichHen->save();

    return response()->json([
        'status' => '1',
        'message' => 'Thêm mới thành công',
        'data' => $lichHen
    ]);
}


    public function load()
    {
        $data = LichHenPet::join('pets', 'pets.id', '=', 'lich_hen_pets.id_pet')
        ->join('nhan_viens', 'nhan_viens.id', '=', 'lich_hen_pets.id_nv')
        ->join('lich_hens', 'lich_hens.id', '=', 'lich_hen_pets.id_lich')
        ->select('lich_hen_pets.*', 'pets.ten_pet', 'nhan_viens.ten_nv', 'lich_hen_pets.id_lich')
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

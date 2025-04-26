<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function Load()
    {
        $data = pet::join('khach_hangs', 'khach_hangs.id', '=', 'pets.id_kh')
            ->select('pets.*', 'khach_hangs.ho_va_ten')
            ->get();
        return response()->json([
            "data" => $data
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
    public function Them(Request $request)
    {
        pet::create($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Thêm mới pet thành công'
        ]);
    }
    public function showPetsByUserId($id)
    {
        $user = KhachHang::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $pets = Pet::where('id_kh', $id)->get();

        return response()->json(['pets' => $pets], 200);
    }
}

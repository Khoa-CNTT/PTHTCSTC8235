<?php

namespace App\Http\Controllers;

use App\Models\LichHenPet;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LichHenPetController extends Controller
{
    // public function them(Request $request)
    // {
    //     $user = Auth::guard('api')->user();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => '0',
    //             'message' => 'Không xác thực được người dùng',
    //         ], 401);
    //     }
    //     $data = $request->all();
    //     LichHenPet::create($data);
    //     return response()->json([
    //         'status' => '1',
    //         "message" => "Thêm mới đánh giá thành công",
    //     ]);
    // }
    public function them(Request $request)
    {
        // if (!Auth::check()) {
        //     return response()->json([
        //         'status' => '0',
        //         'message' => 'Không xác thực được người dùng',
        //     ], 401);
        // }
        // $data = $request->only(['id_lich', 'id_kh', 'id_dv', 'id_pet', 'ngay', 'gio']);
        // $pet = pet::find($data['id_pet']);
        // if ($pet->khach_hang_id !== $data['id_kh']) {
        //     return response()->json([
        //         'status' => '0',
        //         'message' => 'Thú cưng không thuộc về khách hàng này.',
        //     ], 400);
        // }
        // try {
        //     $lichHen = LichHenPet::create([
        //         'id_lich' => $data['id_lich'],
        //         'id_kh' => $data['id_kh'],
        //         'id_dv' => $data['id_dv'],
        //         'id_pet' => $data['id_pet'],
        //         'ngay' => $data['ngay'],
        //         'gio' => $data['gio'],
        //     ]);

        //     return response()->json([
        //         'status' => '1',
        //         'message' => 'Đặt lịch thành công!',
        //         'data' => $lichHen
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => '0',
        //         'message' => 'Có lỗi xảy ra khi tạo lịch hẹn.',
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
        LichHenPet::create($request->all());
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới thành công",
        ]);
    }
    public function load()
    {
        $data = LichHenPet::join('pets', 'pets.id', '=', 'lich_hen_pets.id_pet')
            ->join('khach_hangs', 'khach_hangs.id', '=', 'lich_hen_pets.id_kh')
            ->join('lich_hens', 'lich_hens.id', '=', 'lich_hen_pets.id_lich')
            ->select('lich_hen_pets.*', 'pets.ten_pet', 'khach_hangs.ho_va_ten', 'lich_hen_pets.id_lich')
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
            "message" => "Cập nhật lịch hẹn thành công"
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
            "status" => 1,
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

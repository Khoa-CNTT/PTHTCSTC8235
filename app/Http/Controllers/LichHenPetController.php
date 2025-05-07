<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThemLichHenRequest;
use App\Models\DichVu;
use App\Models\LichHenPet;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LichHenPetController extends Controller
{
    public function them(Request $request)
    {
        $dichVu = DichVu::find($request->id_dv);

        if (!$dichVu) {
            return response()->json([
                'status' => '0',
                'message' => 'Không tìm thấy dịch vụ.'
            ], 404);
        }

        $tienCoc = $dichVu->gia * 0.25;

        $lichHen = LichHenPet::create([
            'id_lich' => $request->id_lich,
            'id_kh' => $request->id_kh,
            'id_dv' => $request->id_dv,
            'id_pet' => $request->id_pet,
            'tinh_trang' => $request->tinh_trang,
            'ngay' => $request->ngay,
            'gio' => $request->gio,
            'tien_coc' => $tienCoc,
        ]);

        return response()->json([
            'status' => '1',
            'message' => 'Thêm mới thành công',
            'data' => $lichHen
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
    public function thongTinSlot(Request $request)
    {
        $ngay = $request->ngay;

        $slots = DB::table('lich_hen_pets')
            ->select('id_lich', DB::raw('COUNT(*) as so_luot'))
            ->where('ngay', $ngay)
            ->groupBy('id_lich')
            ->pluck('so_luot', 'id_lich'); 

        return response()->json([
            'status' => 1,
            'data' => $slots
        ]);
    }
}

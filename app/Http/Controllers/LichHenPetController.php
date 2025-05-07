<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LichHenPet;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LichHenPetController extends Controller
{
    public function layLichHenTheoBacSi()
    {
        $doctorId = Auth::user()->id;

        $ds = DB::table('lich_hen_pets')
            ->join('lich_hens', 'lich_hens.id', '=', 'lich_hen_pets.id_lich')
            ->join('pets', 'pets.id', '=', 'lich_hen_pets.id_pet')
            ->join('khach_hangs', 'khach_hangs.id', '=', 'lich_hen_pets.id_kh')
            ->join('dich_vus', 'dich_vus.id', '=', 'lich_hen_pets.id_dv')
            ->where('lich_hen_pets.id_nv', $doctorId)
            ->select(
                'lich_hen_pets.*',
                'lich_hen_pets.tinh_trang as trang_thai',
                'pets.ten_pet as ten_thu_cung',
                'khach_hangs.ho_va_ten as ten_khach_hang',
                'khach_hangs.so_dien_thoai',
                'dich_vus.ten_dv as dich_vu',
                'lich_hens.khung_gio'
            )
            ->orderBy('lich_hen_pets.ngay', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $ds
        ]);
    }
    public function them(Request $request)
    {
        $dichVu = DichVu::find($request->id_dv);

        if (!$dichVu) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy dịch vụ.'
            ], 404);
        }

        $tienCoc = $dichVu->gia * 0.25;

        // Kiểm tra ngày hợp lệ
        $ngayDat = Carbon::parse($request->ngay)->timezone('Asia/Ho_Chi_Minh')->startOfDay();
        $homNay = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();

        if ($ngayDat->lt($homNay)) {
            return response()->json([
                'status' => 0,
                'message' => 'Không thể đặt lịch ở ngày quá khứ.'
            ]);
        }

        if ($ngayDat->gt($homNay->copy()->addDays(50))) {
            return response()->json([
                'status' => 0,
                'message' => 'Chỉ được phép đặt lịch trong vòng 50 ngày tới.'
            ]);
        }

        // Nếu đặt lịch cho hôm nay => Kiểm tra giờ
        if ($ngayDat->equalTo($homNay)) {
            // Lấy giờ hiện tại
            $gioHienTai = Carbon::now('Asia/Ho_Chi_Minh');

            // Tách giờ bắt đầu từ chuỗi khung giờ (VD: "8:00 - 9:00")
            $gioBatDau = trim(explode('-', $request->gio)[0]); // lấy "8:00"
            $gioBatDauCarbon = Carbon::createFromFormat('H:i', $gioBatDau, 'Asia/Ho_Chi_Minh');

            if ($gioHienTai->gt($gioBatDauCarbon)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Khung giờ này đã qua. Vui lòng chọn giờ khác.'
                ]);
            }
        }

        // Kiểm tra số lượt đặt trong cùng khung giờ
        $count = LichHenPet::where('id_lich', $request->id_lich)
            ->where('ngay', $request->ngay)
            ->count();

        if ($count >= 2) {
            return response()->json([
                'status' => 0,
                'message' => 'Khung giờ này đã đủ số lượt đặt. Vui lòng chọn khung giờ khác.'
            ]);
        }

        // Tạo lịch hẹn
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
            'status' => 1,
            'message' => 'Thêm mới thành công',
            'data' => $lichHen
        ]);
    }
    public function thongTinSlot(Request $request)
    {
        $ngay = $request->ngay;

        $slots = DB::table('lich_hen_pets')
            ->select('id_lich', DB::raw('COUNT(*) as so_luot'))
            ->where('ngay', $ngay)
            ->groupBy('id_lich')
            ->pluck('so_luot', 'id_lich'); // trả về [id_lich => số lượt]

        return response()->json([
            'status' => 1,
            'data' => $slots
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

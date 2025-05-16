<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThemLichHenRequest;
use App\Mail\XacNhanLichHenMail;
use App\Models\DichVu;
use App\Models\KhachHang;
use App\Models\LichHenPet;
use App\Models\NhanVien;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
    public function ganBacSiTuDong($id_lich)
    {
        // Lấy dòng lịch hẹn pet
        $lich = DB::table('lich_hen_pets')
            ->where('id', $id_lich)
            ->first();

        if (!$lich) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy lịch']);
        }

        $ngay = $lich->ngay;
        $gio = $lich->gio;

        // B1: Lấy danh sách bác sĩ (dựa vào id_chucvu = 1 là bác sĩ
        $bac_si = DB::table('nhan_viens')
            ->where('id_chucvu', 1)
            ->get();

        // B2: Lọc bác sĩ chưa có lịch trong cùng khung giờ
        $bac_si_hop_le = $bac_si->filter(function ($b) use ($ngay, $gio) {
            return DB::table('lich_hen_pets')
                ->where('ngay', $ngay)
                ->where('gio', $gio)
                ->where('id_nv', $b->id)
                ->count() == 0;
        });

        if ($bac_si_hop_le->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Không còn bác sĩ nào trống khung giờ này']);
        }

        // B3: Ưu tiên bác sĩ ít lịch nhất
        $bac_si_chon = $bac_si_hop_le->map(function ($b) {
            $so_luot = DB::table('lich_hen_pets')->where('id_nv', $b->id)->count();
            return ['id' => $b->id, 'so_luot' => $so_luot];
        })->sortBy('so_luot')->first();

        // B4: Gán bác sĩ vào lịch hẹn
DB::table('lich_hen_pets')->where('id', $id_lich)->update([
            'id_nv' => $bac_si_chon['id']
        ]);

        return response()->json(['status' => true, 'message' => 'Đã gán bác sĩ thành công']);
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
            'payment_id' => $request->payment_id ?? null,
        ]);

        // Gán bác sĩ tự động sau khi tạo lịch hẹn
        if (in_array($dichVu->id_loaidv, [1, 4])) {
            $this->ganBacSiTuDong($lichHen->id);
        }

        // Lấy thông tin khách hàng và thú cưng để gửi email
        try {
            $khachHang = KhachHang::find($request->id_kh);
            $pet = Pet::find($request->id_pet);

            if ($khachHang && $khachHang->email) {
                // Chuẩn bị dữ liệu cho email
                $emailData = [
                    'ten_khach_hang' => $khachHang->ho_va_ten,
                    'ten_dv' => $dichVu->ten_dv,
                    'gio' => $request->gio,
                    'ngay' => $request->ngay,
                    'id_pet' => $request->id_pet,
                    'ten_pet' => $pet ? $pet->ten_pet : 'Không xác định',
                    'gia' => $dichVu->gia,
                    'tien_coc' => $tienCoc,
                    'payment_id' => $request->payment_id ?? 'Không có',
                ];

                // Gửi email xác nhận
                Mail::to($khachHang->email)->send(new XacNhanLichHenMail($emailData));
            }
        } catch (\Exception $e) {
            // Log lỗi nhưng vẫn tiếp tục xử lý
            
        }

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
    public function changeandCreateBill(Request $request)
    {
        $lichHen = DB::table('lich_hen_pets')->where('id', $request->id)->first();

        if (!$lichHen) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lịch hẹn'
            ]);
        }

        // Cập nhật trạng thái lịch hẹn thành đã điều trị
        DB::table('lich_hen_pets')->where('id', $lichHen->id)->update([
            'tinh_trang' => 1,
            'updated_at' => now()
        ]);

        // Tạo hóa đơn
        $idHoaDon = DB::table('hoa_dons')->insertGetId([
            'id_kh'              => $lichHen->id_kh,
            'id_nv'              => $lichHen->id_nv ?? 1,
            'id_pet'             => $lichHen->id_pet,
            'phuong_thuc'        => 1,
            'tinh_trang'         => 0,
            'ngay_xuat_hoa_don'  => now(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Lấy id đơn thuốc từ hồ sơ bệnh án
$hsba = DB::table('ho_so_benh_ans')->where('id_lich_hen_pet', $lichHen->id)->first();
        $idDonThuoc = $hsba->id_don_thuoc ?? null;

        if ($idDonThuoc) {
            // Nếu có đơn thuốc
            $donThuocChiTiets = DB::table('don_thuoc_chi_tiets')->where('id_don_thuoc', $idDonThuoc)->get();

            foreach ($donThuocChiTiets as $ct) {
                DB::table('hoa_don_chi_tiets')->insert([
                    'id_hoadon'         => $idHoaDon,
                    'id_ct_don_thuoc'   => $ct->id,
                    'id_lich_hen_pet'   => $lichHen->id,
                    'tien_kham'         => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        } else {
            // Nếu không có đơn thuốc vẫn tạo hóa đơn chi tiết
            DB::table('hoa_don_chi_tiets')->insert([
                'id_hoadon'         => $idHoaDon,
                'id_ct_don_thuoc'   => null,
                'id_lich_hen_pet'   => $lichHen->id,
                'tien_kham'         => 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xác nhận điều trị và tạo hóa đơn + chi tiết thành công'
        ]);
    }


    public function load()
    {
        $data = DB::table('lich_hen_pets as lhp')
            ->join('dich_vus as dv', 'lhp.id_dv', '=', 'dv.id')
            ->join('khach_hangs as kh', 'lhp.id_kh', '=', 'kh.id')
            ->join('pets as p', 'lhp.id_pet', '=', 'p.id')
            ->leftJoin('nhan_viens as nv', 'lhp.id_nv', '=', 'nv.id')
            ->select(
                'lhp.*',
                'dv.ten_dv',
                'kh.ho_va_ten',
                'p.ten_pet',
                'nv.ten_nv',
                'dv.gia'
            )
            ->orderByDesc('lhp.id')
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
    public function delete(Request $request)
    {
        LichHenPet::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
    public function showCalsByUserId($id)
    {
        $user = KhachHang::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $lichhenpets = DB::table('lich_hen_pets')
        ->join('dich_vus', 'lich_hen_pets.id_dv', '=', 'dich_vus.id')
        ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
        ->where('lich_hen_pets.id_kh', $id)
        ->select(
            'lich_hen_pets.*',
            'dich_vus.ten_dv',
            'dich_vus.gia',
            'pets.ten_pet',
        )
        ->get();
        return response()->json(['pets' => $lichhenpets], 200);
    }
}
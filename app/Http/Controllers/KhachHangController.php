<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatPetKhachHangRequest;
use App\Http\Requests\CapNhatThongTinCaNhanRequest;
use App\Http\Requests\DoiMatKhauRequest;
use App\Http\Requests\DangKyRequest;
use App\Http\Requests\DangNhapKhachHangRequest;
use App\Http\Requests\DangNhapRequest;
use App\Http\Requests\ThemPetKhachHangRequest;
use App\Models\KhachHang;
use App\Models\pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KhachHangController extends Controller
{
    public function load()
    {
        $data = KhachHang::get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = KhachHang::where('ho_va_ten', 'like', $noi_dung)
            ->orwhere('email', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function dangKy(DangKyRequest $request)
    {
        $hash_active = Str::uuid();
        $a = $request->all();
        $a['password'] = Hash::make($request->password);
        $khach_hang = KhachHang::create($a);
        $khach_hang['hash_active'] = $hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/client/" . $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\TaiKhoan('Kích hoạt tài khoản', 'GiaoDienMail', $data));
        return response()->json([
            "status" => "1",
            "message" => "Mail đã được gửi, vui lòng xác nhận email"
        ]);
    }
    public function sendMail(Request $request)
    {
        $hash_active = Str::uuid();
        $khach_hang = KhachHang::where('email', $request->email)->first();
        $khach_hang['hash_active'] = $hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/doi-mat-khau/" . $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\TaiKhoan('Xác nhận đổi mật khẩu', 'DoiMatKhauMail', $data));
        return response()->json([
            "message" => "Kiểm tra email của bạn"
        ]);
    }
    // giao dien dang nhap
    public function doimk(Request $request)
    {
        $khach_hang = KhachHang::where('hash_active', $request->ma)
            ->first();
        if ($khach_hang) {
            $khach_hang['password'] = bcrypt($request->pass);
            $khach_hang['hash_active'] = '';
            $khach_hang->save();
            return response()->json([
                "status" => 1,
                "message" => "Đổi mật khẩu thành công "
            ]);
        } else {
            return response()->json([
                "status" => 2,
                "message" => "Đổi mật khẩu không thành công "
            ]);
        }
    }
    public function guiMail(Request $request){
        $hash_active = Str::uuid();
        $khach_hang = KhachHang::where('email',$request->email)->first();
        $khach_hang['hash_active']=$hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/client/doi-mat-khau/". $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\TaiKhoan('Bạn muốn đổi mật khẩu?', 'DoiMatKhauMail', $data));
        return response()->json([
            "message" => "Kiểm tra email của bạn"
        ]);
    }
    public function kichHoat(Request $request)
    {
        $check = KhachHang::where('hash_active', $request->id_khach_hang)
            ->first();
        if ($check) {
            if ($check->is_active == 0) {
                $check->is_active = 1;
                $check->save();
                return response()->json([
                    "status" => 1,
                    "message" => "kích hoạt thành công"
                ]);
            } else {
                return response()->json([
                    "status" => 2,
                    "message" => "Tài khoản đã được kích hoạt trước đó"
                ]);
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => "Kích hoạt không thành công"
            ]);
        }
    }

    public function doipassTcn(DoiMatKhauRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user || !$user instanceof \App\Models\KhachHang) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần phải đăng nhập'
            ]);
        }        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->mat_khau_cu, $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Mật khẩu cũ không đúng'
            ]);
        }
        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->mat_khau_moi);
        $user->save();

        // Logout tất cả thiết bị
        DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', \App\Models\KhachHang::class)
            ->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.'
        ]);
    }
    public function layDuLieu()
    {
        $user = Auth::guard('sanctum')->user();
        return response()->json([
            'status' => 1,
            "data" => $user
        ]);
    }
    public function KiemTraDN()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            return response()->json([
                'status' => 1,
                'name' => $user->ho_va_ten,
                'email' => $user->email,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần phải đăng nhập',
            ]);
        }
    }
    public function dangNhap(DangNhapKhachHangRequest $request)
    {
        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->pass]);

        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            return response()->json([
                'status' => 1,
                'token' => $user->createToken('token')->plainTextToken,
                'message' => 'Đăng nhập thành công ',
                'id_khach_hang' => $user->id,

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Đăng nhập không thành công'
            ]);
        }
    }

    public function dangXuat()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)
                ->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Đăng xuất thành công'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Đăng xuất không thành công'
            ]);
        }
    }

    public function dangXuatAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Đăng xuất thành công',

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Đăng xuất không thành công',
            ]);
        }
    }

    public function info(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập'
            ], 401);
        }

        return response()->json([
            'id' => $user->id,
            'ho_va_ten' => $user->ho_va_ten,
            'email' => $user->email,
            'so_dien_thoai' => $user->so_dien_thoai,
            'ngay_sinh' => $user->ngay_sinh,
        ]);
    }
    public function Sua(CapNhatThongTinCaNhanRequest $request)
    {
        KhachHang::find($request->id)->update($request->all());
        return response()->json([
            'status' => 1,
            'message' => 'Sửa thông tin thành công :3'
        ]);
    }
    public function themPet(ThemPetKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user(); // Lấy user đang đăng nhập

        if (!$user || !$user instanceof KhachHang) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để thêm thú cưng'
            ], 401);
        }

        $pet = new pet();
        $pet->id_kh = $user->id; // Gán id khách hàng đăng nhập
        $pet->ten_pet = $request->ten_pet;
        $pet->chung_loai = $request->chung_loai;
        $pet->gioi_tinh = $request->gioi_tinh;
        $pet->tuoi = $request->tuoi;
        $pet->can_nang = $request->can_nang;
        $pet->hinh_anh = $request->hinh_anh ?? null;
        $pet->save();

        return response()->json([
            'status' => 1,
            'message' => 'Thêm thú cưng thành công'
        ]);
    }

    public function updatePet(CapNhatPetKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user instanceof \App\Models\KhachHang) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập'
            ], 401);
        }

        // Kiểm tra thú cưng có tồn tại và có thuộc về user này không
        $pet = \App\Models\Pet::where('id', $request->id)
            ->where('id_kh', $user->id)
            ->first();

        if (!$pet) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy thú cưng để cập nhật'
            ]);
        }

        // Update thông tin
        $pet->update([
            'ten_pet' => $request->ten_pet,
            'chung_loai' => $request->chung_loai,
            'gioi_tinh' => $request->gioi_tinh,
            'tuoi' => $request->tuoi,
            'can_nang' => $request->can_nang,
            'hinh_anh' => $request->hinh_anh
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật thông tin thú cưng thành công'
        ]);
    }
    public function xoaPet(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user instanceof \App\Models\KhachHang) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập'
            ], 401);
        }

        // Kiểm tra thú cưng có tồn tại và có thuộc về user này không
        $pet = \App\Models\Pet::where('id', $request->id)
            ->where('id_kh', $user->id)
            ->first();

        if (!$pet) {
            return response()->json([
                'status' => 0,
                'message' => 'Không tìm thấy thú cưng để xóa'
            ]);
        }

        // Xóa thú cưng
        $pet->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Xóa thú cưng thành công'
        ]);
    }
}

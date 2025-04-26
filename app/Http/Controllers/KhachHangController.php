<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
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
    public function dangKy(Request $request)
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
    public function  quenmk(Request $request){
        $hash_active = Str::uuid();
        $khach_hang = KhachHang::where('email',$request->email)->first();
        $khach_hang['hash_active']=$hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/khach-hang/doi-mat-khau" . $khach_hang->hash_active;
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
    public function layDuLieu(){
        $user = Auth::guard('sanctum')->user();
            return response()->json([
                'data' => $user
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
    public function dangNhap(Request $request)
    {
        $check = Auth::guard('khach_hang')->attempt(['email' => $request->email, 'password' => $request->pass]);

        if ($check) {
            $user = Auth::guard('khach_hang')->user();
            return response()->json([
                'status' => 1,
                'token' => $user->createToken('token')->plainTextToken,
                'message' => 'Đăng nhập thành công ',

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Đăng nhập không thành công'
            ]);
        }
    }

    public function dangXuat(){
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

    public function dangXuatAll(){
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\KhachHang) {
            DB::table('personal_access_tokens')
                ->where('id', $user->id)
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
}

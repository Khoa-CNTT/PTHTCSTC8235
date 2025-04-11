<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class KhachHangController extends Controller
{
    public function load(){
        $data=KhachHang::get();
        return response()->json([
            "data"=> $data
        ]);
    }
    public function timkiem(Request $request){
        $noi_dung='%'.$request->noi_dung.'%';
        $data= KhachHang::where('ho_va_ten','like',$noi_dung)
                        ->orwhere('email','like',$noi_dung)
                        ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request)
    {
        $hash_active = Str::uuid();
        $a = $request->all();
        $a['password'] = bcrypt($request->password);
        $khach_hang=KhachHang::create($a);
        $khach_hang['hash_active']=$hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/khach-hang/" . $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\TaiKhoan('Kich Hoat Tai khoan', 'GiaoDienMail', $data));
        return response()->json([
            "status" => "1",
            "message" => "them thanh cong"
        ]);
    }
    public function sendMail(Request $request){
        $hash_active = Str::uuid();
        $khach_hang = KhachHang::where('email',$request->email)->first();
        $khach_hang['hash_active']=$hash_active;
        $khach_hang->save();
        $data['ho_va_ten'] = $khach_hang->ho_va_ten;
        $data['link'] = "http://localhost:3000/doi-mat-khau/" . $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\TaiKhoan('Kich Hoat Tai khoan', 'DoiMatKhauMail', $data));
        return response()->json([
            "message" => "Kiểm tra email của bạn"
        ]);
    }
    public function doimk(Request $request){
        $khach_hang = KhachHang::where('hash_active', $request->ma)
        ->first();
        if($khach_hang){
            $khach_hang['password']=bcrypt($request->pass);
            $khach_hang['hash_active']='';
            $khach_hang->save();
            return response()->json([
                "status" => 1,
                "message" => "Đổi mật khẩu thành công "
            ]);
        }else{
            return response()->json([
                "status" => 2,
                "message" => "Đổi mật khẩu không thành công "
            ]);
        }
    }
    public function kichHoat(Request $request){
        $check = KhachHang::where('hash_active', $request->id_khach_hang)
                        ->first();
        if($check){
            if($check->is_active==0){
                $check->is_active=1;
                $check->save();
                return response()->json([
                    "status" => 1,
                    "message" => "kich hoat thanh cong"
                ]);
            }else {
                return response()->json([
                    "status" => 2,
                    "message" => "tai khoan da dc kich hoat truoc do"
                ]);
            }
        }else{
            return response()->json([
                "status" => 0,
                "message" => "kich hoat khong thanh cong"
            ]);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;

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
        $data['link'] = "http://localhost:5173/khach-hang/" . $khach_hang->hash_active;
        Mail::to($request->email)->send(new \App\Mail\MuaMail('Kich Hoat Tai khoan', 'ViewMail', $data));
        return response()->json([
            "status" => "1",
            "message" => "them thanh cong"
        ]);
    }
}

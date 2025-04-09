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
}

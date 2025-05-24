<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuaThuocRequest;
use App\Http\Requests\ThemThuocRequest;
use App\Models\Thuoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuocController extends Controller
{
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = Thuoc::where('ten_thuoc', 'like', $noi_dung)
            ->orwhere('mo_ta', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(ThemThuocRequest $request)
    {
        $data = $request->all();
        Thuoc::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thuốc được thêm thành công",
        ]);
    }
    public function load()
    {
        $data = DB::table('thuocs as t')
            ->leftJoin('thuoc_khos as tk', 't.id', '=', 'tk.id_thuoc')
            ->select(
                't.id',
                't.ten_thuoc',
                't.don_vi',
                't.mo_ta',
                't.tinh_trang',
                DB::raw('COALESCE(MAX(tk.gia_ban), t.gia_ban) as gia_ban'),
                DB::raw('SUM(tk.so_luong_ton_kho) as tong_ton_kho')
            )
            ->groupBy('t.id', 't.ten_thuoc','t.don_vi' ,'t.mo_ta', 't.tinh_trang', 't.gia_ban')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function update(SuaThuocRequest $request)
    {
        $data = $request->all();
        Thuoc::find($request->id)->update($data);

        return response()->json([
            "status" => '1',
            "message" => "Cập nhật thành công"
        ]);
    }
    public function doi(Request $request)
    {
        $data = Thuoc::find($request->id);
        if ($data->tinh_trang == 1) {
            $data->tinh_trang = 0;
            $data->save();
        } else {
            $data->tinh_trang = 1;
            $data->save();
        }
        return response()->json([
            "status" => '1',
            "message" => "Đổi trạng thái thuốc thành công"
        ]);
    }
    public function delete(Request $request)
    {
        Thuoc::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thuốc thành công"
        ]);
    }
}

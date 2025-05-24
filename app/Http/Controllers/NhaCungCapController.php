<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThemNhaCungCapRequest;
use App\Models\NhaCungCap;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhaCungCapController extends Controller
{
    private $id_chuc_nang = 9;
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = NhaCungCap::where('ten_ncc', 'like', $noi_dung)
            ->orwhere('email', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(ThemNhaCungCapRequest $request)
    {
            $data = $request->all();
            NhaCungCap::create($data);
            return response()->json([
                'status' => '1',
                "message" => "Thêm mới nhà cung cấp thành công",
            ]);
    }
    public function load()
    {
        $data = NhaCungCap::get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function update(ThemNhaCungCapRequest $request)
    {

            $data = $request->all();
            NhaCungCap::find($request->id)->update($data);
            return response()->json([
                "status" => '1',
                "message" => "cập nhật nhà cung cấp thành công"
            ]);
    }
    public function doi(Request $request)
    {
            $data = NhaCungCap::find($request->id);
            if ($data->tinh_trang == 1) {
                $data->tinh_trang = 0;
                $data->save();
            } else {
                $data->tinh_trang = 1;
                $data->save();
            }
            return response()->json([
                "status" => '1',
                "message" => "Đổi trạng thái nhà cung cấp thành công"
            ]);
    }
    public function delete(Request $request)
    {
            NhaCungCap::where('id', $request->id)->delete();
            return response()->json([
                "status" => '1',
                "message" => "Xóa nhà cung cấp thành công"
            ]);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NhanVienController extends Controller
{
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';
        $data = NhanVien::where('ten_nv', 'like', $noi_dung)
            ->orwhere('email', 'like', $noi_dung)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function them(Request $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        NhanVien::create($data);
        return response()->json([
            'status' => '1',
            "message" => "Thêm mới thành công",
        ]);
    }

    
    public function load()
    {
        $data = NhanVien::join('chuc_vus', 'chuc_vus.id', '=', 'nhan_viens.id_chucvu')
            ->select('nhan_viens.*', 'chuc_vus.ten_chuc_vu')
            ->get();
        return response()->json([
            "data" => $data
        ]);
    }
    public function dangXuat(){
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\NhanVien) {
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
    public function update(Request $request)
    {
        $data = $request->all();
        NhanVien::find($request->id)->update($data);

        return response()->json([
            "status" => '1',
            "message" => "Cập nhật nhân viên thành công"
        ]);
    }
    public function doi(Request $request)
    {
        $data = NhanVien::find($request->id);
        if ($data->tinh_trang == 1) {
            $data->tinh_trang = 0;
            $data->save();
        } else {
            $data->tinh_trang = 1;
            $data->save();
        }
        return response()->json([
            "status" => '1',
            "message" => "Đổi trạng thái thành công"
        ]);
    }
    public function delete(Request $request)
    {
        NhanVien::where('id', $request->id)->delete();
        return response()->json([
            "status" => '1',
            "message" => "Xóa thành công"
        ]);
    }
    public function dangNhap(Request $request)
    {

        $check = Auth::guard('nhan_vien')
            ->attempt(['email' => $request->email, 'password' => $request->password]);
        if ($check) {
            $user = Auth::guard('nhan_vien')->user();
            return response()->json([
                'status' => 1,
                'token' => $user->createToken('token')->plainTextToken,
                'message' => 'Đăng nhập thành công',
                'email' => $user->email,
                'name' => $user->ten_nv,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Đăng nhập không thành công'
            ]);
        }
    }
    public function checkLogin()
    {
        $user = Auth::guard('nhan_vien')->user();
        if ($user && $user instanceof \App\Models\NhanVien) {
            return response()->json([
                'status' => 1,
                // gọi name và email để hiện lên top admin khi đăng nhập vào từng acc nhân viên
                'name' => $user->ten_nv,
                'email' => $user->email,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần phải đăng nhập',
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatNhanVienRequest;
use App\Http\Requests\DangNhapRequest;
use App\Http\Requests\ThemNhanVienRequest;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PhanQuyen;

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
    public function them(ThemNhanVienRequest $request)
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
    public function dangXuat()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Đăng xuất thành công'
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Đăng xuất không thành công'
        ]);
    }
    public function update(CapNhatNhanVienRequest $request)
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
    public function dangNhap(DangNhapRequest $request)
    {
        $check = Auth::guard('nhan_vien')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        if ($check) {
            $user = Auth::guard('nhan_vien')->user();
            $permissions = PhanQuyen::where('id_chuc_vu', $user->id_chucvu)
                ->pluck('id_chuc_nang');

            // Tạo token tên khác nhau tùy loại người dùng
            $tokenName = $permissions->contains(17) ? 'token_doctor' : 'token_admin';

            return response()->json([
                'status' => 1,
                'token' => $user->createToken($tokenName)->plainTextToken,
                'message' => 'Đăng nhập thành công',
                'email' => $user->email,
                'name' => $user->ten_nv,
                'id_chucvu' => $user->id_chucvu,
                'ten_chuc_vu' => optional($user->chuc_vu)->ten_chuc_vu,
                'permissions' => $permissions,
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
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\NhanVien) {
            return response()->json([
                'status' => 1,
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
    public function LoadDataChiTiet(Request $request)
    {
        $data = NhanVien::where("id", $request->id)
            ->first();
        return response()->json([
            'data' => $data,
        ]);
    }
    public function loadBacSi()
    {
        try {
            $bac_si = NhanVien::with('chuc_vu')
                ->whereHas('phanQuyen', function ($query) {
                    $query->where('id_chuc_nang', 17);
                })
                ->select('id', 'ten_nv', 'id_chucvu')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $bac_si,
                'message' => 'Danh sách bác sĩ đã được tải thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tải danh sách bác sĩ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function kiemTraQuyen($id_chuc_nang)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
                ], 401);
            }

            $hasPermission = PhanQuyen::where('id_chuc_vu', $user->id_chucvu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->exists();

            if ($hasPermission) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Bạn có quyền truy cập'
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền truy cập chức năng này'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi khi kiểm tra quyền: ' . $e->getMessage()
            ], 500);
        }
    }
    public function thongTinBacSi()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user && $user instanceof \App\Models\NhanVien) {
            return response()->json([
                'id' => $user->id,
                'ten_nv' => $user->ten_nv,
                'email' => $user->email,
                'id_chucvu' => $user->id_chucvu,
                'chuc_vu' => optional($user->chuc_vu)->ten_chuc_vu,
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Không xác định được người dùng'
        ], 401);
    }
}

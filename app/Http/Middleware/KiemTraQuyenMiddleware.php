<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\PhanQuyen;
use Illuminate\Support\Facades\Auth;


class KiemTraQuyenMiddleware
{
    public function handle(Request $request, Closure $next, $id_chuc_nang)
    {
        // Lấy người dùng hiện tại qua Sanctum
        $user = Auth::guard('sanctum')->user();
        if ($user instanceof NhanVien) {
            $has = PhanQuyen::where('id_chuc_vu', $user->id_chucvu)
                ->where('id_chuc_nang', $id_chuc_nang)
                ->exists();
            if (!$has) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn không có quyền thực hiện chức năng này'
                ], 403);
            }
            return $next($request);
        }
        return response()->json([
            'status' => 0,
            'message' => 'Bạn cần đăng nhập'
        ], 401);
    }
}
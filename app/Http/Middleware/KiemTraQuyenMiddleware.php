<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhanQuyen;
use Symfony\Component\HttpFoundation\Response;

class KiemTraQuyenMiddleware
{
    public function handle(Request $request, Closure $next, $id_chuc_nang)
    {
        // Lấy người dùng hiện tại qua Sanctum
        $user = Auth::guard('sanctum')->user();
        if (!$user || !($user instanceof \App\Models\NhanVien)) {
            return response()->json([
                'status' => 0,
                'message' => 'Chưa đăng nhập'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Kiểm tra phân quyền trong bảng phan_quyens
        $hasPermission = PhanQuyen::where('id_chuc_vu', $user->id_chucvu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'status' => 0,
                'message' => 'Không có quyền truy cập'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
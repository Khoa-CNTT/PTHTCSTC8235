<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PhanQuyen;
use Illuminate\Support\Facades\Auth;

class KiemTraQuyenMiddleware
{
    public function handle(Request $request, Closure $next, $idChucNang)
    {
        // Lấy thông tin nhân viên đang đăng nhập
        $nhanVien = Auth::user();

        // Kiểm tra quyền của nhân viên với chức năng
        $coQuyen = PhanQuyen::where('id_chuc_vu', $nhanVien->id_chucvu)
            ->where('id_chuc_nang', $idChucNang)
            ->exists();

        if (!$coQuyen) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền truy cập chức năng này'
            ], 403);
        }

        return $next($request);
    }
} 
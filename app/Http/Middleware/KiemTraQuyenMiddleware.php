<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\PhanQuyen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KiemTraQuyenMiddleware
{
    public function handle(Request $request, Closure $next, $id_chuc_nang)
    {
        try {
            // Lấy người dùng hiện tại qua Sanctum
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
                ], 401);
            }
            
            if (!($user instanceof NhanVien)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Loại tài khoản không được phép truy cập'
                ], 403);
            }
            
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
        } catch (\Exception $e) {
            Log::error('Lỗi kiểm tra quyền middleware: ' . $e->getMessage());
            
            return response()->json([
                'status' => 0,
                'message' => 'Lỗi hệ thống khi kiểm tra quyền'
            ], 500);
        }
    }
}
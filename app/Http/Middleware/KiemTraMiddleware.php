<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class KiemTraMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = Auth::guard('sanctum')->user();
        
        // Kiểm tra xem người dùng có phải là nhân viên không
        if (!$user || !($user instanceof \App\Models\NhanVien)) {
            return response()->json([
                'status' => '0',
                'message' => 'Bạn không có quyền truy cập vào chức năng này'
            ], 403);
        }

        // Kiểm tra quyền của nhân viên
        if (!DB::table('phan_quyens')
            ->where('id_chuc_vu', $user->id_chucvu)
            ->whereIn('id_chuc_nang', $permissions)
            ->exists()) {
            return response()->json([
                'status' => '0',
                'message' => 'Bạn không có quyền thực hiện chức năng này'
            ], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\PhanQuyen;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $id_chuc_nang)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $check = PhanQuyen::where('id_chuc_vu', $user->id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();

        if (!$check) {
            return response()->json([
                'message' => 'Bạn không có quyền truy cập chức năng này'
            ], 403);
        }

        return $next($request);
    }
}

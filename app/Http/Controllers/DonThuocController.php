<?php

namespace App\Http\Controllers;

use App\Models\Thuoc;
use Illuminate\Http\Request;

class DonThuocController extends Controller
{
    public function loadThuoc()
    {
        $thuocs = Thuoc::where('tinh_trang', 1)->get();

        return response()->json([
            'status' => true,
            'thuoc' => $thuocs,
        ]);
    }
}

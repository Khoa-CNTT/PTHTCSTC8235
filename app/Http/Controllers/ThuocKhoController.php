<?php

namespace App\Http\Controllers;

use App\Models\ThuocKho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuocKhoController extends Controller
{
    public function load()
    {
        $data = DB::table('thuoc_khos as tk')
            ->leftJoin('khos as k', 'tk.id_kho', '=', 'k.id')
            ->leftJoin('thuocs as t', 'tk.id_thuoc', '=', 't.id')
            ->leftJoin('phieu_nhap_chi_tiets as pnct', 'tk.id_thuoc', '=', 'pnct.id_thuoc')
            ->select(
                'tk.*',
                'k.ten_kho',
                't.ten_thuoc',
                'pnct.id_phieu_nhap'
            )
            ->orderByDesc('tk.id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
    public function timkiem(Request $request)
    {
        $noi_dung = '%' . $request->noi_dung . '%';

        $data = ThuocKho::join('thuocs', 'thuoc_khos.id_thuoc', '=', 'thuocs.id')
            ->where('thuocs.ten_thuoc', 'like', $noi_dung)
            ->orderByDesc('thuoc_khos.id')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }
    public function loc(Request $request)
    {
        $id_kho = $request->id_kho;

        $data = DB::table('thuoc_khos as tk')
            ->leftJoin('khos as k', 'tk.id_kho', '=', 'k.id')
            ->leftJoin('thuocs as t', 'tk.id_thuoc', '=', 't.id')
            ->select('tk.*', 'k.ten_kho', 't.ten_thuoc')
            ->when($id_kho, function ($query) use ($id_kho) {
                return $query->where('tk.id_kho', $id_kho);
            })
            ->orderByDesc('tk.id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}

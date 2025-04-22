<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use Illuminate\Http\Request;

class LichHenController extends Controller
{
    public function load(){
        $data=LichHen::get();
        return response()->json([
            "data"=> $data
        ]);
    }
}

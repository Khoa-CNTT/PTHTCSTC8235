<?php

use App\Http\Controllers\DichVuController;
use App\Http\Controllers\LoaiDichVuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("loai-dich-vu/them",[LoaiDichVuController::class,'them']);
Route::get("loai-dich-vu/load",[LoaiDichVuController::class,'load']);
Route::post("loai-dich-vu/update",[LoaiDichVuController::class,'update']);
Route::post("loai-dich-vu/xoa",[LoaiDichVuController::class,'delete']);

Route::post("dich-vu/them",[DichVuController::class,'them']);
Route::get("dich-vu/load",[DichVuController::class,'load']);
Route::post("dich-vu/doi",[DichVuController::class,'doi']);
Route::post("dich-vu/update",[DichVuController::class,'update']);
Route::post("dich-vu/del",[DichVuController::class,'delete']);
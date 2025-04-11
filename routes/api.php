<?php

use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\NhaCungCapController;
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

Route::post("nha-cung-cap/them",[NhaCungCapController::class,'them']);
Route::get("nha-cung-cap/load",[NhaCungCapController::class,'load']);
Route::post("nha-cung-cap/doi-TT",[NhaCungCapController::class,'doi']);
Route::post("nha-cung-cap/update",[NhaCungCapController::class,'update']);
Route::post("nha-cung-cap/xoa",[NhaCungCapController::class,'delete']);
Route::post('/nha-cung-cap/tim-kiem',[NhaCungCapController::class,'timkiem']);


Route::get("khach-hang/load",[KhachHangController::class,'load']);
Route::post('/khach-hang/tim-kiem',[KhachHangController::class,'timkiem']);


Route::get("danh-gia/load",[DanhGiaController::class,'load']);
Route::post("danh-gia/doi-TT",[DanhGiaController::class,'doi']);
Route::post("danh-gia/xoa",[DanhGiaController::class,'delete']);
Route::post('/danh-gia/tim-kiem',[DanhGiaController::class,'timkiem']);

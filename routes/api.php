<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DonThuocController;
use App\Http\Controllers\HoSoBenhAnController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\KhoController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\LichHenPetController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\LuongController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\PhanQuyenController;
use App\Http\Controllers\PhieuNhapController;
use App\Http\Controllers\ThuocController;
use App\Http\Controllers\ThuocKhoController;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("loai-dich-vu/them", [LoaiDichVuController::class, 'them']);
Route::get("loai-dich-vu/load", [LoaiDichVuController::class, 'load']);
Route::post("loai-dich-vu/update", [LoaiDichVuController::class, 'update']);
Route::post("loai-dich-vu/xoa", [LoaiDichVuController::class, 'delete']);

Route::post("dich-vu/them", [DichVuController::class, 'them']);
Route::get("dich-vu/load", [DichVuController::class, 'load']);
Route::post("dich-vu/doi", [DichVuController::class, 'doi']);
Route::post("dich-vu/update", [DichVuController::class, 'update']);
Route::post("dich-vu/del", [DichVuController::class, 'delete']);
Route::post('dich-vu/tim-kiem', [DichVuController::class, 'timkiem']);
Route::get('/dich-vu/load-chi-tiet/{id}', [DichVuController::class, 'LoadDataChiTiet']);
Route::get("dich-vu/load-tiem-chung",[DichVuController::class,'loadTiemChung']);
Route::get("dich-vu/load-cham-soc",[DichVuController::class,'loadChamSoc']);
Route::get("dich-vu/load-bac-si",[DichVuController::class,'loadBacSi']);


Route::post("thuoc/them", [ThuocController::class, 'them']);
Route::get("thuoc/load", [ThuocController::class, 'load']);
Route::post("thuoc/doi-TT", [ThuocController::class, 'doi']);
Route::post("thuoc/update", [ThuocController::class, 'update']);
Route::post("thuoc/del", [ThuocController::class, 'delete']);
Route::post('thuoc/tim-kiem', [ThuocController::class, 'timkiem']);


Route::post("nha-cung-cap/them", [NhaCungCapController::class, 'them']);
Route::get("nha-cung-cap/load", [NhaCungCapController::class, 'load']);
Route::post("nha-cung-cap/doi-TT", [NhaCungCapController::class, 'doi']);
Route::post("nha-cung-cap/update", [NhaCungCapController::class, 'update']);
Route::post("nha-cung-cap/xoa", [NhaCungCapController::class, 'delete']);
Route::post('/nha-cung-cap/tim-kiem', [NhaCungCapController::class, 'timkiem']);


Route::get("khach-hang/load", [KhachHangController::class, 'load']);
Route::post('/khach-hang/tim-kiem', [KhachHangController::class, 'timkiem']);
Route::post("khach-hang/dang-ky", [KhachHangController::class, 'dangKy']);
Route::post("khach-hang/doi-mat-khau", [KhachHangController::class, 'doimk']);
Route::Post("khach-hang/quen-mat-khau", [KhachHangController::class, 'sendMail']);
Route::post("khach-hang/kich-hoat", [KhachHangController::class, 'kichHoat']);
Route::post("khach-hang/dang-nhap", [KhachHangController::class, 'dangNhap']);
Route::get("khach-hang/dang-xuat", [KhachHangController::class, 'dangXuat']);
Route::get("khach-hang/dang-xuat-all", [KhachHangController::class, 'dangXuatAll']);
Route::post("khach-hang/Kiem-tra-dang-nhap", [KhachHangController::class, 'KiemTraDN']);
Route::get("/khach-hang/lay-du-lieu", [KhachHangController::class, 'layDuLieu']);
Route::post('/khach-hang/sua', [KhachHangController::class, 'Sua']);


Route::post('/them-pet', [PetController::class, 'Them']);
Route::post('/xoa-pet', [PetController::class, 'Xoa']);
Route::post('/sua-pet', [PetController::class, 'Sua']);
Route::post('/thay-doi-tt-pet', [PetController::class, 'Doitt']);
Route::get('/load-pet', [PetController::class, 'Load']);
Route::get('/pets/{id_kh}', [PetController::class, 'showPetsByUserId']);

Route::post('/them-luong', [LuongController::class, 'Them']);
Route::post('/thay-doi-trang-thai-luong', [LuongController::class, 'Doitt']);
Route::get('/load-luong', [LuongController::class, 'LoadLuong']);
Route::post('/tim-kiem-luong', [LuongController::class, 'TimKiem']);
Route::post("/sua-luong",[LuongController::class,'suaLuong']);
Route::post("/xoa-luong",[LuongController::class,'xoaLuong']);

Route::post("nhan-vien/them", [NhanVienController::class, 'them']);
Route::get("nhan-vien/load", [NhanVienController::class, 'load']);
Route::post("nhan-vien/doi-TT", [NhanVienController::class, 'doi']);
Route::post("nhan-vien/update", [NhanVienController::class, 'update']);
Route::post("nhan-vien/xoa", [NhanVienController::class, 'delete']);
Route::post('/nhan-vien/tim-kiem', [NhanVienController::class, 'timkiem']);
Route::post('/nhan-vien/kiem-tra-dang-nhap', [NhanVienController::class, 'checkLogin']);
Route::get('/nhan-vien/dang-xuat', [NhanVienController::class, 'dangXuat']);
Route::post('/nhan-vien/dang-nhap', [NhanVienController::class, 'dangNhap']);
Route::get('/nhan-vien/load-chi-tiet/{id}', [NhanVienController::class, 'LoadDataChiTiet']);

Route::post("danh-gia/them", [DanhGiaController::class, 'them']);
Route::get("danh-gia/load", [DanhGiaController::class, 'load']);
Route::post("danh-gia/doi-TT", [DanhGiaController::class, 'doi']);
Route::post("danh-gia/xoa", [DanhGiaController::class, 'delete']);
Route::post('/danh-gia/tim-kiem', [DanhGiaController::class, 'timkiem']);

Route::post("chuc-vu/them", [ChucVuController::class, 'them']);
Route::get("chuc-vu/load", [ChucVuController::class, 'load']);
Route::post("chuc-vu/doi-TT", [ChucVuController::class, 'doi']);
Route::post("chuc-vu/update", [ChucVuController::class, 'update']);
Route::post("chuc-vu/xoa", [ChucVuController::class, 'delete']);
Route::post('/chuc-vu/tim-kiem', [ChucVuController::class, 'timkiem']);


Route::post("kho/them", [KhoController::class, 'them']);
Route::get("kho/load", [KhoController::class, 'load']);
Route::post("kho/doi", [KhoController::class, 'doi']);
Route::post("kho/update", [KhoController::class, 'update']);
Route::post("kho/del", [KhoController::class, 'delete']);
Route::post('kho/tim-kiem', [KhoController::class, 'timkiem']);

Route::get("phieu-nhap/load-kho-ncc-thuoc", [PhieuNhapController::class, 'loadKhovaNCCvaThuoc']);
Route::get("/phieu-nhap", [PhieuNhapController::class, 'load']);
Route::post('phieu-nhap/tim-kiem', [PhieuNhapController::class, 'timkiem']);
Route::post("phieu-nhap/xoa", [PhieuNhapController::class, 'delete']);
Route::post("phieu-nhap/tao", [PhieuNhapController::class, 'tao']); //lưu phiếu nhập
Route::post("phieu-nhap/loc-theo-ngay", [PhieuNhapController::class, 'loc']);
Route::post("phieu-nhap/update", [PhieuNhapController::class, 'update']);


Route::post("thuoc-kho/loc", [ThuocKhoController::class, 'loc']);
Route::get("thuoc-kho/load", [ThuocKhoController::class, 'load']);
Route::post("thuoc-kho/tim-kiem", [ThuocKhoController::class, 'timkiem']);

Route::get('/phan-quyen/load-chuc-vu', [ChucVuController::class, 'load_chuc_vu']);

Route::get('/phan-quyen/load-chuc-nang', [PhanQuyenController::class, 'load_chuc_nang']);
Route::post('/phan-quyen/load-cap-quyen', [PhanQuyenController::class, 'load_cap_quyen']);
Route::post('/phan-quyen/xoa', [PhanQuyenController::class, 'xoa']);
Route::post('phan-quyen/cap-quyen', [PhanQuyenController::class, 'cap_quyen']);
Route::post('/phan-quyen/tim-kiem-chuc-nang', [PhanQuyenController::class, 'tim_kiem_cn']);
Route::get('/phan-quyen/load-chuc-nang',[PhanQuyenController::class,'load_chuc_nang']);
Route::post('phan-quyen/cap-quyen',[PhanQuyenController::class,'cap_quyen']);
Route::post('/phan-quyen/tim-kiem-chuc-nang',[PhanQuyenController::class,'tim_kiem_cn']);

Route::middleware('auth:sanctum')->post('/lich-hen/them', [LichHenPetController::class, 'them']);
Route::get("lich-hen/load",[LichHenPetController::class,'load']);
Route::post("lich-hen/doi",[LichHenPetController::class,'doi']);
Route::post("lich-hen/update",[LichHenPetController::class,'update']);
Route::post("lich-hen/del",[LichHenPetController::class,'delete']);

Route::get("lich/load",[LichHenController::class,'load']);

Route::middleware('auth:sanctum')->get('/user/info', [KhachHangController::class, 'info']);


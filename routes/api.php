<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DonThuocController;
use App\Http\Controllers\HoaDonController;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// admin dich vu routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:4'])->group(function () {
    Route::get("dich-vu/load", [DichVuController::class, 'load']);
    Route::post("dich-vu/them", [DichVuController::class, 'them']);
    Route::post("dich-vu/doi", [DichVuController::class, 'doi']);
    Route::post("dich-vu/update", [DichVuController::class, 'update']);
    Route::post("dich-vu/del", [DichVuController::class, 'delete']);
    // loai dich vu
    Route::post("loai-dich-vu/them", [LoaiDichVuController::class, 'them']);
    Route::get("loai-dich-vu/load", [LoaiDichVuController::class, 'load']);
    Route::post("loai-dich-vu/update", [LoaiDichVuController::class, 'update']);
    Route::post("loai-dich-vu/xoa", [LoaiDichVuController::class, 'delete']);
});

// client dich vu
Route::get("dich-vu/load-bac-si", [DichVuController::class, 'loadBacSi']);
Route::get("dich-vu/load", [DichVuController::class, 'load']);
Route::post('dich-vu/tim-kiem', [DichVuController::class, 'timkiem']);
Route::get('/dich-vu/load-chi-tiet/{id}', [DichVuController::class, 'LoadDataChiTiet']);
Route::get("dich-vu/load-tiem-chung", [DichVuController::class, 'loadTiemChung']);
Route::get("dich-vu/load-cham-soc", [DichVuController::class, 'loadChamSoc']);
Route::get("dich-vu/load-kham-benh", [DichVuController::class, 'loadKhamBenh']);



// admin Thuoc routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:8'])->group(function () {
    Route::post("thuoc/them", [ThuocController::class, 'them']);
    Route::get("thuoc/load", [ThuocController::class, 'load']);
    Route::post("thuoc/doi-TT", [ThuocController::class, 'doi']);
    Route::post("thuoc/update", [ThuocController::class, 'update']);
    Route::post("thuoc/del", [ThuocController::class, 'delete']);
    Route::post('thuoc/tim-kiem', [ThuocController::class, 'timkiem']);
});

// admin NhaCungCap routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:9'])->group(function () {
    Route::post("nha-cung-cap/them", [NhaCungCapController::class, 'them']);
    Route::get("nha-cung-cap/load", [NhaCungCapController::class, 'load']);
    Route::post("nha-cung-cap/doi-TT", [NhaCungCapController::class, 'doi']);
    Route::post("nha-cung-cap/update", [NhaCungCapController::class, 'update']);
    Route::post("nha-cung-cap/xoa", [NhaCungCapController::class, 'delete']);
    Route::post('/nha-cung-cap/tim-kiem', [NhaCungCapController::class, 'timkiem']);
});

// admin KhachHang routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:6'])->group(function () {
    Route::get("/khach-hang/load", [KhachHangController::class, 'load']);
    Route::post('/khach-hang/tim-kiem', [KhachHangController::class, 'timkiem']);
});

// client khach hang routes
Route::post("khach-hang/dang-ky", [KhachHangController::class, 'dangKy']);
Route::post("/khach-hang/doi-mat-khau", [KhachHangController::class, 'doimk']);
Route::post("khach-hang/send-mail", [KhachHangController::class, 'guiMail']);
Route::post("khach-hang/kich-hoat", [KhachHangController::class, 'kichHoat']);
Route::post("khach-hang/dang-nhap", [KhachHangController::class, 'dangNhap']);
Route::get("khach-hang/dang-xuat", [KhachHangController::class, 'dangXuat']);
Route::get("khach-hang/dang-xuat-all", [KhachHangController::class, 'dangXuatAll']);
Route::post("khach-hang/Kiem-tra-dang-nhap", [KhachHangController::class, 'KiemTraDN']);
Route::get("/khach-hang/lay-du-lieu", [KhachHangController::class, 'layDuLieu'])->middleware('auth:sanctum');
Route::post('/khach-hang/sua', [KhachHangController::class, 'Sua'])->middleware('auth:sanctum');
Route::post("khach-hang/doi-mat-khau-tcn", [KhachHangController::class, 'doipassTcn'])->middleware('auth:sanctum');
Route::post("khach-hang/them-pet", [KhachHangController::class, 'themPet'])->middleware('auth:sanctum');
Route::post("khach-hang/update-pet", [KhachHangController::class, 'updatePet'])->middleware('auth:sanctum');
Route::post("khach-hang/xoa-pet", [KhachHangController::class, 'xoaPet'])->middleware('auth:sanctum');
Route::get('/pets/{id_kh}', [PetController::class, 'showPetsByUserId'])->middleware('auth:sanctum');


//admin Pet routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:7'])->group(function () {
    Route::post('/them-pet', [PetController::class, 'Them']);
    Route::post('/xoa-pet', [PetController::class, 'Xoa']);
    Route::post('/sua-pet', [PetController::class, 'Sua']);
    Route::post('/thay-doi-tt-pet', [PetController::class, 'Doitt']);
    Route::get('/load-pet', [PetController::class, 'Load']);
});

// admin Luong routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:10'])->group(function () {
    Route::post('/them-luong', [LuongController::class, 'Them']);
    Route::post('/thay-doi-trang-thai-luong', [LuongController::class, 'Doitt']);
    Route::get('/load-luong', [LuongController::class, 'LoadLuong']);
    Route::post('/tim-kiem-luong', [LuongController::class, 'TimKiem']);
    Route::post("/sua-luong", [LuongController::class, 'suaLuong']);
    Route::post("/xoa-luong", [LuongController::class, 'xoaLuong']);
});

Route::post('/them-luong', [LuongController::class, 'Them']);
Route::post('/thay-doi-trang-thai-luong', [LuongController::class, 'Doitt']);
Route::get('/load-luong', [LuongController::class, 'LoadLuong']);
Route::post('/tim-kiem-luong', [LuongController::class, 'TimKiem']);
Route::post("/sua-luong", [LuongController::class, 'suaLuong']);
Route::post("/xoa-luong", [LuongController::class, 'xoaLuong']);

// admin NhanVien routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:5'])->group(function () {
    Route::post("nhan-vien/them", [NhanVienController::class, 'them']);
    Route::post("nhan-vien/doi-TT", [NhanVienController::class, 'doi']);
    Route::post("nhan-vien/update", [NhanVienController::class, 'update']);
    Route::post("nhan-vien/xoa", [NhanVienController::class, 'delete']);
    Route::post('/nhan-vien/tim-kiem', [NhanVienController::class, 'timkiem']);
});

//client nhan vien routes
Route::get('/nhan-vien/load-chi-tiet/{id}', [NhanVienController::class, 'LoadDataChiTiet']);
Route::get("nhan-vien/load", [NhanVienController::class, 'load']);

Route::middleware('auth:sanctum')->get('/doctor/thong-tin-bac-si', [NhanVienController::class, 'thongTinBacSi']);
// dang nhap nhan vien routes
Route::get('/nhan-vien/kiem-tra-dang-nhap', [NhanVienController::class, 'checkLogin']);
Route::get('/nhan-vien/dang-xuat', [NhanVienController::class, 'dangXuat']);
Route::post('/nhan-vien/dang-nhap', [NhanVienController::class, 'dangNhap']);

// admin DanhGia routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:11'])->group(function () {
    Route::get("khach-hang/load", [KhachHangController::class, 'load']);
    Route::post("danh-gia/them", [DanhGiaController::class, 'them']);
    Route::get("danh-gia/load", [DanhGiaController::class, 'load']);
    Route::post("danh-gia/doi-TT", [DanhGiaController::class, 'doi']);
    Route::post("danh-gia/xoa", [DanhGiaController::class, 'delete']);
    Route::post('/danh-gia/tim-kiem', [DanhGiaController::class, 'timkiem']);
});
Route::get("danh-gia/load2", [DanhGiaController::class, 'load2']);
Route::post("danh-gia/them2", [DanhGiaController::class, 'them2']);
Route::get("khach-hang/load", [KhachHangController::class, 'load']);

// admin Kho routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:11'])->group(function () {
    Route::post("kho/them", [KhoController::class, 'them']);
    Route::get("kho/load", [KhoController::class, 'load']);
    Route::post("kho/doi", [KhoController::class, 'doi']);
    Route::post("kho/update", [KhoController::class, 'update']);
    Route::post("kho/del", [KhoController::class, 'delete']);
    Route::post('kho/tim-kiem', [KhoController::class, 'timkiem']);
});

// admin PhieuNhap routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:1'])->group(function () {
    Route::get("phieu-nhap/load-kho-ncc-thuoc", [PhieuNhapController::class, 'loadKhovaNCCvaThuoc']);
    Route::get("/phieu-nhap", [PhieuNhapController::class, 'load']);
    Route::post('phieu-nhap/tim-kiem', [PhieuNhapController::class, 'timkiem']);
    Route::post("phieu-nhap/xoa", [PhieuNhapController::class, 'delete']);
    Route::post("phieu-nhap/tao", [PhieuNhapController::class, 'tao']); //lưu phiếu nhập
    Route::post("phieu-nhap/loc-theo-ngay", [PhieuNhapController::class, 'loc']);
    Route::post("phieu-nhap/update", [PhieuNhapController::class, 'update']);
    Route::post("phieu-nhap/nhap-kho", [PhieuNhapController::class, 'nhapKho']);
});

// admin ThuocKho routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:2'])->group(function () {
    Route::post("thuoc-kho/loc", [ThuocKhoController::class, 'loc']);
    Route::get("thuoc-kho/load", [ThuocKhoController::class, 'load']);
    Route::post("thuoc-kho/tim-kiem", [ThuocKhoController::class, 'timkiem']);
    Route::post("thuoc-kho/del", [ThuocKhoController::class, 'xoa']);
});



// admin PhanQuyen routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:16'])->group(function () {
    Route::get('/phan-quyen/load-chuc-vu', [ChucVuController::class, 'load_chuc_vu']);
    Route::get('/phan-quyen/load-chuc-nang', [PhanQuyenController::class, 'load_chuc_nang']);
    Route::post('/phan-quyen/load-cap-quyen', [PhanQuyenController::class, 'load_cap_quyen']);
    Route::post('/phan-quyen/xoa', [PhanQuyenController::class, 'xoa']);
    Route::post('phan-quyen/cap-quyen', [PhanQuyenController::class, 'cap_quyen']);
    Route::post('/phan-quyen/tim-kiem-chuc-nang', [PhanQuyenController::class, 'tim_kiem_cn']);
    Route::post("chuc-vu/them", [ChucVuController::class, 'them']);
    Route::get("chuc-vu/load", [ChucVuController::class, 'load']);
    Route::post("chuc-vu/doi-TT", [ChucVuController::class, 'doi']);
    Route::post("chuc-vu/update", [ChucVuController::class, 'update']);
    Route::post("chuc-vu/xoa", [ChucVuController::class, 'delete']);
    Route::post('/chuc-vu/tim-kiem', [ChucVuController::class, 'timkiem']);
});

// admin LichHen routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:3'])->group(function () {
    Route::get("lich-hen/khach-hang-load", [LichHenController::class, 'loadkh']);
    Route::post("lich-hen/them", [LichHenPetController::class, 'them']);
    Route::get("lich-hen/load", [LichHenPetController::class, 'load']);
    Route::post("lich-hen/doi", [LichHenPetController::class, 'doi']);
    Route::post("lich-hen/update", [LichHenPetController::class, 'update']);
    Route::post("lich-hen/del", [LichHenPetController::class, 'delete']);
    Route::get("lich/load", [LichHenController::class, 'load']);
});

//bac si routes
Route::middleware(['auth:sanctum', 'kiemtra.quyen:17'])->group(function () {
    Route::get("/don-thuoc/load-thuoc", [DonThuocController::class, 'loadThuoc']);
    Route::get('/ho-so-benh-an/chi-tiet/{id}', [HoSoBenhAnController::class, 'chiTiet']);
    Route::get("ho-so-benh-an/load", [HoSoBenhAnController::class, 'load']);
    Route::post("ho-so-benh-an/doi-TT", [HoSoBenhAnController::class, 'doi']);
    Route::post("ho-so-benh-an/update", [HoSoBenhAnController::class, 'update']);
    Route::post("ho-so-benh-an/xoa", [HoSoBenhAnController::class, 'delete']);
    Route::post("ho-so-benh-an/tim-kiem", [HoSoBenhAnController::class, 'search']);
    Route::post("ho-so-benh-an/them", [HoSoBenhAnController::class, 'them']);
    Route::post('/ho-so-benh-an/tao-tu-lich', [HoSoBenhAnController::class, 'taoTuLich']);
    Route::get('/don-thuoc/load-thuoc', [DonThuocController::class, 'loadThuoc']);
    Route::post('/don-thuoc/them', [DonThuocController::class, 'them']);
    Route::get('/don-thuoc/load', [DonThuocController::class, 'load']);
    Route::get('/don-thuoc/danh-sach', [DonThuocController::class, 'getAll']);
    Route::post('/don-thuoc/xoa', [DonThuocController::class, 'xoa']);
    Route::get('/khach-hang/load', [KhachHangController::class, 'load']);
    Route::get('/nhan-vien/load-bac-si', [NhanVienController::class, 'loadBacSi']);
    Route::get('/pet/load', [PetController::class, 'Load']);
    Route::get('don-thuoc/chi-tiet/{id}', [DonThuocController::class, 'chiTiet']);
    Route::get('/don-thuoc/chi-tiet-in/{id}', [DonThuocController::class, 'getChiTietInDon']);
    Route::get('/don-thuoc/khach-hang-dang-dieu-tri', [DonThuocController::class, 'loadKhachHangDangDieuTri']);
    Route::get('/don-thuoc/pets-dang-dieu-tri/{id_kh}', [DonThuocController::class, 'layPetsDangDieuTriTheoKhach']);
    Route::post('/don-thuoc/toggle-tinh-trang', [DonThuocController::class, 'toggleTinhTrang']);
    Route::post('/lich-hen/them', [LichHenPetController::class, 'them']);
    Route::get('/doctor/lich-hen', [LichHenPetController::class, 'layLichHenTheoBacSi']);
    Route::post('/don-thuoc/tim-kiem', [DonThuocController::class, 'timKiem']);
});
// Route kiểm tra quyền
Route::get('/phan-quyen/kiem-tra-quyen/{id}', [NhanVienController::class, 'kiemTraQuyen']);

//client lich hen routes
Route::middleware('auth:api')->post('/lich-hen/them', [LichHenPetController::class, 'them']);
Route::get("lich-hen/load",[LichHenPetController::class,'load']);
Route::post("lich-hen/doi",[LichHenPetController::class,'doi']);
Route::post("lich-hen/update",[LichHenPetController::class,'update']);
Route::post("lich-hen/del",[LichHenPetController::class,'delete']);
Route::get("lich/load",[LichHenController::class,'load']);

Route::get('/ho-so-benh-an/loc-theo-bac-si/{id}', [HoSoBenhAnController::class, 'locTheoBacSi']);

// Chatbot routes
Route::post('/chatbot/chat', [ChatbotController::class, 'query']);
Route::post('/chatbot/ask', [ChatbotController::class, 'ask']); // Legacy endpoint
Route::get('/chatbot/suggest-services', [ChatbotController::class, 'suggestServices']);
Route::get('/chatbot/get-available-slots', [ChatbotController::class, 'getAvailableSlots']);
Route::get('/chatbot/get-service-reviews/{serviceId}', [ChatbotController::class, 'getServiceReviews']);




Route::get("lich-hen/load", [LichHenPetController::class, 'load']);
Route::post("lich-hen/doi", [LichHenPetController::class, 'doi']);
Route::post("lich-hen/update", [LichHenPetController::class, 'update']);
Route::post("lich-hen/del", [LichHenPetController::class, 'delete']);

Route::get("lich-hen/thong-tin-slot", [LichHenPetController::class, 'thongTinSlot']);

Route::get("lich/load", [LichHenController::class, 'load']);
Route::get('/lich-hen/thong-tin-slot', [LichHenPetController::class, 'thongTinSlot']);
Route::post('/lich-hen/them', [LichHenPetController::class, 'them']);

Route::get("doctor/thong-tin-bac-si", [LichHenPetController::class, 'load']);

Route::post("gio/them", [LichHenController::class, 'them']);
Route::get("gio/load", [LichHenController::class, 'load']);
Route::post("gio/doi-TT", [LichHenController::class, 'doi']);
Route::post("gio/update", [LichHenController::class, 'update']);
Route::post("gio/del", [LichHenController::class, 'delete']);

Route::get("hoa-don/load", [HoaDonController::class, 'load']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('hoa-don/load', [HoaDonController::class, 'load']);
    Route::get('hoa-don/danh-sach', [HoaDonController::class, 'danhSach']);
    Route::get('hoa-don/chi-tiet/{id}', [HoaDonController::class, 'chiTiet']);
    Route::post('hoa-don/update', [HoaDonController::class, 'update']);
    Route::post('hoa-don/xoa', [HoaDonController::class, 'xoa']);
});
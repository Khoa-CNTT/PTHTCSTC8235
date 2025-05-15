<?php

namespace App\Http\Controllers;

use App\Models\HoSoBenhAn;
use App\Models\ThuCung;
use App\Models\KhachHang;
use App\Models\DonThuoc;
use App\Models\DonThuocChiTiet;
use App\Models\LichHenPet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoSoBenhAnController extends Controller
{
    public function load()
    {
        $ho_so_benh_an = DB::table('ho_so_benh_ans')
            ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->join('khach_hangs', 'lich_hen_pets.id_kh', '=', 'khach_hangs.id')
            ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->select(
                'ho_so_benh_ans.*',
                'lich_hen_pets.id_pet',
                'lich_hen_pets.id_kh',
                'lich_hen_pets.ngay as ngay_kham',
                'pets.ten_pet as ten_thu_cung',
                'khach_hangs.ho_va_ten as ten_chu',
                'khach_hangs.so_dien_thoai as sdt',
                'nhan_viens.ten_nv as ten_bac_si'
            )
            ->orderBy('lich_hen_pets.ngay', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $ho_so_benh_an
        ]);
    }

    public function taoTuLich(Request $req)
    {
        $lich = LichHenPet::find($req->id_lich);

        if (!$lich) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lịch hẹn'
            ]);
        }

        $hoSoDaTonTai = HoSoBenhAn::where('id_lich_hen_pet', $lich->id)->exists();

        if ($hoSoDaTonTai) {
            return response()->json([
                'status' => false,
                'message' => 'Đã tạo hồ sơ cho lịch hẹn này'
            ]);
        }

        $hsba = HoSoBenhAn::create([
            'id_lich_hen_pet' => $lich->id,
            'id_nv' => $req->id_nv,
            'tinh_trang' => 1,
            'chuan_doan' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tạo hồ sơ bệnh án thành công',
            'id_hsba' => $hsba->id
        ]);
    }


    public function chiTiet($id)
    {
        $ho_so = DB::table('ho_so_benh_ans')
            ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
            ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
            ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
            ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
            ->where('ho_so_benh_ans.id', $id)
            ->select(
                'ho_so_benh_ans.*',
                'pets.ten_pet as ten_thu_cung',
                'pets.tuoi',
                'lich_hen_pets.ngay as ngay_kham',
                'pets.can_nang',
                'pets.chung_loai',
                'pets.gioi_tinh as gioi_tinh_pet',
                'khach_hangs.ho_va_ten as ten_chu',
                'nhan_viens.ten_nv as ten_bac_si'
            )
            ->first();

        $thuoc = DB::table('don_thuocs')
            ->join('don_thuoc_chi_tiets', 'don_thuocs.id', '=', 'don_thuoc_chi_tiets.id_don_thuoc')
            ->join('thuocs', 'don_thuoc_chi_tiets.id_thuoc', '=', 'thuocs.id')
            ->join('ho_so_benh_ans', 'ho_so_benh_ans.id_don_thuoc', '=', 'don_thuocs.id')
            ->where('ho_so_benh_ans.id', $id)
            ->where('don_thuoc_chi_tiets.tinh_trang', '1')
            ->select('thuocs.ten_thuoc', 'don_thuoc_chi_tiets.so_luong', 'don_thuoc_chi_tiets.lieu_luong')
            ->get();

        return response()->json([
            'status' => true,
            'ho_so' => $ho_so,
            'thuoc' => $thuoc
        ]);
    }
    public function update(Request $request)
    {
        try {
            $ho_so_benh_an = HoSoBenhAn::find($request->id);
            if (!$ho_so_benh_an) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hồ sơ bệnh án'
                ]);
            }

            // Cập nhật chẩn đoán & tình trạng
            if ($request->has('tinh_trang') && !$request->has('chuan_doan')) {
                $ho_so_benh_an->tinh_trang = $request->tinh_trang;
            } else {
                $ho_so_benh_an->chuan_doan = $request->chuan_doan;
                $ho_so_benh_an->tinh_trang = $request->tinh_trang;
            }
            $ho_so_benh_an->save();

            // Nếu tình trạng là "đã khỏi" => tạo hóa đơn + chi tiết
            if ($ho_so_benh_an->tinh_trang == 0) {
                $lich = LichHenPet::find($ho_so_benh_an->id_lich_hen_pet);

                if ($lich) {
                    // Cập nhật lịch hẹn đã điều trị
                    $lich->tinh_trang = 1;
                    $lich->save();

                    // Nếu chưa có hóa đơn
                    $daCoHoaDon = DB::table('hoa_dons')->where('id_lich_pet', $lich->id)->exists();
                    if (!$daCoHoaDon) {
                        $idHoaDon = DB::table('hoa_dons')->insertGetId([
                            'id_kh'              => $lich->id_kh,
                            'id_pet'             => $lich->id_pet,
                            'id_nv'              => $lich->id_nv ?? 1,
                            'id_lich_pet'        => $lich->id,
                            'phuong_thuc'        => 1,
                            'tinh_trang'         => 0,
                            'ngay_xuat_hoa_don'  => now(),
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);

                        // Lấy đơn thuốc nếu có
                        $idDonThuoc = $ho_so_benh_an->id_don_thuoc;

                        if ($idDonThuoc) {
                            $chiTietThuocs = DB::table('don_thuoc_chi_tiets')
                                ->where('id_don_thuoc', $idDonThuoc)
                                ->get();

                            foreach ($chiTietThuocs as $thuoc) {
                                DB::table('hoa_don_chi_tiets')->insert([
                                    'id_hoadon'        => $idHoaDon,
                                    'id_lich_hen_pet'  => $lich->id,
                                    'id_ct_don_thuoc'  => $thuoc->id,
                                    'tien_kham'        => 0,
                                    'created_at'       => now(),
                                    'updated_at'       => now(),
                                ]);
                            }
                        } else {
                            // Không có đơn thuốc cũng tạo dòng chi tiết để cập nhật tiền dịch vụ, tiền khám, cọc
                            DB::table('hoa_don_chi_tiets')->insert([
                                'id_hoadon'        => $idHoaDon,
                                'id_lich_hen_pet'  => $lich->id,
                                'id_ct_don_thuoc'  => null,
                                'tien_kham'        => 0,
                                'created_at'       => now(),
                                'updated_at'       => now(),
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật hồ sơ bệnh án thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }


    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $ho_so_benh_an = HoSoBenhAn::find($request->id);
            if (!$ho_so_benh_an) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy hồ sơ bệnh án'
                ]);
            }

            // Lấy id đơn thuốc từ hồ sơ bệnh án
            $don_thuoc_id = $ho_so_benh_an->id_don_thuoc;

            if ($don_thuoc_id) {
                DonThuocChiTiet::where('id_don_thuoc', $don_thuoc_id)->delete();
                DonThuoc::where('id', $don_thuoc_id)->delete();
            }

            $ho_so_benh_an->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Xóa hồ sơ bệnh án và đơn thuốc thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }


    public function search(Request $request)
    {
        try {
            $ho_so_benh_an = DB::table('ho_so_benh_ans')
                ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
                ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
                ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
                ->where(function ($query) use ($request) {
                    $query->where('pets.ten_pet', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('ho_so_benh_ans.chuan_doan', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.ho_va_ten', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('khach_hangs.so_dien_thoai', 'like', '%' . $request->noi_dung . '%')
                        ->orWhere('nhan_viens.ten_nv', 'like', '%' . $request->noi_dung . '%');
                })
                ->select(
                    'ho_so_benh_ans.*',
                    'pets.ten_pet as ten_thu_cung',
                    'khach_hangs.ho_va_ten as ten_chu',
                    'lich_hen_pets.ngay as ngay_kham',
                    'khach_hangs.so_dien_thoai as sdt',
                    'nhan_viens.ten_nv as ten_bac_si'
                )
                ->orderBy('lich_hen_pets.ngay', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $ho_so_benh_an
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function locTheoBacSi($id)
    {
        try {
            $ho_so_benh_an = DB::table('ho_so_benh_ans')
                ->join('lich_hen_pets', 'ho_so_benh_ans.id_lich_hen_pet', '=', 'lich_hen_pets.id')
                ->join('pets', 'lich_hen_pets.id_pet', '=', 'pets.id')
                ->join('khach_hangs', 'pets.id_kh', '=', 'khach_hangs.id')
                ->join('nhan_viens', 'ho_so_benh_ans.id_nv', '=', 'nhan_viens.id')
                ->where('ho_so_benh_ans.id_nv', $id)
                ->select(
                    'ho_so_benh_ans.*',
                    'pets.ten_pet as ten_thu_cung',
                    'khach_hangs.ho_va_ten as ten_chu',
                    'khach_hangs.so_dien_thoai as sdt',
                    'nhan_viens.ten_nv as ten_bac_si'
                )
                ->orderBy('lich_hen_pets.ngay', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $ho_so_benh_an
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}

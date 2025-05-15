<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuaDanhSachThuocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.id_thuoc' => 'required|exists:thuocs,id',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
            'chi_tiet.*.gia_nhap' => 'required|numeric|min:0',
            'chi_tiet.*.gia_ban' => 'required|numeric|min:0',
            'chi_tiet.*.han_su_dung' => 'required|date|after:today',
        ];
    }

    public function messages(): array
    {
        return [
            'chi_tiet.required' => 'Danh sách thuốc không được để trống.',
            'chi_tiet.*.id_thuoc.required' => 'Vui lòng chọn thuốc.',
            'chi_tiet.*.id_thuoc.exists' => 'Thuốc không tồn tại.',
            'chi_tiet.*.so_luong.required' => 'Vui lòng nhập số lượng.',
            'chi_tiet.*.so_luong.integer' => 'Số lượng phải là số nguyên.',
            'chi_tiet.*.so_luong.min' => 'Số lượng tối thiểu là 1.',
            'chi_tiet.*.gia_nhap.required' => 'Vui lòng nhập giá nhập.',
            'chi_tiet.*.gia_nhap.numeric' => 'Giá nhập phải là số.',
            'chi_tiet.*.gia_nhap.min' => 'Giá nhập không được âm.',
            'chi_tiet.*.gia_ban.required' => 'Vui lòng nhập giá bán.',
            'chi_tiet.*.gia_ban.numeric' => 'Giá bán phải là số.',
            'chi_tiet.*.gia_ban.min' => 'Giá bán không được âm.',
            'chi_tiet.*.han_su_dung.required' => 'Vui lòng nhập hạn sử dụng.',
            'chi_tiet.*.han_su_dung.date' => 'Hạn sử dụng không đúng định dạng.',
            'chi_tiet.*.han_su_dung.after' => 'Hạn sử dụng phải sau ngày hôm nay.',
        ];
    }
}

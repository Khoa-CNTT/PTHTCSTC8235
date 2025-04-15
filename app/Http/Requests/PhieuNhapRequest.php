<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhieuNhapRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_kho' => 'required|integer|exists:khos,id',
            'id_ncc' => 'required|integer|exists:nha_cung_caps,id',
            'ngay_nhap' => 'required|date',

            'chi_tiet' => 'required|array|min:1',
            'chi_tiet.*.id_thuoc' => 'required|integer|exists:thuocs,id',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
            'chi_tiet.*.gia_nhap' => 'required|integer|min:1',
            'chi_tiet.*.han_su_dung' => 'required|date|after:today',
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute không được để trống.',
            'exists' => ':attribute không hợp lệ.',
            'min' => ':attribute phải lớn hơn 0.',
            'after' => ':attribute phải sau ngày hôm nay.',
        ];
    }
    public function attributes()
    {
        return [
            'id_kho' => 'Kho',
            'id_ncc' => 'Nhà cung cấp',
            'ngay_nhap' => 'Ngày nhập',

            'chi_tiet' => 'Danh sách thuốc',
            'chi_tiet.*.id_thuoc' => 'Thuốc',
            'chi_tiet.*.so_luong' => 'Số lượng',
            'chi_tiet.*.gia_nhap' => 'Giá nhập',
            'chi_tiet.*.han_su_dung' => 'Hạn sử dụng',
        ];
    }
}

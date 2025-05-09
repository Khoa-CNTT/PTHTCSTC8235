<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThanhToanLuongRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_luong'          => 'required|string|max:20',
            'id_nv'             => 'required|exists:nhan_viens,id',
            'tien_luong'        => 'required|numeric|min:0',
            'ngay_thanh_toan'   => 'required|date',
            'tien_thuong'       => 'nullable|numeric|min:0',
            'tinh_trang'        => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'id_luong.required'         => 'Mã lương không được để trống.',
            'id_luong.string'           => 'Mã lương phải là chuỗi.',
            'id_luong.max'              => 'Mã lương không được vượt quá 20 ký tự.',

            'id_nv.required'            => 'Vui lòng chọn nhân viên.',
            'id_nv.exists'              => 'Nhân viên không tồn tại.',

            'tien_luong.required'       => 'Tiền lương không được để trống.',
            'tien_luong.numeric'        => 'Tiền lương phải là số.',
            'tien_luong.min'            => 'Tiền lương phải lớn hơn hoặc bằng 0.',

            'ngay_thanh_toan.required'  => 'Ngày thanh toán không được để trống.',
            'ngay_thanh_toan.date'      => 'Ngày thanh toán không hợp lệ.',

            'tien_thuong.numeric'       => 'Tiền thưởng phải là số.',
            'tien_thuong.min'           => 'Tiền thưởng phải lớn hơn hoặc bằng 0.',

            'tinh_trang.required'       => 'Vui lòng chọn tình trạng.',
            'tinh_trang.in'             => 'Tình trạng không hợp lệ.',
        ];
    }
}

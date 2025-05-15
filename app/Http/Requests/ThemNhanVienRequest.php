<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemNhanVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_nv' => 'required',
            'gioi_tinh' => 'required',
            'hinh_anh' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required',
            'mo_ta' => 'required',
            'tinh_trang' => 'required',
            'id_chucvu' => 'required',
            'tien_kham' => 'required_if:id_chucvu,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nv.required' => 'Họ và tên không được để trống.',
            'gioi_tinh.required' => 'Giới tính không được để trống.',
            'hinh_anh.required' => 'Hình ảnh không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'password.required' => 'Mật khẩu không được để trống.',
            'mo_ta.required' => 'Mô tả không được để trống.',
            'tinh_trang.required' => 'Tình trạng không được để trống.',
            'id_chucvu.required' => 'Chức vụ không được để trống.',
            'tien_kham.required_if' => 'Tiền khám không được để trống khi chức vụ là bác sĩ.',
        ];
    }
}

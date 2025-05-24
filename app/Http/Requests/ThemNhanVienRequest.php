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
            'ten_nv'       => 'required|string|max:255',
            'email'        => 'required|email|unique:nhan_viens,email',
            'password'     => 'required|min:6',
            'id_chucvu'    => 'required|integer|exists:chuc_vus,id',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_nv.required'     => 'Tên nhân viên không được để trống.',
            'email.required'      => 'Email không được để trống.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.unique'        => 'Email đã được sử dụng.',
            'password.required'   => 'Mật khẩu không được để trống.',
            'password.min'        => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'id_chucvu.required'  => 'Chức vụ không được để trống.',
            'id_chucvu.exists'    => 'Chức vụ không hợp lệ.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|exists:khach_hangs,email',
            'pass' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email không được để trống',
            'email.email'       => 'Email không đúng định dạng',
            'email.exists' => 'Email chưa được đăng ký. Bạn cần đăng ký tài khoản',
            'pass.required' => 'Mật khẩu không được để trống',
        ];
    }
}

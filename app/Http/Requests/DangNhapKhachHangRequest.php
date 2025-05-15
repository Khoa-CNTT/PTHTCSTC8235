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
            'email'    => 'required|email',
            'pass' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email không được để trống',
            'email.email'       => 'Email không đúng định dạng',
            'pass.required' => 'Mật khẩu không được để trống',
        ];
    }
}

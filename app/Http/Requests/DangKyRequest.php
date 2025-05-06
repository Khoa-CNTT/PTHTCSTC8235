<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
{
    return [
        'ho_va_ten'      => 'required|string|max:255',
        'email'          => 'required|email|unique:users,email',
        'so_dien_thoai'  => 'required|numeric|digits_between:10,15',
        'password'        => 'required|min:6|confirmed|regex:/[A-Z]/|regex:/[@$!%*?&]/',
    ];
}
public function messages(): array
{
    return [
        'ho_va_ten.required'       => 'Tên của bạn không được để trống',
        'email.required'           => 'Email không được để trống',
        'email.email'              => 'Email không đúng định dạng',
        'email.unique'             => 'Email đã được đăng ký',
        'so_dien_thoai.required'   => 'Số điện thoại không được để trống',
        'so_dien_thoai.numeric'    => 'Số điện thoại phải là dạng số',
        'so_dien_thoai.digits_between' => 'Số điện thoại phải có từ 10 đến 15 chữ số',
        'password.required'        => 'Mật khẩu không được để trống',
        'password.min'             => 'Mật khẩu phải có ít nhất 6 ký tự',
        'password.confirmed'       => 'Mật khẩu và xác nhận mật khẩu không khớp',
        'password.regex'           => 'Mật khẩu phải chứa ít nhất một chữ cái viết hoa và một ký tự đặc biệt',
    ];
}
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatThongTinCaNhanRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; 
    }


    public function rules(): array
    {
        return [
            'ho_va_ten'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'so_dien_thoai'  => 'required|regex:/^(0)[0-9]{9}$/',
            'ngay_sinh'      => 'nullable|date|before:today',
            'hinh_anh'       => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'ho_va_ten.required'     => 'Họ và tên không được để trống.',
            'ho_va_ten.max'          => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required'         => 'Email không được để trống.',
            'email.email'            => 'Email không đúng định dạng.',
            'email.max'              => 'Email không được vượt quá 255 ký tự.',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'so_dien_thoai.regex'    => 'Số điện thoại không hợp lệ (phải bắt đầu bằng 0 và đủ 10 số).',
            'ngay_sinh.date'         => 'Ngày sinh không hợp lệ.',
            'ngay_sinh.before'       => 'Ngày sinh phải nhỏ hơn ngày hiện tại.',
            'hinh_anh.max'           => 'Link hình ảnh quá dài.',
        ];
    }
}

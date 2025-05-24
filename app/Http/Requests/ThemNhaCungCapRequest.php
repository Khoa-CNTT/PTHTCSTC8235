<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemNhaCungCapRequest extends FormRequest
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
            'ten_ncc'     => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'so_dien_thoai' => 'required|digits_between:9,15',
            'dia_chi'     => 'required|string|max:255',
            'tinh_trang'  => 'required|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'ten_ncc.required'           => 'Tên nhà cung cấp không được để trống',
            'email.required'             => 'Email không được để trống',
            'email.email'                => 'Email không đúng định dạng',
            'so_dien_thoai.required'     => 'Số điện thoại không được để trống',
            'so_dien_thoai.digits_between' => 'Số điện thoại phải từ 9 đến 15 chữ số',
            'dia_chi.required'           => 'Địa chỉ không được để trống',
            'tinh_trang.required'        => 'Tình trạng không được để trống',
            'tinh_trang.in'              => 'Tình trạng phải là 0 (ẩn) hoặc 1 (hiển thị)',
        ];
    }
}

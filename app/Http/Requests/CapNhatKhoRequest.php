<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatKhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_kho' => ['required', 'string', 'max:255'],
            'dia_chi' => ['required', 'string', 'max:255'],
            'tinh_trang' => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_kho.required' => 'Vui lòng nhập tên kho.',
            'ten_kho.string' => 'Tên kho phải là một chuỗi.',
            'ten_kho.max' => 'Tên kho không được vượt quá 255 ký tự.',
            'dia_chi.required' => 'Vui lòng nhập địa chỉ.',
            'dia_chi.string' => 'Địa chỉ phải là một chuỗi.',
            'dia_chi.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'tinh_trang.required' => 'Vui lòng chọn tình trạng kho.',
            'tinh_trang.in' => 'Tình trạng kho phải là 0 (tạm ngưng) hoặc 1 (hoạt động).',
        ];
    }
}

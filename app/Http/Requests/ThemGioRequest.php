<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemGioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'khung_gio' => ['required'],
            'tinh_trang' => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'khung_gio.required' => 'Vui lòng nhập khung giờ.',
            'tinh_trang.required' => 'Vui lòng chọn tình trạng.',
            'tinh_trang.in' => 'Tình trạng chỉ có thể là 0 (ẩn) hoặc 1 (hiện).',
        ];
    }
}

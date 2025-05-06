<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemLoaiDichVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_loaidv' => 'required|string|max:255',
            'mo_ta'      => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_loaidv.required' => 'Tên loại dịch vụ không được để trống',
            'ten_loaidv.string'   => 'Tên loại dịch vụ phải là chuỗi ký tự',
            'ten_loaidv.max'      => 'Tên loại dịch vụ không được vượt quá 255 ký tự',
            'mo_ta.string'        => 'Mô tả phải là chuỗi văn bản',
            'mo_ta.max'           => 'Mô tả không được vượt quá 1000 ký tự',
        ];
    }
}

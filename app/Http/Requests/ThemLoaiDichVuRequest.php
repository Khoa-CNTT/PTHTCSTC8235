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
            'ten_loaidv' => 'required|string|max:255|unique:loai_dich_vus,ten_loaidv',
            'mo_ta'      => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_loaidv.required' => 'Tên không được bỏ trống.',
            'ten_loaidv.unique'   => 'Tên đã tồn tại',
            'ten_loaidv.string'   => 'Tên loại dịch vụ phải là chuỗi.',
            'ten_loaidv.max'      => 'Tên loại dịch vụ không vượt quá 255 ký tự.',
            'mo_ta.required'      => 'Vui lòng nhập mô tả cho loại dịch vụ.',
            'mo_ta.string'        => 'Mô tả phải là chuỗi văn bản.',
            'mo_ta.max'           => 'Mô tả không vượt quá 1000 ký tự.',
        ];
    }
}

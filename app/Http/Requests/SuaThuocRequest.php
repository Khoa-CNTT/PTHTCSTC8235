<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuaThuocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_thuoc'   => 'required',
            'don_vi'      => 'required',
            'mo_ta'       => 'required',
            'gia_ban'     => 'required|min:0',
            'tinh_trang'  => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_thuoc.required'   => 'Vui lòng nhập tên thuốc.',
            'don_vi.required'      => 'Vui lòng nhập đơn vị.',
            'mo_ta.required'       => 'Vui lòng nhập mô tả.',
            'gia_ban.required'     => 'Vui lòng nhập giá bán.',
            'gia_ban.min'          => 'Giá bán phải lớn hơn hoặc bằng 0.',
            'tinh_trang.required'  => 'Vui lòng chọn tình trạng.',
        ];
    }
}

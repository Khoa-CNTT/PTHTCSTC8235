<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemDichVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dv'        => 'required|string|max:255',
            'hinh_anh'      => 'required|string|max:255',
            'phan_loai_kg'  => 'required|boolean',
            'can_nang_min'  => 'nullable|required_if:phan_loai_kg,1|numeric|min:0',
            'can_nang_max'  => 'nullable|required_if:phan_loai_kg,1|numeric|gte:can_nang_min',
            'mo_ta'         => 'required|string|max:500',
            'id_loaidv'     => 'required|exists:loai_dich_vus,id',
            'gia'           => 'required|numeric|min:0',
            'tinh_trang'    => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_dv.required'          => 'Tên dịch vụ không được để trống.',
            'hinh_anh.required'        => 'Hình ảnh không được để trống.',
            'phan_loai_kg.required'    => 'Vui lòng chọn phân loại cân nặng.',
            'phan_loai_kg.boolean'     => 'Phân loại cân nặng không hợp lệ.',
            'can_nang_min.required_if' => 'Cân nặng tối thiểu bắt buộc khi có phân loại.',
            'can_nang_max.required_if' => 'Cân nặng tối đa bắt buộc khi có phân loại.',
            'can_nang_min.numeric'     => 'Cân nặng tối thiểu phải là số.',
            'can_nang_max.numeric'     => 'Cân nặng tối đa phải là số.',
            'can_nang_max.gte'         => 'Cân nặng tối đa phải lớn hơn hoặc bằng tối thiểu.',
            'mo_ta.required'           => 'Mô tả không được để trống.',
            'id_loaidv.required'       => 'Vui lòng chọn phân loại dịch vụ.',
            'id_loaidv.exists'         => 'Phân loại dịch vụ không tồn tại.',
            'gia.required'             => 'Giá không được để trống.',
            'gia.numeric'              => 'Giá phải là số.',
            'gia.min'                  => 'Giá phải lớn hơn hoặc bằng 0.',
            'tinh_trang.required'      => 'Tình trạng là bắt buộc.',
            'tinh_trang.in'            => 'Tình trạng không hợp lệ.',
        ];
    }
}

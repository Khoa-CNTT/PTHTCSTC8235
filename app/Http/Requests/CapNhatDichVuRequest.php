<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatDichVuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dv'        => 'required|string|max:255',
            'hinh_anh'      => 'nullable|string|max:255',
            'phan_loai_kg'  => 'required|boolean',
            'can_nang_min'  => 'nullable|numeric|required_if:phan_loai_kg,1',
            'can_nang_max'  => 'nullable|numeric|required_if:phan_loai_kg,1|gte:can_nang_min',
            'mo_ta'         => 'nullable|string',
            'id_loaidv'     => 'required|exists:loai_dich_vus,id',
            'gia'           => 'required|numeric|min:0',
            'tinh_trang'    => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_dv.required'           => 'Không được để trống tên dịch vụ',
            'ten_dv.max'                => 'Tên dịch vụ không được vượt quá 255 ký tự',
            'hinh_anh.max'              => 'Đường dẫn hình ảnh không được vượt quá 255 ký tự',
            'phan_loai_kg.required'     => 'Vui lòng chọn phân loại cân nặng',
            'phan_loai_kg.boolean'      => 'Phân loại cân nặng phải là có hoặc không',
            'can_nang_min.required_if'  => 'Cân nặng từ là bắt buộc khi chọn phân loại cân nặng',
            'can_nang_min.numeric'      => 'Cân nặng từ phải là số',
            'can_nang_max.required_if'  => 'Cân nặng đến là bắt buộc khi chọn phân loại cân nặng',
            'can_nang_max.numeric'      => 'Cân nặng đến phải là số',
            'can_nang_max.gte'          => 'Cân nặng đến phải lớn hơn hoặc bằng cân nặng từ',
            'id_loaidv.required'        => 'Vui lòng chọn phân loại dịch vụ',
            'id_loaidv.exists'          => 'Phân loại dịch vụ không hợp lệ',
            'gia.required'              => 'Giá dịch vụ không được để trống',
            'gia.numeric'               => 'Giá dịch vụ phải là số',
            'gia.min'                   => 'Giá dịch vụ không được nhỏ hơn 0',
            'tinh_trang.required'       => 'Vui lòng chọn tình trạng',
            'tinh_trang.boolean'        => 'Tình trạng phải là hoạt động hoặc tạm dừng',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CapNhatPetKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_pet'    => 'required|string|max:100',
            'hinh_anh'   => 'nullable|url|max:255',
            'chung_loai' => 'required|in:0,1',
            'gioi_tinh'  => 'required|in:0,1',
            'tuoi'       => 'required|min:0',
            'can_nang'   => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_pet.required'      => 'Vui lòng nhập tên thú cưng.',
            'hinh_anh.url'          => 'Hình ảnh phải là đường dẫn URL hợp lệ.',
            'chung_loai.required'   => 'Vui lòng chọn chủng loại.',
            'chung_loai.in'         => 'Chủng loại không hợp lệ.',
            'gioi_tinh.required'    => 'Vui lòng chọn giới tính.',
            'gioi_tinh.in'          => 'Giới tính không hợp lệ.',
            'tuoi.required'         => 'Vui lòng nhập tuổi.',
            'tuoi.min'              => 'Tuổi không được âm.',
            'can_nang.required'     => 'Vui lòng nhập cân nặng.',
            'can_nang.numeric'      => 'Cân nặng phải là số.',
            'can_nang.min'          => 'Cân nặng không được âm.',
        ];
    }
}

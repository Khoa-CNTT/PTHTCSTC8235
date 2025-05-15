<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemPetKhachHangRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'ten_pet'   => 'required|string|max:100',
            'chung_loai' => 'required|in:0,1',
            'gioi_tinh' => 'required|in:0,1',
            'hinh_anh'  => 'nullable|url|max:255',
            'tuoi'      => 'required|integer|min:0',
            'can_nang'  => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_pet.required'      => 'Vui lòng nhập tên thú cưng.',
            'chung_loai.required'   => 'Vui lòng chọn chủng loại.',
            'chung_loai.in'         => 'Chủng loại không hợp lệ.',
            'gioi_tinh.required'    => 'Vui lòng chọn giới tính.',
            'gioi_tinh.in'          => 'Giới tính không hợp lệ.',
            'hinh_anh.url'          => 'Hình ảnh phải là một URL hợp lệ.',
            'tuoi.required'         => 'Vui lòng nhập tuổi.',
            'tuoi.integer'          => 'Tuổi phải là một số nguyên.',
            'tuoi.min'              => 'Tuổi không được âm.',
            'can_nang.required'     => 'Vui lòng nhập cân nặng.',
            'can_nang.numeric'      => 'Cân nặng phải là số.',
            'can_nang.min'          => 'Cân nặng không được âm.',
        ];
    }
}

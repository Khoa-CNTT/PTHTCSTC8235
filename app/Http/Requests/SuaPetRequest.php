<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuaPetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id_kh'      => 'required',
            'ten_pet'    => 'required',
            'chung_loai'=> 'required',
            'gioi_tinh'  => 'required',
            'tuoi'       => 'required',
            'hinh_anh'   => 'required',
            'can_nang'   => 'required',
            'tinh_trang'=> 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'id_kh.required'        => 'Vui lòng chọn khách hàng.',
            'ten_pet.required'      => 'Vui lòng nhập tên pet.',
            'chung_loai.required'   => 'Vui lòng chọn chủng loại.',
            'gioi_tinh.required'    => 'Vui lòng chọn giới tính.',
            'tuoi.required'         => 'Vui lòng nhập tuổi của pet.',
            'hinh_anh.required'     => 'Vui lòng chọn hình ảnh cho pet.',
            'can_nang.required'     => 'Vui lòng nhập cân nặng của pet.',
            'tinh_trang.required'   => 'Vui lòng chọn tình trạng.',
        ];
    }
}


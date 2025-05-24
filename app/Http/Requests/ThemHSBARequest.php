<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemHSBARequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
}

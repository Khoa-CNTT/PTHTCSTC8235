<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemChucVuRequest extends FormRequest
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
            'ten_chuc_vu' => 'required|string|max:255|unique:chuc_vus,ten_chuc_vu,' . $this->id,
            'tinh_trang'  => 'required|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'Tên chức vụ không được để trống',
            'ten_chuc_vu.unique'   => 'Tên chức vụ đã tồn tại',
            'ten_chuc_vu.max'      => 'Tên chức vụ tối đa 255 ký tự',
            'tinh_trang.required'  => 'Trạng thái không được để trống',
            'tinh_trang.in'        => 'Trạng thái phải là 0 hoặc 1',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoiMatKhauRequest extends FormRequest
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
            'mat_khau_cu' => 'required',
            'mat_khau_moi' => 'required',
            'xac_nhan_mat_khau' => 'required|same:mat_khau_moi',
        ];
    }
    public function messages()
    {
        return [
            'mat_khau_cu.required' => 'Vui lòng nhập mật khẩu cũ.',
            'mat_khau_moi.required' => 'Vui lòng nhập mật khẩu mới.',
            'xac_nhan_mat_khau.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'xac_nhan_mat_khau.same' => 'Xác nhận mật khẩu không khớp với mật khẩu mới.',
        ];
    }
}

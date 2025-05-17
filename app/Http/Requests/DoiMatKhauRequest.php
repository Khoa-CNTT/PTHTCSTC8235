<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'mat_khau_cu' => ['required', 'min:6'],
            'mat_khau_moi' => [
                'required',
                'min:8',
                'different:mat_khau_cu',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'xac_nhan_mat_khau' => ['required', 'same:mat_khau_moi'],
        ];
    }

    public function messages()
    {
        return [
            'mat_khau_cu.required' => 'Vui lòng nhập mật khẩu cũ.',
            'mat_khau_cu.min' => 'Mật khẩu cũ phải có ít nhất 6 ký tự.',
            
            'mat_khau_moi.required' => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau_moi.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'mat_khau_moi.different' => 'Mật khẩu mới phải khác với mật khẩu cũ.',
            
            'xac_nhan_mat_khau.required' => 'Vui lòng xác nhận mật khẩu mới.',
            'xac_nhan_mat_khau.same' => 'Xác nhận mật khẩu không khớp với mật khẩu mới.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HoaDonRequest extends FormRequest
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
            'id'           => 'required|integer|exists:hoa_dons,id',
            'id_nv'        => 'required|integer|exists:nhan_viens,id',
            'phuong_thuc'  => 'required|in:0,1',
            'tien_kham'    => 'nullable|numeric|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'id.required'           => 'Thiếu ID hóa đơn.',
            'id.integer'            => 'ID hóa đơn không hợp lệ.',
            'id.exists'             => 'Hóa đơn không tồn tại.',

            'id_nv.required'        => 'Bạn chưa chọn nhân viên thu tiền.',
            'id_nv.integer'         => 'ID nhân viên không hợp lệ.',
            'id_nv.exists'          => 'Nhân viên không tồn tại.',

            'phuong_thuc.required' => 'Chưa chọn phương thức thanh toán.',
            'phuong_thuc.in'       => 'Phương thức thanh toán không hợp lệ.',

            'tien_kham.numeric'     => 'Tiền khám phải là số.',
            'tien_kham.min'         => 'Tiền khám không được âm.',
        ];
    }
}

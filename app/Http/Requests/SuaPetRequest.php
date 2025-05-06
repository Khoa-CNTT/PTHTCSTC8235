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
            'id_kh' => 'required|exists:khach_hang,id',  // Kiểm tra xem id_kh có tồn tại trong bảng khách hàng
            'ten_pet' => 'required|string|max:255',  // Tên pet bắt buộc và có độ dài tối đa
            'chung_loai' => 'required|in:0,1',  // Chủng loại phải là '0' (Mèo) hoặc '1' (Chó)
            'gioi_tinh' => 'required|in:0,1',  // Giới tính phải là '0' (Đực) hoặc '1' (Cái)
            'tuoi' => 'required|integer|min:0',  // Tuổi bắt buộc và là số nguyên dương
            'hinh_anh' => 'nullable|string|max:255',  // Hình ảnh có thể null nhưng nếu có phải là chuỗi có độ dài tối đa
            'can_nang' => 'nullable|numeric|min:0',  // Cân nặng có thể null nhưng nếu có phải là số dương
            'tinh_trang' => 'required|in:0,1',  // Tình trạng bắt buộc và có giá trị '0' (Đã khám) hoặc '1' (Chưa khám)
        ];
    }
    public function messages(): array
    {
        return [
            'id_kh.required' => 'Vui lòng chọn khách hàng.',
            'id_kh.exists' => 'Khách hàng không tồn tại.',
            'ten_pet.required' => 'Vui lòng nhập tên pet.',
            'ten_pet.max' => 'Tên pet không được vượt quá 255 ký tự.',
            'chung_loai.required' => 'Vui lòng chọn chủng loại.',
            'chung_loai.in' => 'Chủng loại không hợp lệ.',
            'gioi_tinh.required' => 'Vui lòng chọn giới tính.',
            'gioi_tinh.in' => 'Giới tính không hợp lệ.',
            'tuoi.required' => 'Vui lòng nhập tuổi của pet.',
            'tuoi.integer' => 'Tuổi phải là số nguyên.',
            'hinh_anh.max' => 'Độ dài hình ảnh không được vượt quá 255 ký tự.',
            'can_nang.numeric' => 'Cân nặng phải là một số.',
            'can_nang.min' => 'Cân nặng phải lớn hơn hoặc bằng 0.',
            'tinh_trang.required' => 'Vui lòng chọn tình trạng.',
            'tinh_trang.in' => 'Tình trạng không hợp lệ.',
        ];
    }
}


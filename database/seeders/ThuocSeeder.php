<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThuocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thuocs')->delete();

        DB::table('thuocs')->insert([
            ['ten_thuoc' => 'Paracetamol 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm đau, hạ sốt thường dùng.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Amoxicillin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm penicillin, trị viêm nhiễm.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Vitamin C 1000mg', 'don_vi' => 'viên sủi', 'mo_ta' => 'Bổ sung vitamin C, tăng sức đề kháng.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Hydrocortisone 1%', 'don_vi' => 'tuýp', 'mo_ta' => 'Thuốc bôi chống viêm da nhẹ.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Omeprazole 20mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc điều trị dạ dày, ức chế tiết acid.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Loratadine 10mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc chống dị ứng không gây buồn ngủ.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Cefuroxime 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm cephalosporin.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Prednisolone 5mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc kháng viêm corticosteroid.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Metformin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Điều trị tiểu đường type 2.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Aspirin 81mg', 'don_vi' => 'viên', 'mo_ta' => 'Chống đông máu, dự phòng tai biến.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Ibuprofen 400mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm đau, chống viêm.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Ranitidine 150mg', 'don_vi' => 'viên', 'mo_ta' => 'Điều trị trào ngược dạ dày.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Salbutamol 2mg', 'don_vi' => 'viên', 'mo_ta' => 'Giãn phế quản, điều trị hen.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Clorpheniramine 4mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc kháng histamin giảm dị ứng.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Calcium + D3', 'don_vi' => 'viên', 'mo_ta' => 'Bổ sung canxi và vitamin D.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Azithromycin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh phổ rộng.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Diclofenac 50mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc chống viêm không steroid.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Lansoprazole 30mg', 'don_vi' => 'viên', 'mo_ta' => 'Giảm acid dạ dày, điều trị loét.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Magie B6', 'don_vi' => 'viên', 'mo_ta' => 'Chống mệt mỏi, giảm chuột rút.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Clarithromycin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm macrolid.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Ferrovit', 'don_vi' => 'viên nang', 'mo_ta' => 'Bổ sung sắt và acid folic.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Dextromethorphan 15mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm ho khô.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Bromhexin 8mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc long đờm.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Telfast 180mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng histamin trị dị ứng.', 'tinh_trang' => 1],
            ['ten_thuoc' => 'Panadol Extra', 'don_vi' => 'viên', 'mo_ta' => 'Giảm đau, hạ sốt có cafein.', 'tinh_trang' => 1],
        ]);
    }
}

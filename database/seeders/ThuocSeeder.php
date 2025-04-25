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
    ['id'=>1,'ten_thuoc' => 'Paracetamol 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm đau, hạ sốt thường dùng.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>2,'ten_thuoc' => 'Amoxicillin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm penicillin, trị viêm nhiễm.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>3,'ten_thuoc' => 'Vitamin C 1000mg', 'don_vi' => 'viên sủi', 'mo_ta' => 'Bổ sung vitamin C, tăng sức đề kháng.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>4,'ten_thuoc' => 'Hydrocortisone 1%', 'don_vi' => 'tuýp', 'mo_ta' => 'Thuốc bôi chống viêm da nhẹ.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>5,'ten_thuoc' => 'Omeprazole 20mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc điều trị dạ dày, ức chế tiết acid.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>6,'ten_thuoc' => 'Loratadine 10mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc chống dị ứng không gây buồn ngủ.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>7,'ten_thuoc' => 'Cefuroxime 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm cephalosporin.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>8,'ten_thuoc' => 'Prednisolone 5mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc kháng viêm corticosteroid.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>9,'ten_thuoc' => 'Metformin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Điều trị tiểu đường type 2.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>10,'ten_thuoc' => 'Aspirin 81mg', 'don_vi' => 'viên', 'mo_ta' => 'Chống đông máu, dự phòng tai biến.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>11,'ten_thuoc' => 'Ibuprofen 400mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm đau, chống viêm.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>12,'ten_thuoc' => 'Ranitidine 150mg', 'don_vi' => 'viên', 'mo_ta' => 'Điều trị trào ngược dạ dày.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>13,'ten_thuoc' => 'Salbutamol 2mg', 'don_vi' => 'viên', 'mo_ta' => 'Giãn phế quản, điều trị hen.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>14,'ten_thuoc' => 'Clorpheniramine 4mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc kháng histamin giảm dị ứng.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>15,'ten_thuoc' => 'Calcium + D3', 'don_vi' => 'viên', 'mo_ta' => 'Bổ sung canxi và vitamin D.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>16,'ten_thuoc' => 'Azithromycin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh phổ rộng.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>17,'ten_thuoc' => 'Diclofenac 50mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc chống viêm không steroid.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>18,'ten_thuoc' => 'Lansoprazole 30mg', 'don_vi' => 'viên', 'mo_ta' => 'Giảm acid dạ dày, điều trị loét.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>19,'ten_thuoc' => 'Magie B6', 'don_vi' => 'viên', 'mo_ta' => 'Chống mệt mỏi, giảm chuột rút.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>20,'ten_thuoc' => 'Clarithromycin 500mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng sinh nhóm macrolid.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>21,'ten_thuoc' => 'Ferrovit', 'don_vi' => 'viên nang', 'mo_ta' => 'Bổ sung sắt và acid folic.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>22,'ten_thuoc' => 'Dextromethorphan 15mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc giảm ho khô.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>23,'ten_thuoc' => 'Bromhexin 8mg', 'don_vi' => 'viên', 'mo_ta' => 'Thuốc long đờm.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>24,'ten_thuoc' => 'Telfast 180mg', 'don_vi' => 'viên', 'mo_ta' => 'Kháng histamin trị dị ứng.', 'gia_ban' => null, 'tinh_trang' => 1],
    ['id'=>25,'ten_thuoc' => 'Panadol Extra', 'don_vi' => 'viên', 'mo_ta' => 'Giảm đau, hạ sốt có cafein.', 'gia_ban' => null, 'tinh_trang' => 1],
]);

    }
}

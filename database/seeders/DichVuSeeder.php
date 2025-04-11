<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dich_vus')->delete();
        DB::table('dich_vus')->insert([
            [
                'id' => 1,
                'ten_dv' => 'Tiêm 5 bệnh cho chó',
                'id_loaidv' => 1, // Tiêm chủng
                'hinh_anh' => 'https://tropicpet.vn/wp-content/uploads/2025/02/vacxin-5-benh-o-cho.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Tiêm phòng các bệnh phổ biến ở chó: Care, Parvo, ho cũi chó, viêm gan, leptospira.',
                'gia' => 250000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 2,
                'ten_dv' => 'Spa dưỡng lông toàn thân cho mèo',
                'id_loaidv' => 2, // Spa
                'hinh_anh' => 'https://phongkhamthuythithipet.com/wp-content/uploads/2024/07/dich-vu-cham-soc-lam-dep-cho-thu-cung.jpg',
                'phan_loai_kg' => 1,
                'can_nang_min' => 2,
                'can_nang_max' => 6,
                'mo_ta' => 'Chăm sóc lông, cắt móng, vệ sinh tai và massage thư giãn toàn thân cho mèo.',
                'gia' => 300000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 3,
                'ten_dv' => 'Gửi thú cưng trong ngày',
                'id_loaidv' => 3, // Gửi thú cưng
                'hinh_anh' => 'https://tropicpet.vn/wp-content/uploads/2024/10/khach-san-thu-cung-1.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Dịch vụ gửi thú cưng an toàn, có chỗ ngủ riêng, ăn uống và vệ sinh đúng giờ trong ngày.',
                'gia' => 120000,
                'tinh_trang' => 0,
            ],
            [
                'id' => 4,
                'ten_dv' => 'Khám tổng quát định kỳ',
                'id_loaidv' => 4, // Khám bệnh
                'hinh_anh' => 'https://dreampet.com.vn/wp-content/uploads/2020/11/bac-si-thu-y-Hoan-Kiem-3.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Kiểm tra toàn thân: mắt, tai, da, răng, tim phổi và đo thân nhiệt cho thú cưng.',
                'gia' => 180000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 5,
                'ten_dv' => 'Khám tổng quát thú cưng định kỳ',
                'id_loaidv' => 4, // Khám bệnh
                'hinh_anh' => 'https://pethealthcentre.vn/upload/images/2(4).png',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Khám toàn thân định kỳ: kiểm tra mắt, tai, da, răng và tim phổi.',
                'gia' => 180000,
                'tinh_trang' => 0,
            ],
            [
                'id' => 6,
                'ten_dv' => 'Khám tiêu hoá cho thú cưng',
                'id_loaidv' => 4, // Khám bệnh
                'hinh_anh' => 'https://thuyypethospital.com/upload/news/kham-va-dieu-tri-benh-ly-phuc-tap-y-pet-2380.png',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Thăm khám và tư vấn các vấn đề tiêu hoá như chướng bụng, tiêu chảy, biếng ăn.',
                'gia' => 200000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 7,
                'ten_dv' => 'Gửi thú cưng qua đêm',
                'id_loaidv' => 3,
                'hinh_anh' => 'https://shopvatnuoi.vn/wp-content/uploads/2025/01/dich-vu-trong-giu-thu-cung-ha-noi-uy-tin-trach-nhiem-shovatnuoi.vn-nai-pet-6-1024x876.png',
                'phan_loai_kg' => 1,
                'can_nang_min' => 0,
                'can_nang_max' => 10,
                'mo_ta' => 'Dịch vụ gửi thú cưng qua đêm, bao gồm chỗ ngủ riêng, theo dõi và ăn uống đầy đủ.',
                'gia' => 200000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 8,
                'ten_dv' => 'Tiêm ngừa dại cho mèo',
                'id_loaidv' => 1,
                'hinh_anh' => 'https://www.chamsocpet.com/wp-content/uploads/2020/10/thong-tin-can-luu-y-ve-tiem-phong-meo-4.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Tiêm ngừa bệnh dại định kỳ cho mèo, giúp bảo vệ sức khỏe toàn diện.',
                'gia' => 180000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 9,
                'ten_dv' => 'Tắm khử mùi cho mèo',
                'id_loaidv' => 2,
                'hinh_anh' => 'https://petservicehcm.com/wp-content/uploads/2024/07/tim-nguoi-tam-cho-meo-tan-noi-gan-day-3.jpg',
                'phan_loai_kg' => 1,
                'can_nang_min' => 0,
                'can_nang_max' => 5,
                'mo_ta' => 'Tắm bằng sữa tắm dịu nhẹ, khử mùi, chống rụng lông cho mèo lông ngắn.',
                'gia' => 140000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 10,
                'ten_dv' => 'Vệ sinh tai và cắt móng',
                'id_loaidv' => 2,
                'hinh_anh' => 'https://www.petmart.vn/wp-content/uploads/2016/09/kem-cat-bam-mong-cho-meo-paw-safety-guard-clipper1.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Dịch vụ vệ sinh tai, loại bỏ ráy tai và cắt móng an toàn cho thú cưng.',
                'gia' => 100000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 11,
                'ten_dv' => 'Gói spa dưỡng lông chuyên sâu',
                'id_loaidv' => 2,
                'hinh_anh' => 'https://peticon.edu.vn/wp-content/uploads/2023/12/spa-1-1024x768.jpeg',
                'phan_loai_kg' => 1,
                'can_nang_min' => 5,
                'can_nang_max' => 15,
                'mo_ta' => 'Dưỡng bóng, chải rối và phục hồi lông hư tổn cho chó mèo .',
                'gia' => 230000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 12,
                'ten_dv' => 'Tiêm phòng 7 bệnh cho chó (7in1)',
                'id_loaidv' => 1,
                'hinh_anh' => 'https://medlatec.vn/media/1948/content/20230327_tiem-phong-cho-cho.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Gói tiêm phòng mở rộng cho chó gồm 7 bệnh, tăng hiệu quả bảo vệ toàn diện.',
                'gia' => 300000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 13,
                'ten_dv' => 'Tiêm phòng bệnh FVRCP cho mèo',
                'id_loaidv' => 1,
                'hinh_anh' => 'https://benhvienthuyanipet.com/wp-content/uploads/2021/10/tiem-phong-vacxin-thu-cung.jpg',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Gói tiêm phòng FVRCP giúp bảo vệ mèo khỏi cúm mèo, viêm mũi, viêm ruột và các bệnh hô hấp.',
                'gia' => 240000,
                'tinh_trang' => 1,
            ],
            [
                'id' => 14,
                'ten_dv' => 'Tiêm phòng FeLV (bệnh bạch cầu mèo)',
                'id_loaidv' => 1,
                'hinh_anh' => 'https://vinpet.vn/files/assets/tiem_vacine_meo.png',
                'phan_loai_kg' => 0,
                'can_nang_min' => null,
                'can_nang_max' => null,
                'mo_ta' => 'Tiêm phòng virus bạch cầu ở mèo – đặc biệt cần thiết cho mèo sống cộng đồng hoặc nuôi nhiều con.',
                'gia' => 220000,
                'tinh_trang' => 1,
            ],

        ]);
    }
}

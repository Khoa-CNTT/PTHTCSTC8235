<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pets')->delete();
        DB::table('pets')->insert([
            [
                'id' => 1,
                'id_kh' => 1,
                'ten_pet' => 'Chó Corgi Đực Mỹ',
                'chung_loai' => 0,
                'gioi_tinh' => 1,
                'tuoi' => '11 tháng',
                'hinh_anh' => 'https://www.waggingtonpost.com/wp-content/uploads/sites/699/2018/06/corgi-face.jpg',
                'can_nang' => '10',
                'tinh_trang' => 1,
            ],
            [
                'id' => 2,
                'id_kh' => 2,
                'ten_pet' => 'Chó Poodle Đực',
                'chung_loai' => 0,
                'gioi_tinh' => 1,
                'tuoi' => '5 tháng',
                'hinh_anh' => 'https://cdn.eva.vn/upload/3-2022/images/2022-07-29/image14-1659059504-4-width2048height1365.jpg',
                'can_nang' => '5',
                'tinh_trang' => 1,
            ],
            [
                'id' => 3,
                'id_kh' => 3,
                'ten_pet' => 'Mèo Anh Lông Ngắn',
                'chung_loai' => 1,
                'gioi_tinh' => 0,
                'tuoi' => '8 tháng',
                'hinh_anh' => 'https://image-us.eva.vn/upload/3-2022/images/2022-08-12/image10-1660292104-351-width700height808.jpg',
                'can_nang' => '5',
                'tinh_trang' => 1,
            ],
            [
                'id' => 4,
                'id_kh' => 1,
                'ten_pet' => 'Mèo Ba Tư',
                'chung_loai' => 1,
                'gioi_tinh' => 0,
                'tuoi' => '2 tháng',
                'hinh_anh' => 'https://i.pinimg.com/originals/07/00/6f/07006f89f5908dfe877cfbedeb0c3f76.jpg',
                'can_nang' => '3',
                'tinh_trang' => 1,
            ],
            [
                'id' => 5,
                'id_kh' => 2,
                'ten_pet' => 'Chó Golden Đực',
                'chung_loai' => 0,
                'gioi_tinh' => 1,
                'tuoi' => '1 năm',
                'hinh_anh' => 'https://static.chotot.com/storage/chotot-kinhnghiem/c2c/2019/09/cho-golden-10-e1567509617864.jpg',
                'can_nang' => '20',
                'tinh_trang' => 1,
            ],
            [
                'id' => 6,
                'id_kh' => 2,
                'ten_pet' => 'Chó Alaska',
                'chung_loai' => 0,
                'gioi_tinh' => 1,
                'tuoi' => '3 tháng',
                'hinh_anh' => 'https://opet.com.vn/wp-content/uploads/2022/07/alaska-thuan-chung-1.jpg',
                'can_nang' => '30',
                'tinh_trang' => 1,
            ],
            [
                'id' => 7,
                'id_kh' => 4,
                'ten_pet' => 'Hanni',
                'chung_loai' => 0,
                'gioi_tinh' => 1,
                'tuoi' => '20',
                'hinh_anh' => 'https://thaka.bing.com/th/id/OIP.NLohwCzr94_N6leoL4NckAHaJ3?pid=ImgDet&w=474&h=631&rs=1',
                'can_nang' => '54',
                'tinh_trang' => 1,
            ],
            [
                'id' => 8,
                'id_kh' => 4,
                'ten_pet' => 'Mỹ Diệu',
                'chung_loai' => 1,
                'gioi_tinh' => 1,
                'tuoi' => '2',
                'hinh_anh' => 'https://kenh14cdn.com/203336854389633024/2023/3/6/photo-13-1678089401776337463298.jpeg',
                'can_nang' => '5',
                'tinh_trang' => 1,
            ],
            [
                'id' => 9,
                'id_kh' => 5,
                'ten_pet' => 'Diamond',
                'chung_loai' => 1,
                'gioi_tinh' => 1,
                'tuoi' => '2',
                'hinh_anh' => 'https://kenh14cdn.com/203336854389633024/2023/3/6/photo-13-1678089401776337463298.jpeg',
                'can_nang' => '5',
                'tinh_trang' => 1,
            ],
        ]);
    }
}

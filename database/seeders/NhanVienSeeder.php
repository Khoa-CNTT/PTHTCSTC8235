<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nhan_viens')->delete();
        DB::table('nhan_viens')->insert([
            [
                'id' => 1,
                'ten_nv' => 'Nguyễn Thu Thảo',
                'gioi_tinh' => 0,
                'password' => '123456',
                'hinh_anh' => 'https://shriram-college.com/wp-content/uploads/2022/08/49a-716x1000-716x1000.jpg',
                'email' => 'nguyenthao789@gmail.com',
                'tien_kham' => 200000,
                'mo_ta' => 'Bác sĩ Thảo nổi bật với khả năng tư vấn về chế độ ăn uống, quản lý cân nặng và phòng ngừa bệnh qua dinh dưỡng. Cô cũng là người sáng tạo các khóa học chăm sóc thú cưng online.',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
            ],
            [
                'id' => 2,
                'ten_nv' => 'Phạm Thị Thu Hà',
                'gioi_tinh' => 0,
                'password' => '123456',
                'hinh_anh' => 'https://newkit.moxcreative.com/neermala/wp-content/uploads/sites/36/2022/10/doctor_0007_Layer-1.jpg',
                'email' => 'phamha123@gmail.com',
                'tien_kham' => 220000,
                'mo_ta' => 'Tốt nghiệp Đại học Nông Lâm TP.HCM, bác sĩ Thu Hà có thế mạnh trong việc điều trị các bệnh lý nội khoa như tiêu hóa, hô hấp và da liễu cho chó mèo.',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
            ],
            [
                'id' => 3,
                'ten_nv' => 'Huỳnh Nguyễn Cao Đức',
                'gioi_tinh' => 1,
                'password' => '123456',
                'hinh_anh' => 'https://img.freepik.com/premium-photo/asian-woman-healthcare-professional-transparent-white-iso-white-background-white-background-hd-pho_873925-969336.jpg',
                'email' => 'caoduc456@gmail.com',
                'tien_kham' => 250000,
                'mo_ta' => 'Bác sĩ Đức sử dụng thành thạo các thiết bị hiện đại như máy siêu âm Doppler, máy X-quang kỹ thuật số và kính hiển vi xét nghiệm. Anh đặc biệt giỏi phát hiện các bệnh lý gan, thận, tử cung ở chó mèo.',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
            ],
            [
                'id' => 4,
                'ten_nv' => 'Lương Văn Ái',
                'gioi_tinh' => 1,
                'password' => '123456',
                'hinh_anh' => 'https://thumbs.dreamstime.com/b/beautiful-asian-american-doctor-nurse-posing-stethoscope-isolated-white-background-asian-american-doctor-nurse-105504245.jpg',
                'email' => 'vanAi999@gmail.com',
                'tien_kham' => 300000,
                'mo_ta' => 'Nhiệt tình, nhạy bén và dày dạn kinh nghiệm, bác sĩ Ái chuyên cấp cứu thú cưng trong các tình huống khẩn cấp như ngộ độc, sinh khó, tai nạn…',
                'tinh_trang' => 1,
                'id_chucvu' => 1,
            ],
            [
                'id' => 5,
                'ten_nv' => 'Nguyễn Thị Mai',
                'gioi_tinh' => 0,
                'password' => '123456',
                'hinh_anh' => 'https://p.globalsources.com/IMAGES/PDT/B5733256141/hospital-uniforms-scrub-uniform-hospital.jpg',
                'email' => 'mainguyen@gmail.com',
                'tien_kham' => '100.000',
                'mo_ta' => 'Chuyên viên cắt tỉa lông thú cưng với hơn 3 năm kinh nghiệm, tạo kiểu theo xu hướng Hàn Quốc, Nhật Bản và các giống chó cảnh nổi tiếng.',
                'tinh_trang' => 1,
                'id_chucvu' => 2,
            ],
            [
                'id' => 6,
                'ten_nv' => 'Trần Gia Huy',
                'gioi_tinh' => 1,
                'password' => '123456',
                'hinh_anh' => 'https://white-coat-manila.s3-ap-southeast-1.amazonaws.com/images/shop/products/1659425101128whitecoatmanila-3-pocket-scrub-top-men/1659425101416color1-i0_1png.png',
                'email' => 'hantran@gmail.com',
                'tien_kham' => '200.000',
                'mo_ta' => 'Tận tâm và khéo léo, chuyên viên spa thú cưng thực hiện các bước tắm gội, sấy khô, vệ sinh tai, cắt móng bằng sản phẩm chuyên dụng an toàn.',
                'tinh_trang' => 1,
                'id_chucvu' => 2,
            ],
            [
                'id' => 7,
                'ten_nv' => 'Lê Tuấn Kiệt',
                'gioi_tinh' => 0,
                'password' => '123456',
                'hinh_anh' => 'https://white-coat-manila.s3-ap-southeast-1.amazonaws.com/images/shop/products/1607933918578suititupmanila-3-pocket-movetech-scrub-top-preorder/1607933918719color1-i0_cerulean-3pocket-scrubtop-01jpg.jpeg',
                'email' => 'tuan.kiet@gmail.com',
                'tien_kham' => '120.000',
                'mo_ta' => 'Phụ trách các gói spa toàn diện: tắm thảo dược, xịt khử mùi, dưỡng ẩm da, giúp thú cưng thơm tho, thư giãn và giảm stress.',
                'tinh_trang' => 1,
                'id_chucvu' => 2,
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\LoaiDichVu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiDichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('loai_dich_vus')->delete();
        DB::table('loai_dich_vus')->insert([
            [
                'id'    => 1,
                'ten_loaidv' =>'Tiêm chủng',
                'mo_ta' =>'Dịch vụ tiêm phòng giúp thú cưng được bảo vệ khỏi các bệnh truyền nhiễm phổ biến. Được thực hiện bởi bác sĩ thú y với quy trình an toàn và chính xác.',
            ],
            [
                'id'    => 2,
                'ten_loaidv' =>'Spa',
                'mo_ta' =>'Gói spa toàn diện bao gồm tắm, sấy, massage và dưỡng lông chuyên sâu. Giúp thú cưng thư giãn, sạch sẽ và thơm tho.   ',
            ],
            [
                'id'    => 3,
                'ten_loaidv' =>'Gửi thú cưng',
                'mo_ta' =>'Dịch vụ lưu trú an toàn, sạch sẽ cho thú cưng khi chủ vắng nhà. Có nhân viên chăm sóc và theo dõi 24/7, giúp bạn an tâm tuyệt đối.',
            ],
            [
                'id'    => 4,
                'ten_loaidv' =>'Khám bệnh',
                'mo_ta' =>'Kiểm tra sức khỏe toàn thân cho thú cưng bởi bác sĩ có kinh nghiệm. Phát hiện sớm các dấu hiệu bệnh lý và tư vấn chăm sóc phù hợp.',
            ],

        ]);
    }
}

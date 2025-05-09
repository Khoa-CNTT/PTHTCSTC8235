<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DichVu;
use App\Models\LichHen;
use App\Models\Pet;
use App\Models\DanhGia;
use App\Models\HoSoBenhAn;
use App\Models\LoaiDichVu;
use App\Models\LichHenPet;
use App\Models\KhachHang;
use App\Models\NhanVien;
use App\Models\ChucVu;
use Exception;

class ChatbotController extends Controller
{
    private $apiKey;
    private $geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';
    private static $conversationHistory = [];

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function query(Request $request)
    {
        try {
            if (empty($this->apiKey)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xin chào! Tôi là trợ lý ảo của PetCare. Hiện tại tôi chưa thể tư vấn thông minh vì chưa được cấu hình API. Bạn có thể liên hệ trực tiếp với phòng khám qua số điện thoại của chúng tôi nhé! 😊'
                ]);
            }

            $validatedData = $request->validate([
                'message' => 'required|string|max:1000',
                'userId' => 'nullable|string',
            ]);

            $question = $validatedData['message'];
            $userId = $validatedData['userId'] ?? 'guest';

            // Save this conversation for context
            if (!isset(self::$conversationHistory[$userId])) {
                self::$conversationHistory[$userId] = [];
            }
            
            // Keep conversation history limited to last 5 exchanges
            if (count(self::$conversationHistory[$userId]) > 6) {
                array_shift(self::$conversationHistory[$userId]);
            }
            
            self::$conversationHistory[$userId][] = ['user' => $question];

            $contextData = [];

            // Simple keyword detection for common questions
            if (preg_match('/(xin chào|hello|hi|chào|hey|xin chao)/ui', strtolower($question))) {
                $response = "Xin chào! Tôi là trợ lý ảo của PetCare. Tôi có thể giúp gì cho bạn? 😊";
                self::$conversationHistory[$userId][] = ['bot' => $response];
                
                return response()->json([
                    'success' => true,
                    'message' => $response
                ]);
            }

            // Get customer information if userId is valid
            if ($userId !== 'guest') {
                $customer = KhachHang::find($userId);
                if ($customer) {
                    $contextData['khach_hang'] = $customer;
                }
            }

            try {
                // Get relevant information based on question
                $this->collectContextData($question, $userId, $contextData);
                
                // Call Gemini API for response
                $response = $this->callGeminiApi($question, $contextData, $userId);
                
                // Save bot response to conversation history
                self::$conversationHistory[$userId][] = ['bot' => $response];

                return response()->json([
                    'success' => true,
                    'message' => $response
                ]);
            } catch (Exception $e) {
                Log::error('Chatbot processing error: ' . $e->getMessage());
                
                // Fallback response for common issues if API call fails
                if (preg_match('/(dịch vụ|giá)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Phòng khám PetCare cung cấp nhiều dịch vụ cho thú cưng như khám bệnh, tiêm phòng, spa, phẫu thuật và cắt tỉa lông. Giá dịch vụ từ 100.000đ đến 5.000.000đ tùy loại. Bạn quan tâm đến dịch vụ cụ thể nào? 🐾"
                    ]);
                }
                
                if (preg_match('/(đặt lịch|hẹn)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Bạn có thể đặt lịch hẹn trực tiếp trên trang web của chúng tôi trong mục 'Đặt lịch'. Sau khi đặt lịch, bạn sẽ nhận được xác nhận qua email hoặc số điện thoại. Nếu cần hỗ trợ đặt lịch, vui lòng gọi số điện thoại của phòng khám. 📅"
                    ]);
                }
                
                if (preg_match('/(bác sĩ|b[aá]c s[iĩ]|b[as][iĩ]|doctor|bác|bs)/ui', $question)) {
                    try {
                        $bacSiChucVu = ChucVu::where('ten_chuc_vu', 'like', '%bác sĩ%')
                            ->orWhere('ten_chuc_vu', 'like', '%y tá%')
                            ->pluck('id')
                            ->toArray();
                            
                        $bacSi = NhanVien::whereIn('id_chucvu', $bacSiChucVu)
                            ->where('tinh_trang', 1)
                            ->with('chuc_vu')
                            ->get();
                            
                        if ($bacSi->count() > 0) {
                            $response = "Phòng khám của chúng tôi có các bác sĩ sau:\n\n";
                            foreach ($bacSi as $bs) {
                                $chucVu = $bs->chuc_vu ? $bs->chuc_vu->ten_chuc_vu : 'Bác sĩ';
                                $response .= "• " . $bs->ten_nv . " - " . $chucVu;
                                if (!empty($bs->mo_ta)) {
                                    $response .= " - " . $bs->mo_ta;
                                }
                                $response .= "\n";
                            }
                            $response .= "\nBạn có thể đặt lịch khám với bác sĩ mình mong muốn thông qua mục đặt lịch nhé! 🩺";
                            
                            return response()->json([
                                'success' => true,
                                'message' => $response
                            ]);
                        }
                    } catch (Exception $e) {
                        Log::error('Error in doctor direct response: ' . $e->getMessage());
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => "Phòng khám của chúng tôi có đội ngũ bác sĩ giàu kinh nghiệm trong lĩnh vực thú y. Bạn có thể xem thông tin chi tiết về các bác sĩ trong mục 'Đội ngũ bác sĩ' trên trang web hoặc đặt lịch khám trực tiếp. 👨‍⚕️"
                    ]);
                }
                
                if (preg_match('/(vaccine|vắc-xin|vắc xin|tiêm phòng|phòng bệnh)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare cung cấp dịch vụ tiêm phòng vaccine cho thú cưng như: vaccine 7 bệnh, vaccine dại, vaccine viêm mũi truyền nhiễm và các loại vaccine khác tùy theo loài và độ tuổi. Nên tiêm vaccine định kỳ theo lịch để đảm bảo sức khỏe cho thú cưng. Bạn có thể đặt lịch tiêm phòng trực tiếp trên web hoặc gọi điện đến phòng khám nhé! 💉"
                    ]);
                }
                
                if (preg_match('/(spa|làm đẹp|cắt tỉa|tắm|vệ sinh|chăm sóc)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare cung cấp các dịch vụ spa và chăm sóc cho thú cưng bao gồm: tắm rửa, vệ sinh tai, cắt móng, tỉa lông theo yêu cầu, massage và các gói làm đẹp tổng hợp. Dịch vụ spa giúp thú cưng của bạn luôn sạch sẽ, khỏe mạnh và xinh đẹp. Bạn muốn đặt lịch spa cho thú cưng không? ✨"
                    ]);
                }
                
                if (preg_match('/(phẫu thuật|mổ|chữa|điều trị|cấp cứu)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare có đội ngũ bác sĩ giàu kinh nghiệm thực hiện các ca phẫu thuật cho thú cưng như: triệt sản, phẫu thuật chỉnh hình, phẫu thuật nội tạng, và các tiểu phẫu khác. Phòng khám được trang bị đầy đủ thiết bị y tế hiện đại đảm bảo an toàn cho thú cưng. Trường hợp khẩn cấp, chúng tôi luôn sẵn sàng cấp cứu 24/7. 🏥"
                    ]);
                }
                
                if (preg_match('/(thuốc|đơn thuốc|toa thuốc|kê đơn|dược phẩm)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare cung cấp các loại thuốc và dược phẩm dành cho thú cưng theo đơn của bác sĩ. Chúng tôi có đầy đủ thuốc đặc trị, kháng sinh, thuốc bổ, vitamin và các chế phẩm chăm sóc sức khỏe khác. Thuốc chỉ được cấp khi có chỉ định của bác sĩ để đảm bảo an toàn cho thú cưng. 💊"
                    ]);
                }
                
                if (preg_match('/(khám tổng quát|khám định kỳ|general checkup|sức khỏe|health)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare khuyến nghị khám sức khỏe tổng quát định kỳ cho thú cưng 6 tháng/lần. Gói khám bao gồm kiểm tra thể trạng, da lông, tai mắt, răng miệng, tim phổi và các chỉ số sức khỏe cơ bản. Đối với thú cưng cao tuổi hoặc có bệnh nền, nên khám 3 tháng/lần. Việc khám định kỳ giúp phát hiện sớm và phòng ngừa các bệnh lý tiềm ẩn. 🩺"
                    ]);
                }
                
                if (preg_match('/(giá|chi phí|phí|thanh toán|bao nhiêu)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Bảng giá dịch vụ tại PetCare:\n• Khám tổng quát: 150.000đ - 300.000đ\n• Tiêm phòng: 150.000đ - 500.000đ/mũi\n• Spa cơ bản: 200.000đ - 500.000đ\n• Spa cao cấp: 400.000đ - 800.000đ\n• Phẫu thuật: 1.000.000đ - 5.000.000đ\n\nGiá có thể thay đổi tùy theo cân nặng, tình trạng và nhu cầu cụ thể của thú cưng. Vui lòng liên hệ trực tiếp để được tư vấn chi tiết. 💰"
                    ]);
                }
                
                if (preg_match('/(bệnh|triệu chứng|dấu hiệu|truyền nhiễm|ký sinh|cấp cứu)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Một số dấu hiệu bệnh lý phổ biến ở thú cưng cần đưa đến bác sĩ ngay:\n• Bỏ ăn, bỏ uống quá 24h\n• Nôn mửa hoặc tiêu chảy kéo dài\n• Khó thở, thở gấp\n• Co giật, mất thăng bằng\n• Sốt cao, bứt rứt\n• Chảy máu bất thường\n• Sưng tấy, tổn thương da\n\nNếu thú cưng của bạn có các triệu chứng trên, hãy đưa đến phòng khám ngay để được cấp cứu và điều trị kịp thời. 🚑"
                    ]);
                }
                
                if (preg_match('/(chó|dog|cún|chó cưng)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare cung cấp đầy đủ dịch vụ cho chó cưng:\n• Khám và điều trị bệnh\n• Tiêm phòng định kỳ\n• Tắm, cắt tỉa lông\n• Phẫu thuật\n• Siêu âm, xét nghiệm\n• Tẩy giun, trị ký sinh\n• Khách sạn thú cưng\n\nMỗi giống chó có đặc điểm và nhu cầu chăm sóc khác nhau. Hãy cho chúng tôi biết giống chó của bạn để được tư vấn chi tiết hơn. 🐕"
                    ]);
                }
                
                if (preg_match('/(mèo|cat|miu|meo|meow)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "PetCare cung cấp các dịch vụ đặc biệt dành cho mèo:\n• Khám chuyên khoa mèo\n• Tiêm phòng 3-in-1, 4-in-1\n• Chữa các bệnh đường hô hấp\n• Phẫu thuật triệt sản\n• Spa, cắt móng an toàn\n• Điều trị bệnh da, ký sinh\n\nMèo cần được chăm sóc khác với chó, đặc biệt về chế độ dinh dưỡng và môi trường. Hãy đặt lịch khám định kỳ cho mèo cưng của bạn nhé! 🐈"
                    ]);
                }
                
                // Thêm tư vấn dinh dưỡng
                if (preg_match('/(dinh dưỡng|thức ăn|đồ ăn|cám|thực phẩm|food|nutrition|ăn uống|chế độ ăn|ăn gì|cho ăn)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Tư vấn dinh dưỡng cho thú cưng tại PetCare:\n• Thú cưng cần được ăn thức ăn phù hợp với độ tuổi, giống và tình trạng sức khỏe\n• Chó cần thức ăn giàu protein, ít ngũ cốc\n• Mèo cần thức ăn giàu taurine và protein động vật\n• Nên cho ăn theo khẩu phần và thời gian cố định\n• Luôn đảm bảo có nước sạch\n• Tránh cho ăn thức ăn cay, mặn, ngọt, socola và nho\n\nPhòng khám chúng tôi có bác sĩ dinh dưỡng có thể tư vấn chế độ ăn phù hợp cho thú cưng của bạn. 🍖"
                    ]);
                }
                
                // Thêm tư vấn nuôi dưỡng thú cưng con
                if (preg_match('/(thú con|chó con|mèo con|puppy|kitten|thú nhỏ|mới sinh|sơ sinh|chăm sóc|cún con|miu con)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Chăm sóc thú cưng con tại PetCare:\n• Tiêm phòng đầy đủ theo lịch (từ 6-8 tuần tuổi)\n• Tẩy giun định kỳ mỗi tháng đến 6 tháng tuổi\n• Cho ăn thức ăn dành riêng cho thú con (puppy/kitten food)\n• Chia nhỏ bữa ăn: 3-4 bữa/ngày\n• Huấn luyện vệ sinh sớm\n• Giữ ấm, tránh môi trường bụi bẩn\n• Không tắm khi quá nhỏ (dưới 2 tháng)\n\nBạn nên đưa thú cưng con đến khám định kỳ mỗi tháng trong 6 tháng đầu để theo dõi sự phát triển. 🐾"
                    ]);
                }
                
                // Thêm tư vấn về triệt sản
                if (preg_match('/(triệt sản|thiến|khử|sterilization|spay|neuter|không sinh sản|cắt trứng|hoạt động sinh dục)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Thông tin về triệt sản thú cưng tại PetCare:\n• Thời điểm lý tưởng: 5-6 tháng tuổi\n• Chi phí: 800.000đ - 1.500.000đ tùy cân nặng và giới tính\n• Thời gian hồi phục: 7-10 ngày\n• Lợi ích: giảm nguy cơ bệnh ung thư, nhiễm trùng tử cung, tinh hoàn, giảm hành vi gây hấn, đánh dấu lãnh thổ, giảm nguy cơ thú cưng đi lạc\n\nTriệt sản là thủ thuật an toàn và được khuyến khích để kiểm soát quần thể thú cưng và cải thiện sức khỏe. PetCare có bác sĩ chuyên khoa và trang thiết bị hiện đại đảm bảo an toàn cho thú cưng. ✂️"
                    ]);
                }
                
                // Thêm tư vấn về huấn luyện
                if (preg_match('/(huấn luyện|training|dạy|tập|vệ sinh|toilet|sủa|cắn|phá|ngoan|phục tùng|lệnh|nghe lời|bướng|nghịch)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Tư vấn huấn luyện thú cưng từ PetCare:\n• Huấn luyện sớm từ 8-12 tuần tuổi\n• Áp dụng phương pháp tích cực, thưởng khi làm đúng\n• Nhất quán trong mệnh lệnh và quy tắc\n• Tập trung vào các lệnh cơ bản: ngồi, nằm, đứng, đến\n• Huấn luyện vệ sinh đúng chỗ\n• Tránh la mắng, đánh đập khi thú cưng làm sai\n• Giữ buổi huấn luyện ngắn (5-15 phút) nhưng thường xuyên\n\nPetCare có kết nối với huấn luyện viên chuyên nghiệp nếu bạn cần hỗ trợ thêm. 🦮"
                    ]);
                }
                
                // Thêm tư vấn về du lịch cùng thú cưng
                if (preg_match('/(du lịch|đi chơi|đi xa|mang theo|pet friendly|khách sạn|nhà nghỉ|đưa đi|travel|vacation)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Du lịch cùng thú cưng - Lời khuyên từ PetCare:\n• Đảm bảo tiêm phòng đầy đủ và mang theo sổ tiêm chủng\n• Chuẩn bị túi sơ cứu cơ bản\n• Kiểm tra trước nơi lưu trú cho phép thú cưng\n• Mang theo thức ăn quen thuộc, đồ chơi yêu thích\n• Sử dụng dây dắt, vòng cổ an toàn\n• Nếu đi xa, cân nhắc chống say xe cho thú\n• Giữ lịch sinh hoạt đều đặn\n\nNếu không thể mang theo thú cưng, PetCare cung cấp dịch vụ trông giữ với không gian thoải mái và chăm sóc chu đáo. 🧳"
                    ]);
                }
                
                // Thêm tư vấn về chăm sóc thú già
                if (preg_match('/(thú già|chó già|mèo già|senior|tuổi cao|cao tuổi|lớn tuổi|già yếu|chăm sóc đặc biệt)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Chăm sóc thú cưng cao tuổi tại PetCare:\n• Khám sức khỏe định kỳ mỗi 3-6 tháng\n• Chế độ ăn đặc biệt cho thú già (ít calo, dễ tiêu hóa)\n• Bổ sung vitamin và khoáng chất\n• Vận động nhẹ nhàng, tránh hoạt động mạnh\n• Giữ ấm vào mùa lạnh\n• Kiểm tra răng miệng thường xuyên\n• Theo dõi thay đổi trong hành vi, ăn uống\n\nThú cưng trên 7 tuổi (chó) hoặc 10 tuổi (mèo) cần được quan tâm đặc biệt. PetCare có gói khám sức khỏe toàn diện dành riêng cho thú cưng cao tuổi. 👵"
                    ]);
                }
                
                // Thêm tư vấn về cấp cứu
                if (preg_match('/(cấp cứu|khẩn cấp|emergency|nguy hiểm|nguy kịch|tai nạn|bị thương|chảy máu|ngộ độc|ngất|hôn mê|co giật)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dịch vụ cấp cứu tại PetCare:\n• Đường dây nóng cấp cứu: 0123.456.789 (24/7)\n• Bác sĩ trực 24/7 sẵn sàng xử lý các trường hợp khẩn cấp\n• Trang thiết bị cấp cứu hiện đại\n• Phòng mổ khẩn luôn sẵn sàng\n\nKhi thú cưng gặp tình trạng khẩn cấp:\n• Giữ bình tĩnh\n• Gọi ngay cho phòng khám\n• Mô tả chi tiết tình trạng\n• Làm theo hướng dẫn sơ cứu từ bác sĩ\n• Đưa thú cưng đến phòng khám càng sớm càng tốt\n\nĐừng tự ý dùng thuốc hay thực hiện các thủ thuật khi chưa có chỉ định từ bác sĩ. 🚑"
                    ]);
                }
                
                // Thêm tư vấn về tiêm phòng dại
                if (preg_match('/(dại|rabies|phòng dại|bệnh dại|chó dại|mèo dại|vắc xin dại|tiem phong dai)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Tiêm phòng dại tại PetCare:\n• Vaccine dại bắt buộc cho tất cả chó, mèo\n• Liều đầu tiên: từ 3 tháng tuổi\n• Tái tiêm: mỗi năm 1 lần\n• Chi phí: 150.000đ - 300.000đ/liều\n• Cấp giấy chứng nhận tiêm phòng\n\nBệnh dại là bệnh nguy hiểm có thể gây tử vong cho cả thú cưng và người. Việc tiêm phòng dại đúng lịch là bắt buộc theo quy định của pháp luật. PetCare sử dụng vaccine chất lượng cao, an toàn và hiệu quả. 💉"
                    ]);
                }
                
                // Thêm tư vấn về tẩy giun, trị ký sinh
                if (preg_match('/(giun|ký sinh|ve|bọ chét|rận|deworming|parasites|tẩy giun|ngoại ký sinh|nội ký sinh|sán|flea|tick)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dịch vụ tẩy giun và trị ký sinh tại PetCare:\n• Tẩy giun định kỳ: 3 tháng/lần\n• Phòng ve, bọ chét: hàng tháng\n• Chi phí tẩy giun: 100.000đ - 300.000đ\n• Chi phí trị ve, bọ chét: 200.000đ - 500.000đ\n\nKý sinh trùng gây nhiều vấn đề sức khỏe cho thú cưng như thiếu máu, suy nhược, tiêu chảy và có thể lây sang người. PetCare cung cấp các sản phẩm tẩy giun, trị ký sinh chất lượng cao, hiệu quả và an toàn. 🪱"
                    ]);
                }
                
                // Thêm tư vấn về chải lông, vệ sinh
                if (preg_match('/(chải lông|vệ sinh|rụng lông|cắt móng|rửa tai|vệ sinh tai|vệ sinh răng|đánh răng|mùi hôi|grooming)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dịch vụ vệ sinh, chải lông tại PetCare:\n• Chải lông toàn diện: 150.000đ - 400.000đ\n• Cắt móng: 60.000đ - 120.000đ\n• Vệ sinh tai: 80.000đ - 150.000đ\n• Đánh răng: 100.000đ - 200.000đ\n• Gói trọn vẹn: 250.000đ - 600.000đ\n\nLời khuyên chăm sóc tại nhà:\n• Chải lông: 2-3 lần/tuần với lông ngắn, hàng ngày với lông dài\n• Vệ sinh tai: 1-2 lần/tuần\n• Cắt móng: 2-4 tuần/lần\n• Đánh răng: tốt nhất là hàng ngày\n\nVệ sinh thường xuyên giúp phát hiện sớm các vấn đề về da, tai, móng và răng miệng. 🧽"
                    ]);
                }
                
                // Thêm tư vấn về xử lý hành vi
                if (preg_match('/(hành vi|cắn|sủa|cào|phá|stress|lo âu|sợ hãi|hung dữ|cô đơn|buồn|behavior|ghen|ghen tị|không nghe lời)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Tư vấn xử lý hành vi thú cưng tại PetCare:\n• Đánh giá hành vi: 350.000đ\n• Tư vấn xử lý hành vi: 500.000đ/buổi\n• Liệu pháp điều chỉnh hành vi: theo gói\n\nCác vấn đề hành vi thường gặp:\n• Sủa quá nhiều, cắn phá đồ đạc\n• Lo âu khi xa chủ\n• Hung dữ với người hoặc thú khác\n• Đi vệ sinh không đúng chỗ\n• Stress, căng thẳng\n\nHầu hết vấn đề hành vi có thể cải thiện qua huấn luyện đúng cách và kiên trì. PetCare có chuyên gia hành vi động vật có thể giúp bạn hiểu và điều chỉnh hành vi của thú cưng. 🧠"
                    ]);
                }
                
                // Thêm điều kiện để nhận diện câu hỏi về điều trị da và lông
                if (preg_match('/(da|lông|nấm|viêm da|ngứa|gãi|rụng lông|hói|nổi cục|skin|fur|hair|dermatology|ghẻ|nấm da|vảy|chàm|dị ứng)/ui', $question)) {
                    return response()->json([
                        'success' => true,
                        'message' => "Dịch vụ điều trị da và lông tại PetCare:\n• Khám da chuyên khoa: 200.000đ\n• Xét nghiệm tìm nguyên nhân: 300.000đ - 800.000đ\n• Điều trị nấm da: 300.000đ - 1.000.000đ\n• Điều trị viêm da: 500.000đ - 2.000.000đ\n• Điều trị dị ứng: 800.000đ - 3.000.000đ\n\nVấn đề về da và lông thường gặp ở thú cưng:\n• Viêm da do dị ứng\n• Nhiễm nấm, vi khuẩn\n• Ve, rận, bọ chét\n• Rụng lông bất thường\n• Nổi u cục trên da\n\nNếu thú cưng bị ngứa liên tục, rụng lông nhiều, da có vảy hoặc đỏ, hãy đưa đến khám ngay để được điều trị kịp thời. 🦙"
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => "Xin lỗi, tôi chỉ hỗ trợ về các vấn đề liên quan đến dịch vụ và đặt lịch hẹn của thú cưng. Những câu hỏi ngoài phạm vi mình không thể hỗ trợ.\n\nBạn có thể đặt lại câu hỏi đúng chủ đề để mình hỗ trợ nhé! 😅"
                ]);
            }
        } catch (Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Xin lỗi, tôi đang gặp chút vấn đề kỹ thuật. Bạn vui lòng thử lại sau nhé! 😅'
            ]);
        }
    }
    
    private function collectContextData($question, $userId, &$contextData)
    {
        // Get service information
        if (preg_match('/(dịch vụ|khám|điều trị|tiêm|phẫu thuật|spa|service|chăm sóc|cắt tỉa|tắm|giá|chi phí|phí|thuốc|tư vấn|tiem|phau thuat)/ui', $question)) {
            // Get services
                $services = DichVu::where('tinh_trang', 1)->get();
            
            // Get service categories if available
            try {
                $serviceCategories = LoaiDichVu::where('tinh_trang', 1)->get();
                $contextData['loai_dich_vu'] = $serviceCategories;
                
                $groupedServices = [];
                
                // Group services by category
                foreach ($serviceCategories as $category) {
                    $groupedServices[$category->id] = [
                        'ten_loai' => $category->ten_loai_dv,
                        'dich_vu' => []
                    ];
                }
                
                foreach ($services as $service) {
                    if (isset($groupedServices[$service->id_loai_dv])) {
                        $groupedServices[$service->id_loai_dv]['dich_vu'][] = $service;
                    }
                }
                
                $contextData['dich_vu'] = [
                    'all' => $services,
                    'grouped' => $groupedServices
                ];
            } catch (Exception $e) {
                // Fallback if LoaiDichVu model or relationship doesn't exist
                Log::error('Error getting service categories: ' . $e->getMessage());
                $contextData['dich_vu'] = [
                    'all' => $services
                ];
            }
        }

        // Get appointment information
        if (preg_match('/(lịch hẹn|đặt lịch|đăng ký|khám|hẹn|cuộc hẹn|đặt khám|thời gian|ngày|giờ)/ui', $question)) {
            try {
                $contextData['lich_hen'] = LichHen::where('tinh_trang', 1)->get();
                
                // Get customer's appointments if user is logged in
                if ($userId !== 'guest') {
                    $petIds = Pet::where('id_kh', $userId)->pluck('id')->toArray();
                    if (!empty($petIds)) {
                        $contextData['lich_hen_pet'] = LichHenPet::whereIn('id_pet', $petIds)
                            ->orderBy('created_at', 'desc')
                            ->get();
                    }
                }
            } catch (Exception $e) {
                Log::error('Error getting appointment info: ' . $e->getMessage());
            }
        }

        // Get pet information
        if (preg_match('/(thú cưng|pet|chó|mèo|dog|cat|thú|cún|miu|boss|hamster|chuột|vật nuôi|thỏ|chim)/ui', $question)) {
            try {
                if ($userId !== 'guest') {
                $contextData['pets'] = Pet::where('id_kh', $userId)->get();
                }
            } catch (Exception $e) {
                Log::error('Error getting pet info: ' . $e->getMessage());
            }
        }

        // Get doctors information
        if (preg_match('/(bác sĩ|b[aá]c s[iĩ]|b[as][iĩ]|doctor|bác|nhân viên|nv|y tá|chuyên môn|kinh nghiệm|chuyên gia|bs|bsi)/ui', $question)) {
            try {
                $bacSiChucVu = ChucVu::where('ten_chuc_vu', 'like', '%bác sĩ%')
                    ->orWhere('ten_chuc_vu', 'like', '%y tá%')
                    ->orWhere('ten_chuc_vu', 'like', '%khám bệnh%')
                    ->pluck('id')
                    ->toArray();
                
                $contextData['bac_si'] = NhanVien::whereIn('id_chucvu', $bacSiChucVu)
                    ->where('tinh_trang', 1)
                    ->with('chuc_vu')
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting doctor info: ' . $e->getMessage());
            }
        }

        // Get medical records
        if (preg_match('/(hồ sơ|bệnh án|lịch sử|khám bệnh|bệnh|triệu chứng|chẩn đoán|kết quả|kết luận|điều trị|thuốc|đơn thuốc|kê đơn)/i', $question)) {
            try {
                if ($userId !== 'guest') {
                    $pets = Pet::where('id_kh', $userId)->pluck('id')->toArray();
                    if (!empty($pets)) {
                        $contextData['health_records'] = HoSoBenhAn::whereIn('id_pet', $pets)
                            ->orderBy('ngay_kham', 'desc')
                            ->get();
                    }
                }
            } catch (Exception $e) {
                Log::error('Error getting health records: ' . $e->getMessage());
            }
        }

        // Get reviews
        if (preg_match('/(đánh giá|review|nhận xét|feedback|phản hồi|ý kiến)/ui', $question)) {
            try {
                $contextData['danh_gia'] = DanhGia::where('tinh_trang', 1)
                    ->orderBy('ngay_tao', 'desc')
                    ->take(5)
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting reviews: ' . $e->getMessage());
            }
        }

        // Thêm thông tin về thuốc
        if (preg_match('/(thuốc|đơn thuốc|thuoc|toa thuốc|kê đơn|dược phẩm|dùng thuốc|liều lượng|medicine|pharmacy|prescription)/ui', $question)) {
            try {
                $contextData['thuoc'] = \App\Models\Thuoc::where('tinh_trang', 1)
                    ->orderBy('ten_thuoc')
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting medicine info: ' . $e->getMessage());
            }
        }
        
        // Thêm thông tin về tiêm phòng vaccine
        if (preg_match('/(vaccine|vắc-xin|vắc xin|tiêm phòng|phòng bệnh|tiem phong|phong benh|mũi tiêm|lịch tiêm)/ui', $question)) {
            try {
                $contextData['vaccine'] = \App\Models\DichVu::where('tinh_trang', 1)
                    ->where(function($query) {
                        $query->where('ten_dv', 'like', '%vaccine%')
                            ->orWhere('ten_dv', 'like', '%tiêm phòng%')
                            ->orWhere('ten_dv', 'like', '%vắc xin%');
                    })
                    ->with('loaiDichVu')
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting vaccine info: ' . $e->getMessage());
            }
        }
        
        // Thêm thông tin về chăm sóc và spa
        if (preg_match('/(spa|làm đẹp|cắt tỉa|tắm|vệ sinh|chăm sóc|grooming|tỉa lông|cắt móng|rửa tai|lông|móng)/ui', $question)) {
            try {
                $contextData['spa'] = \App\Models\DichVu::where('tinh_trang', 1)
                    ->where(function($query) {
                        $query->where('ten_dv', 'like', '%spa%')
                            ->orWhere('ten_dv', 'like', '%tắm%')
                            ->orWhere('ten_dv', 'like', '%cắt%')
                            ->orWhere('ten_dv', 'like', '%tỉa%')
                            ->orWhere('ten_dv', 'like', '%vệ sinh%')
                            ->orWhere('ten_dv', 'like', '%làm đẹp%');
                    })
                    ->with('loaiDichVu')
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting spa info: ' . $e->getMessage());
            }
        }
        
        // Thêm thông tin về phẫu thuật
        if (preg_match('/(phẫu thuật|mổ|chữa|điều trị|cấp cứu|phau thuat|mo|surgery|operation|cắt|trị|thủ thuật)/ui', $question)) {
            try {
                $contextData['phau_thuat'] = \App\Models\DichVu::where('tinh_trang', 1)
                    ->where(function($query) {
                        $query->where('ten_dv', 'like', '%phẫu thuật%')
                            ->orWhere('ten_dv', 'like', '%mổ%')
                            ->orWhere('ten_dv', 'like', '%cắt%')
                            ->orWhere('ten_dv', 'like', '%cấp cứu%');
                    })
                    ->with('loaiDichVu')
                    ->get();
            } catch (Exception $e) {
                Log::error('Error getting surgery info: ' . $e->getMessage());
            }
        }
        
        // Thêm thông tin về giá cả
        if (preg_match('/(giá|chi phí|phí|bảng giá|bao nhiêu|đắt|rẻ|tiền|thanh toán|price|cost|fee)/ui', $question)) {
            try {
                $loaiDichVu = \App\Models\LoaiDichVu::where('tinh_trang', 1)->get();
                $contextData['bang_gia'] = [];
                
                foreach ($loaiDichVu as $loai) {
                    $dichVu = \App\Models\DichVu::where('tinh_trang', 1)
                        ->where('id_loai_dv', $loai->id)
                        ->get();
                        
                    if ($dichVu->count() > 0) {
                        $contextData['bang_gia'][$loai->ten_loai_dv] = $dichVu;
                    }
                }
        } catch (Exception $e) {
                Log::error('Error getting price info: ' . $e->getMessage());
            }
        }
    }

    private function callGeminiApi($question, $contextData, $userId)
    {
        $personality = "Tôi là trợ lý ảo của phòng khám thú y PetCare. Tôi giúp khách hàng tìm hiểu thông tin về dịch vụ, đặt lịch hẹn, theo dõi sức khỏe thú cưng và các vấn đề liên quan đến chăm sóc thú cưng. Tôi luôn trả lời ngắn gọn, dễ hiểu, thân thiện và sử dụng emoji phù hợp.";
        
        $formattedContext = $this->formatContextData($contextData);
        
        // Add conversation history for context
        $conversationHistory = "";
        if (isset(self::$conversationHistory[$userId]) && count(self::$conversationHistory[$userId]) > 1) {
            $conversationHistory = "Lịch sử cuộc trò chuyện gần đây:\n";
            $recentHistory = array_slice(self::$conversationHistory[$userId], -5);
            foreach ($recentHistory as $exchange) {
                if (isset($exchange['user'])) {
                    $conversationHistory .= "Khách hàng: " . $exchange['user'] . "\n";
                } elseif (isset($exchange['bot'])) {
                    $conversationHistory .= "Bot: " . $exchange['bot'] . "\n";
                }
            }
        }
        
        $prompt = "{$personality}\n\n{$conversationHistory}\n\nCâu hỏi hiện tại của khách hàng: {$question}\n\nThông tin từ hệ thống: {$formattedContext}\n\nYêu cầu: trả lời ngắn gọn, tự nhiên, thân thiện như đang trò chuyện, sử dụng emoji phù hợp. Nếu không đủ thông tin thì gợi ý khách hàng cung cấp thêm thông tin hoặc gợi ý chủ đề liên quan. Nếu câu hỏi liên quan đến đặt lịch, hãy gợi ý khách hàng sử dụng chức năng đặt lịch hẹn trên trang web.";
        
        $data = [
            "contents" => [
                ["parts" => [["text" => $prompt]]]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 800,
                "topP" => 0.8,
                "topK" => 40
            ]
        ];
        
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->geminiUrl . '?key=' . $this->apiKey, $data);
        
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    return $responseData['candidates'][0]['content']['parts'][0]['text'];
                }
            }
        
        Log::error('Gemini API error response: ' . $response->body());
        throw new Exception('Failed to get response from Gemini API');
    }

    private function formatContextData($contextData)
    {
        $formatted = "";
        
        if (isset($contextData['khach_hang'])) {
            $kh = $contextData['khach_hang'];
            $formatted .= "\nThông tin khách hàng:\n";
            $formatted .= "- Tên: " . $kh->ho_ten . "\n";
            $formatted .= "- Email: " . $kh->email . "\n";
            $formatted .= "- SĐT: " . $kh->sdt . "\n";
        }
        
        if (isset($contextData['dich_vu'])) {
            $formatted .= "\nDịch vụ:\n";
            if (isset($contextData['dich_vu']['grouped'])) {
                foreach ($contextData['dich_vu']['grouped'] as $category) {
                    $formatted .= "- " . $category['ten_loai'] . ":\n";
                    foreach ($category['dich_vu'] as $service) {
                        $formatted .= "  + " . $service->ten_dv . " (" . number_format($service->gia, 0, ',', '.') . " VNĐ)\n";
                    }
                }
            } else if (isset($contextData['dich_vu']['all'])) {
                foreach ($contextData['dich_vu']['all'] as $service) {
                    $formatted .= "- " . $service->ten_dv . " (" . number_format($service->gia, 0, ',', '.') . " VNĐ)\n";
                }
            }
        }
        
        if (isset($contextData['lich_hen'])) {
            $formatted .= "\nKhung giờ đặt lịch khả dụng:\n";
            foreach ($contextData['lich_hen'] as $ap) {
                $formatted .= "- " . $ap->khung_gio . "\n";
            }
        }
        
        if (isset($contextData['lich_hen_pet'])) {
            $formatted .= "\nLịch hẹn của khách hàng:\n";
            foreach ($contextData['lich_hen_pet'] as $appt) {
                $formatted .= "- ";
                if (isset($appt->pet) && $appt->pet) {
                    $formatted .= "Thú cưng: " . $appt->pet->ten_pet . ", ";
                }
                if (isset($appt->dichVu) && $appt->dichVu) {
                    $formatted .= "Dịch vụ: " . $appt->dichVu->ten_dv . ", ";
                }
                if (isset($appt->lichHen) && $appt->lichHen) {
                    $formatted .= "Thời gian: " . $appt->lichHen->khung_gio . ", ";
                }
                $formatted .= "Trạng thái: " . $this->getStatusText($appt->tinh_trang) . "\n";
            }
        }
        
        if (isset($contextData['pets'])) {
            $formatted .= "\nThú cưng của khách hàng:\n";
            foreach ($contextData['pets'] as $pet) {
                $formatted .= "- Tên: " . $pet->ten_pet;
                if (isset($pet->chung_loai)) {
                    $formatted .= ", Loại: " . $pet->chung_loai;
                }
                if (isset($pet->giong)) {
                    $formatted .= ", Giống: " . $pet->giong;
                }
                if (isset($pet->tuoi)) {
                    $formatted .= ", Tuổi: " . $pet->tuoi;
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['bac_si']) && count($contextData['bac_si']) > 0) {
            $formatted .= "\nDanh sách bác sĩ và nhân viên y tế:\n";
            foreach ($contextData['bac_si'] as $bacSi) {
                $chucVu = $bacSi->chuc_vu ? $bacSi->chuc_vu->ten_chuc_vu : 'Bác sĩ';
                $formatted .= "- " . $bacSi->ten_nv . " (" . $chucVu . ")";
                if (!empty($bacSi->tien_kham)) {
                    $formatted .= " - Phí khám: " . number_format($bacSi->tien_kham, 0, ',', '.') . "đ";
                }
                if (!empty($bacSi->mo_ta)) {
                    $formatted .= " - " . $bacSi->mo_ta;
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['thuoc']) && count($contextData['thuoc']) > 0) {
            $formatted .= "\nDanh sách thuốc và dược phẩm:\n";
            foreach ($contextData['thuoc'] as $thuoc) {
                $formatted .= "- " . $thuoc->ten_thuoc;
                if (!empty($thuoc->cong_dung)) {
                    $formatted .= " (Công dụng: " . $thuoc->cong_dung . ")";
                }
                if (!empty($thuoc->don_vi_tinh)) {
                    $formatted .= " - Đơn vị: " . $thuoc->don_vi_tinh;
                }
                if (!empty($thuoc->gia_ban)) {
                    $formatted .= " - Giá: " . number_format($thuoc->gia_ban, 0, ',', '.') . "đ";
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['vaccine']) && count($contextData['vaccine']) > 0) {
            $formatted .= "\nDịch vụ tiêm phòng và vaccine:\n";
            foreach ($contextData['vaccine'] as $vaccine) {
                $formatted .= "- " . $vaccine->ten_dv . " - " . number_format($vaccine->gia, 0, ',', '.') . "đ";
                if ($vaccine->loaiDichVu) {
                    $formatted .= " (Loại: " . $vaccine->loaiDichVu->ten_loai_dv . ")";
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['spa']) && count($contextData['spa']) > 0) {
            $formatted .= "\nDịch vụ spa và chăm sóc:\n";
            foreach ($contextData['spa'] as $spa) {
                $formatted .= "- " . $spa->ten_dv . " - " . number_format($spa->gia, 0, ',', '.') . "đ";
                if ($spa->loaiDichVu) {
                    $formatted .= " (Loại: " . $spa->loaiDichVu->ten_loai_dv . ")";
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['phau_thuat']) && count($contextData['phau_thuat']) > 0) {
            $formatted .= "\nDịch vụ phẫu thuật và điều trị:\n";
            foreach ($contextData['phau_thuat'] as $pt) {
                $formatted .= "- " . $pt->ten_dv . " - " . number_format($pt->gia, 0, ',', '.') . "đ";
                if ($pt->loaiDichVu) {
                    $formatted .= " (Loại: " . $pt->loaiDichVu->ten_loai_dv . ")";
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['bang_gia']) && !empty($contextData['bang_gia'])) {
            $formatted .= "\nBảng giá dịch vụ theo loại:\n";
            foreach ($contextData['bang_gia'] as $loai => $dichVu) {
                $formatted .= "- " . $loai . ":\n";
                foreach ($dichVu as $dv) {
                    $formatted .= "  + " . $dv->ten_dv . " - " . number_format($dv->gia, 0, ',', '.') . "đ\n";
                }
            }
        }
        
        if (isset($contextData['health_records'])) {
            $formatted .= "\nHồ sơ bệnh án gần đây:\n";
            foreach ($contextData['health_records'] as $rec) {
                $formatted .= "- ";
                if (isset($rec->pet) && $rec->pet) {
                    $formatted .= "Thú cưng: " . $rec->pet->ten_pet . ", ";
                }
                $formatted .= "Ngày khám: " . $rec->ngay_kham . ", Chuẩn đoán: " . $rec->chuan_doan;
                if (isset($rec->nhanVien) && $rec->nhanVien) {
                    $formatted .= ", Bác sĩ: " . $rec->nhanVien->ho_ten;
                }
                $formatted .= "\n";
            }
        }
        
        if (isset($contextData['danh_gia'])) {
            $formatted .= "\nĐánh giá gần đây:\n";
            foreach ($contextData['danh_gia'] as $dg) {
                $formatted .= "- ";
                
                if (isset($dg->khachHang) && $dg->khachHang) {
                    $formatted .= $dg->khachHang->ho_ten;
                } else {
                    $formatted .= "Khách hàng";
                }
                
                $formatted .= " (" . $dg->so_sao . " sao): " . $dg->noi_dung;
                
                if (isset($dg->dichVu) && $dg->dichVu) {
                    $formatted .= " - Dịch vụ: " . $dg->dichVu->ten_dv;
                }
                
                $formatted .= " (" . $dg->ngay_tao . ")\n";
            }
        }
        
        return $formatted;
    }
    
    private function getStatusText($status)
    {
        switch ($status) {
            case 0: return 'Đã hủy';
            case 1: return 'Đã đặt';
            case 2: return 'Đã hoàn thành';
            default: return 'Không xác định';
        }
    }
} 
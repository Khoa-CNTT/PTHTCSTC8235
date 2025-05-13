<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ChatbotLearningService;
use App\Models\ChatbotInteraction;
use App\Models\ChatbotFeedback;
use Exception;

class ChatbotController extends Controller
{
    private $apiKey;
    private $geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    private $learningService;
    private static $conversationHistory = [];

    public function __construct(ChatbotLearningService $learningService)
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->learningService = $learningService;
    }

    public function chat(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string|max:1000',
                'userId' => 'nullable|string',
            ]);

            $question = trim($validatedData['message']);
            $userId = $validatedData['userId'] ?? $this->getGuestId($request);
            
            // Cleanup old guest data periodically
            if (rand(1, 100) === 1) {
                $this->cleanupOldGuestData();
            }

            // Kiểm tra từ khóa cấm
            if ($this->isInappropriateContent($question)) {
                $history = $this->getChatHistory($userId);
                $history[] = ['role' => 'user', 'content' => $question];
                if (count($history) > 10) array_shift($history);
                session(["chatbot_history_$userId" => $history]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Xin lỗi, tôi không thể hỗ trợ các nội dung không phù hợp hoặc vi phạm quy định. Bạn vui lòng đặt câu hỏi khác nhé!'
                ]);
            }

            // Lấy lịch sử chat
            $history = $this->getChatHistory($userId);
            
            // Phân tích câu hỏi
            $analysis = $this->analyzeQuestion($question);
            
            // Tìm câu trả lời phù hợp
            $response = $this->getContextualResponse($question, $analysis, $history);
            
            // Nếu không tìm thấy câu trả lời phù hợp, gọi Gemini API
            if (!$response) {
                $context = $this->prepareContext($question, $history);
                $response = $this->callGeminiApiForWebSearchWithContext($question, $context);
            }
            
            // Tạo nút điều hướng
            $navigationButtons = $this->generateNavigationButtons($question);
            
            // Lưu lịch sử
            $interaction = $this->storeChatHistory($userId, $question, $response);
            
                return response()->json([
                    'success' => true,
                'message' => $response,
                'navigation_buttons' => $navigationButtons,
                'interaction_id' => $interaction->id
            ]);
            
        } catch (Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
                return response()->json([
                'success' => false,
                'message' => 'Xin lỗi, tôi đang gặp vấn đề. Vui lòng thử lại sau.'
            ]);
        }
    }
    
    public function feedback(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'interaction_id' => 'required|integer',
                'is_helpful' => 'required|boolean',
                'comment' => 'nullable|string',
                'userId' => 'nullable|string'
            ]);

            $feedback = $this->learningService->storeFeedback(
                $validatedData['interaction_id'],
                $validatedData['userId'],
                $validatedData['is_helpful'],
                $validatedData['comment'] ?? null
            );
            
                return response()->json([
                    'success' => true,
                'message' => 'Cảm ơn phản hồi của bạn!'
                ]);
            
        } catch (Exception $e) {
            Log::error('Feedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý phản hồi.'
            ]);
        }
    }
    
    private function processQuestion($question, $userId, $analysis)
    {
        // Giữ nguyên logic xử lý câu hỏi hiện tại
        // ... (giữ nguyên code cũ)

        // Thêm xử lý dựa trên phân tích
        switch ($analysis['intent']) {
            case 'greeting':
                return [
                    'success' => true,
                    'message' => "Xin chào! Tôi là trợ lý ảo của PetCare. Tôi có thể giúp gì cho bạn? 😊"
                ];
            
            case 'booking':
                return $this->startBookingFlow();
            
            case 'price':
                return [
                    'success' => true,
                    'message' => "Bảng giá dịch vụ tại PetCare:\n• Khám tổng quát: 150.000đ - 300.000đ\n• Tiêm phòng: 150.000đ - 500.000đ/mũi\n• Spa cơ bản: 200.000đ - 500.000đ\n• Spa cao cấp: 400.000đ - 800.000đ\n• Phẫu thuật: 1.000.000đ - 5.000.000đ\n\nGiá có thể thay đổi tùy theo cân nặng, tình trạng và nhu cầu cụ thể của thú cưng. 💰"
                ];
            
            case 'service':
                if (isset($analysis['entities']['service'])) {
                    return $this->getServiceInfo($analysis['entities']['service']);
                }
                return [
                'success' => true,
                    'message' => "PetCare cung cấp nhiều dịch vụ cho thú cưng như khám bệnh, tiêm phòng, spa, phẫu thuật và cắt tỉa lông. Bạn quan tâm đến dịch vụ cụ thể nào? 🐾"
                ];
            
            case 'doctor':
                return $this->getDoctorInfo();
            
            case 'emergency':
                return [
                    'success' => true,
                    'message' => "🚨 TRƯỜNG HỢP KHẨN CẤP 🚨\n\nVui lòng gọi ngay số điện thoại cấp cứu: 0123.456.789\n\nChúng tôi luôn sẵn sàng hỗ trợ 24/7!"
                ];
            
            default:
                return $this->searchFromWeb($question);
        }
    }

    private function getServiceInfo($service)
    {
        $serviceInfo = [
            'spa' => "Dịch vụ spa tại PetCare bao gồm:\n• Tắm rửa\n• Cắt tỉa lông\n• Vệ sinh tai\n• Cắt móng\n• Massage\n\nGiá từ 200.000đ - 800.000đ tùy gói dịch vụ. ✨",
            'khám' => "Dịch vụ khám bệnh tại PetCare:\n• Khám tổng quát\n• Khám chuyên khoa\n• Xét nghiệm\n• Siêu âm\n\nGiá từ 150.000đ - 500.000đ. 🩺",
            'tiêm' => "Dịch vụ tiêm phòng tại PetCare:\n• Vaccine 7 bệnh\n• Vaccine dại\n• Vaccine viêm mũi truyền nhiễm\n\nGiá từ 150.000đ - 500.000đ/mũi. 💉",
            'phẫu thuật' => "Dịch vụ phẫu thuật tại PetCare:\n• Triệt sản\n• Phẫu thuật chỉnh hình\n• Phẫu thuật nội tạng\n\nGiá từ 1.000.000đ - 5.000.000đ. 🏥"
        ];

        return [
                    'success' => true,
            'message' => $serviceInfo[$service] ?? "Xin lỗi, tôi chưa có thông tin chi tiết về dịch vụ này. Vui lòng liên hệ trực tiếp với phòng khám để được tư vấn."
        ];
    }

    private function getDoctorInfo()
    {
        try {
            $bacSiChucVu = \App\Models\ChucVu::where('ten_chuc_vu', 'like', '%bác sĩ%')
                            ->orWhere('ten_chuc_vu', 'like', '%y tá%')
                            ->pluck('id')
                            ->toArray();
                            
            $bacSi = \App\Models\NhanVien::whereIn('id_chucvu', $bacSiChucVu)
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
                            
                return $response;
                        }
                    } catch (Exception $e) {
            Log::error('Error getting doctor info: ' . $e->getMessage());
                    }
                    
        return "Phòng khám của chúng tôi có đội ngũ bác sĩ giàu kinh nghiệm trong lĩnh vực thú y. Bạn có thể xem thông tin chi tiết về các bác sĩ trong mục 'Đội ngũ bác sĩ' trên trang web hoặc đặt lịch khám trực tiếp. 👨‍⚕️";
    }

    private function searchFromWeb($question)
    {
        $q = mb_strtolower($question, 'UTF-8');

        // Nhận diện triệu chứng phổ biến
        if (preg_match('/(sổ mũi|sổ mui|chảy nước mũi|hắt hơi|ho|khò khè|nghẹt mũi|thở khò khè)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng của bạn có dấu hiệu về đường hô hấp như sổ mũi, ho, hắt hơi. Nguyên nhân có thể do cảm lạnh, nhiễm khuẩn, dị ứng hoặc bệnh truyền nhiễm. Bạn nên giữ ấm cho thú, vệ sinh mũi bằng nước muối sinh lý và theo dõi thêm. Nếu tình trạng kéo dài hoặc thú cưng khó thở, hãy đưa đến phòng khám thú y để được kiểm tra và điều trị kịp thời nhé! 🐶🐱"
            ];
        }
        // Nhận diện triệu chứng tiêu chảy, nôn, bỏ ăn
        if (preg_match('/(tiêu chảy|nôn|ói|mửa|bỏ ăn|không ăn|biếng ăn|phân lỏng|đi ngoài|bụng trướng|đầy hơi|táo bón)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng có dấu hiệu rối loạn tiêu hóa như tiêu chảy, nôn, bỏ ăn. Nguyên nhân có thể do thay đổi thức ăn, nhiễm khuẩn, ký sinh trùng hoặc dị ứng. Bạn nên cho thú cưng uống đủ nước, tạm ngưng cho ăn 1 bữa (nếu là thú trưởng thành), sau đó cho ăn thức ăn dễ tiêu. Nếu triệu chứng kéo dài trên 24h hoặc thú cưng yếu, hãy đưa đến phòng khám để được bác sĩ kiểm tra nhé! 💩🤢"
            ];
        }
        // Nhận diện triệu chứng ngứa, rụng lông, gãi
        if (preg_match('/(ngứa|gãi|rụng lông|nấm|ghẻ|vảy|da đỏ|da khô|viêm da|mảng đỏ|mụn|nổi cục)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng bị ngứa, rụng lông hoặc có dấu hiệu da bất thường có thể do ký sinh trùng, nấm, dị ứng hoặc viêm da. Bạn nên kiểm tra kỹ vùng da bị ảnh hưởng, giữ vệ sinh sạch sẽ và tránh để thú cưng tự gãi nhiều. Không tự ý dùng thuốc khi chưa có chỉ định của bác sĩ. Nếu tình trạng không cải thiện, hãy đưa thú cưng đến phòng khám để được tư vấn và điều trị đúng cách! 🐾"
            ];
        }
        // Nhận diện triệu chứng đau mắt, chảy nước mắt, đỏ mắt
        if (preg_match('/(đau mắt|chảy nước mắt|mắt đỏ|mắt sưng|mắt mờ|mắt đục|ghèn|viêm mắt|mắt nhắm|không mở được mắt)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng có dấu hiệu đau mắt, chảy nước mắt, đỏ mắt có thể do viêm kết mạc, dị vật, nhiễm khuẩn hoặc chấn thương. Bạn nên vệ sinh mắt nhẹ nhàng bằng nước muối sinh lý, tránh để thú cưng cào gãi vào mắt. Nếu mắt sưng, mờ, có mủ hoặc thú cưng không mở được mắt, hãy đưa đến phòng khám để được bác sĩ kiểm tra ngay nhé! 👁️"
            ];
        }
        // Nhận diện triệu chứng đau tai, lắc đầu, ngứa tai
        if (preg_match('/(ngứa tai|gãi tai|lắc đầu|hôi tai|viêm tai|ráy tai|chảy mủ tai|tai đỏ|tai sưng)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng có dấu hiệu ngứa tai, lắc đầu, tai có mùi hôi hoặc chảy dịch có thể bị viêm tai do ký sinh trùng, nấm hoặc vi khuẩn. Không tự ý dùng thuốc nhỏ tai của người. Bạn nên đưa thú cưng đến phòng khám để được kiểm tra và điều trị đúng cách, tránh biến chứng nặng hơn nhé! 🦻"
            ];
        }
        // Nhận diện triệu chứng về tiết niệu
        if (preg_match('/(tiểu khó|đi tiểu nhiều|tiểu ra máu|không đi tiểu được|tiểu nhỏ giọt|tiểu thường xuyên|tiểu ngoài khay)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng có dấu hiệu bất thường khi đi tiểu như tiểu khó, tiểu ra máu, tiểu nhiều lần có thể do viêm đường tiết niệu, sỏi bàng quang hoặc bệnh lý thận. Đây là dấu hiệu cần được kiểm tra sớm, đặc biệt nếu thú cưng không đi tiểu được hoặc tiểu ra máu. Hãy đưa thú cưng đến phòng khám để được bác sĩ kiểm tra và điều trị kịp thời nhé! 🚾"
            ];
        }
        // Nhận diện triệu chứng về xương khớp, đi khập khiễng
        if (preg_match('/(đi khập khiễng|đau khớp|sưng khớp|không đi được|khó đứng|khó leo cầu thang|đau lưng|không nhảy được|tê liệt|yếu chân|run chân)/ui', $q)) {
            return [
                        'success' => true,
                'message' => "Thú cưng đi khập khiễng, đau khớp, yếu chân có thể do chấn thương, viêm khớp hoặc bệnh lý xương khớp. Bạn nên hạn chế vận động mạnh cho thú cưng, giữ ấm và theo dõi thêm. Nếu thú cưng không đi được hoặc đau nhiều, hãy đưa đến phòng khám để được chẩn đoán và điều trị phù hợp! 🦴"
            ];
        }
        // Nếu không khớp triệu chứng nào, luôn gọi AI Gemini để trả lời
        $aiResponse = $this->callGeminiApiForWebSearch($question);

        // Kiểm duyệt tự động (nếu cần)
        if ($this->isInappropriateContent($aiResponse)) {
            return [
                        'success' => true,
                'message' => 'Xin lỗi, tôi chưa có thông tin phù hợp để trả lời câu hỏi này. Bạn vui lòng liên hệ trực tiếp với phòng khám để được hỗ trợ nhé!'
            ];
                }
                
        return [
                        'success' => true,
            'message' => $aiResponse
        ];
    }

    // Gọi Gemini API để lấy câu trả lời AI cho các câu hỏi không có trong tri thức
    private function callGeminiApiForWebSearch($question)
    {
        $apiKey = env('GEMINI_API_KEY', '');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $prompt = "Bạn là trợ lý ảo phòng khám thú y PetCare. Hãy trả lời ngắn gọn, thân thiện, dễ hiểu, có thể dùng emoji nếu phù hợp. Nếu không chắc chắn, hãy khuyên khách liên hệ bác sĩ. Câu hỏi: $question";
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
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(30)
              ->post($url . '?key=' . $apiKey, $data);
            \Log::info('Gemini API response:', ['body' => $response->body()]);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    return $responseData['candidates'][0]['content']['parts'][0]['text'];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gemini API error: ' . $e->getMessage());
        }
        return 'Xin lỗi, tôi chưa có thông tin về vấn đề này. Bạn vui lòng liên hệ trực tiếp với phòng khám để được hỗ trợ nhé!';
    }

    // Kiểm duyệt nội dung AI trả về
    private function isInappropriateContent($text) {
        // Danh sách từ khóa cấm
        $badWords = [
            'sex', 'khiêu dâm', 'bạo lực', 'chửi', 'tục', 'phản động', 'chính trị', 
            'đánh nhau', 'ma túy', 'cờ bạc', 'lừa đảo', 'hack', 'crack', 'xxx', '18+', 
            'bán thuốc', 'bán hàng', 'liên hệ', 'số điện thoại', 'facebook', 'zalo', 
            'telegram', 'viber', 'email', 'địa chỉ', 'website', 'link', 'http', 'https', 'www.'
        ];
        
        $textLower = mb_strtolower($text, 'UTF-8');
        foreach ($badWords as $word) {
            if (mb_strpos($textLower, $word) !== false) {
                return true;
            }
        }
        return false;
    }

    public function getDoctorsWithSchedule()
    {
        try {
            $bacSiChucVu = \App\Models\ChucVu::where('ten_chuc_vu', 'like', '%bác sĩ%')
                    ->orWhere('ten_chuc_vu', 'like', '%y tá%')
                    ->pluck('id')
                    ->toArray();
                
            $bacSi = \App\Models\NhanVien::whereIn('id_chucvu', $bacSiChucVu)
                    ->where('tinh_trang', 1)
                            ->where('is_deleted', 0)
                            ->with(['chuc_vu', 'lich_ranh' => function($query) {
                                $query->where('tinh_trang', 1)
                                      ->where('is_deleted', 0);
                            }])
                            ->get()
                            ->map(function($bs) {
                                return [
                                    'id' => $bs->id,
                                    'ten' => $bs->ten_nv,
                                    'chuc_vu' => $bs->chuc_vu ? $bs->chuc_vu->ten_chuc_vu : 'Bác sĩ',
                                    'mo_ta' => $bs->mo_ta,
                                    'lich_ranh' => $bs->lich_ranh ? $bs->lich_ranh->map(function($lich) {
                                        return [
                                            'ngay' => $lich->ngay,
                                            'gio_bat_dau' => $lich->gio_bat_dau,
                                            'gio_ket_thuc' => $lich->gio_ket_thuc,
                                        ];
                                    }) : []
                                ];
                            });

            return response()->json([
                'success' => true,
                'data' => $bacSi
            ]);
            } catch (Exception $e) {
            Log::error('Error getting doctors with schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin bác sĩ'
            ]);
        }
    }

    // Hàm gọi Gemini API với context hội thoại
    private function callGeminiApiForWebSearchWithContext($question, $context)
    {
        $apiKey = env('GEMINI_API_KEY', '');
        $url = $this->geminiUrl;
        $data = [
            'contents' => $context,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->timeout(15) // Giảm timeout xuống 15 giây để phát hiện vấn đề sớm hơn
              ->post($url . '?key=' . $apiKey, $data);
            \Log::info('Gemini API request:', ['data' => $data]);
            \Log::info('Gemini API response:', ['body' => $response->body()]);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    return $responseData['candidates'][0]['content']['parts'][0]['text'];
                }
            }
            // Nếu không thành công nhưng không phải timeout
            return 'Xin lỗi, tôi đang gặp vấn đề khi tìm kiếm thông tin. Vui lòng đặt câu hỏi khác hoặc liên hệ trực tiếp với phòng khám.';
        } catch (\Exception $e) {
            \Log::error('Gemini API error: ' . $e->getMessage());
            // Kiểm tra nếu lỗi là do timeout
            if (strpos($e->getMessage(), 'timeout') !== false || $e instanceof \Illuminate\Http\Client\ConnectionException) {
                return 'Hmm, câu hỏi của bạn có vẻ cần nhiều thời gian để tìm câu trả lời. Bạn có thể đặt câu hỏi ngắn hơn hoặc cụ thể hơn để tôi có thể giúp bạn nhanh chóng hơn. 😊';
            }
            return 'Xin lỗi, tôi chưa có thông tin về vấn đề này. Bạn vui lòng liên hệ trực tiếp với phòng khám để được hỗ trợ nhé!';
        }
    }

    private function getChatHistory($userId) {
        // Lấy lịch sử từ session
        $history = session("chatbot_history_$userId", []);
        
        // Nếu không có trong session, lấy từ database
        if (empty($history)) {
            $interactions = ChatbotInteraction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
                
            foreach ($interactions as $interaction) {
                $history[] = [
                    'role' => 'user',
                    'content' => $interaction->question,
                    'timestamp' => $interaction->created_at
                ];
                $history[] = [
                    'role' => 'bot',
                    'content' => $interaction->response,
                    'timestamp' => $interaction->created_at
                ];
            }
        }
        
        return $history;
    }

    private function storeChatHistory($userId, $question, $response) {
        // Lưu vào session
        $history = session("chatbot_history_$userId", []);
        $history[] = [
            'role' => 'user',
            'content' => $question,
            'timestamp' => now()
        ];
        
        // Xử lý response nếu là array
        $responseText = is_array($response) ? ($response['message'] ?? '') : $response;
        
        $history[] = [
            'role' => 'bot',
            'content' => $responseText,
            'timestamp' => now()
        ];
        
        // Giới hạn 10 tin nhắn gần nhất trong session
        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }
        session(["chatbot_history_$userId" => $history]);
        
        // Lưu vào database
        return ChatbotInteraction::create([
            'user_id' => $userId,
            'question' => $question,
            'response' => $responseText,
            'context' => json_encode($history),
            'success_rate' => 1,
            'usage_count' => 1,
            'status' => 'approved'
        ]);
    }

    private function prepareContext($question, $history) {
        $context = [];
        
        // Thêm prompt hệ thống
        $context[] = [
            'role' => 'system',
            'parts' => [
                ['text' => "Bạn là trợ lý ảo phòng khám thú y PetCare. Hãy tiếp tục hội thoại dựa trên lịch sử bên dưới. Nếu người dùng trả lời ngắn, hãy hiểu đó là phản hồi cho câu hỏi trước. Hãy trả lời bằng tiếng Việt, ngắn gọn, thân thiện, dễ hiểu, sử dụng ngôn ngữ tự nhiên như người Việt Nam. Có thể dùng emoji nếu phù hợp. Nếu không chắc chắn, hãy khuyên khách liên hệ bác sĩ."]
            ]
        ];
        
        // Thêm lịch sử hội thoại
        foreach ($history as $item) {
            $context[] = [
                'role' => $item['role'] === 'bot' ? 'system' : 'user',
                'parts' => [
                    ['text' => $item['content']]
                ]
            ];
        }
        
        // Thêm câu hỏi hiện tại
        $context[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $question]
            ]
        ];
        
        return $context;
    }

    private function generateGuestId()
    {
        return 'guest_' . uniqid() . '_' . time();
    }

    private function getGuestId($request)
    {
        $guestId = $request->cookie('guest_id');
        if (!$guestId) {
            $guestId = $this->generateGuestId();
            // Set cookie for 30 days
            cookie()->queue('guest_id', $guestId, 60 * 24 * 30);
        }
        return $guestId;
    }

    private function cleanupOldGuestData()
    {
        // Delete guest interactions older than 30 days
        $thirtyDaysAgo = now()->subDays(30);
        ChatbotInteraction::where('user_id', 'like', 'guest_%')
            ->where('created_at', '<', $thirtyDaysAgo)
            ->delete();
    }

    private function analyzeQuestion($question) {
        $questionLower = mb_strtolower($question, 'UTF-8');
        $analysis = [
            'intent' => 'general',
            'entities' => [],
            'context' => []
        ];

        // Phân tích ý định
        if (preg_match('/(giá|phí|chi phí|bao nhiêu tiền)/ui', $questionLower)) {
            $analysis['intent'] = 'price';
        } 
        elseif (preg_match('/(đặt lịch|hẹn|lịch khám|khám)/ui', $questionLower)) {
            $analysis['intent'] = 'booking';
        } 
        elseif (preg_match('/(bác sĩ|y tá|nhân viên)/ui', $questionLower)) {
            $analysis['intent'] = 'doctor';
        } 
        elseif (preg_match('/(dịch vụ|trang dịch vụ|vào trang dịch vụ|xem dịch vụ|chọn dịch vụ)/ui', $questionLower)) {
            $analysis['intent'] = 'service';
            $analysis['entities']['direct_navigation'] = true;
        } 
        elseif (preg_match('/(cấp cứu|khẩn cấp|nguy hiểm)/ui', $questionLower)) {
            $analysis['intent'] = 'emergency';
        }
        elseif (preg_match('/(vào trang|đi tới|mở trang|chuyển tới)/ui', $questionLower)) {
            $analysis['intent'] = 'navigation';
            
            // Xác định trang cần chuyển đến
            if (preg_match('/(trang chủ|home)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'home';
            }
            elseif (preg_match('/(đăng nhập|login)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'login';
            }
            elseif (preg_match('/(dịch vụ)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'service';
            }
            elseif (preg_match('/(bác sĩ)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'doctor';
            }
            elseif (preg_match('/(đặt lịch)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'booking';
            }
            elseif (preg_match('/(giá)/ui', $questionLower)) {
                $analysis['entities']['target_page'] = 'price';
            }
        }

        // Phân tích thực thể
        if (preg_match('/(chó|mèo|thú cưng)/ui', $questionLower, $matches)) {
            $analysis['entities']['pet_type'] = $matches[0];
        }
        if (preg_match('/(spa|tắm|cắt tỉa|tiêm|phẫu thuật)/ui', $questionLower, $matches)) {
            $analysis['entities']['service'] = $matches[0];
        }

        return $analysis;
    }

    private function getContextualResponse($question, $analysis, $history) {
        // Kiểm tra câu hỏi tương tự trong lịch sử
        $similarQuestion = $this->findSimilarQuestion($question, $history);
        if ($similarQuestion) {
            return $similarQuestion['response'];
        }

        // Xử lý yêu cầu điều hướng trực tiếp
        if ($analysis['intent'] === 'navigation') {
            $targetPage = $analysis['entities']['target_page'] ?? '';
            
            switch ($targetPage) {
                case 'service':
                    return "Bạn có thể xem các dịch vụ của chúng tôi ở trang Dịch vụ. Tôi đã tạo nút điều hướng bên dưới để bạn có thể truy cập nhanh.";
                case 'login':
                    return "Bạn có thể đăng nhập vào tài khoản của mình ở trang Đăng nhập. Tôi đã tạo nút điều hướng bên dưới để bạn truy cập nhanh.";
                case 'doctor':
                    return "Bạn có thể xem thông tin về các bác sĩ của chúng tôi ở trang Bác sĩ. Tôi đã tạo nút điều hướng bên dưới để bạn truy cập nhanh.";
                case 'booking':
                    return "Bạn có thể đặt lịch khám cho thú cưng ở trang Đặt lịch. Tôi đã tạo nút điều hướng bên dưới để bạn truy cập nhanh.";
                case 'price':
                    return "Bạn có thể xem bảng giá dịch vụ ở trang Bảng giá. Tôi đã tạo nút điều hướng bên dưới để bạn truy cập nhanh.";
                case 'home':
                    return "Bạn có thể quay về trang chủ bằng nút điều hướng bên dưới.";
                default:
                    // Không tìm thấy trang cụ thể, vẫn tạo nút điều hướng
                    return null;
            }
        }

        // Xử lý theo ý định
        switch ($analysis['intent']) {
            case 'price':
                return $this->getPriceInfo($analysis['entities'] ?? []);
            case 'booking':
                return $this->getBookingInfo($analysis['entities'] ?? []);
            case 'doctor':
                return $this->getDoctorInfo();
            case 'service':
                if (isset($analysis['entities']['direct_navigation']) && $analysis['entities']['direct_navigation']) {
                    return "Bạn có thể xem các dịch vụ của chúng tôi ở trang Dịch vụ. Tôi đã tạo nút điều hướng bên dưới để bạn có thể truy cập nhanh.";
                }
                return $this->getServiceInfo($analysis['entities']['service'] ?? null);
            case 'emergency':
                return $this->getEmergencyInfo();
            default:
                return null;
        }
    }

    private function findSimilarQuestion($question, $history) {
        $bestMatch = null;
        $highestSimilarity = 0;

        foreach ($history as $item) {
            if ($item['role'] === 'user') {
                $similarity = similar_text($question, $item['content'], $percent);
                if ($percent > 80 && $percent > $highestSimilarity) {
                    $highestSimilarity = $percent;
                    $bestMatch = $item;
                }
            }
        }

        return $bestMatch;
    }

    // Thêm các phương thức còn thiếu
    private function getPriceInfo($entities = []) {
        $petType = $entities['pet_type'] ?? '';
        $serviceType = $entities['service'] ?? '';
        
        $message = "Bảng giá dịch vụ tại PetCare:\n";
        $message .= "• Khám tổng quát: 150.000đ - 300.000đ\n";
        $message .= "• Tiêm phòng: 150.000đ - 500.000đ/mũi\n";
        $message .= "• Spa cơ bản: 200.000đ - 500.000đ\n";
        $message .= "• Spa cao cấp: 400.000đ - 800.000đ\n";
        $message .= "• Phẫu thuật: 1.000.000đ - 5.000.000đ\n\n";
        $message .= "Giá có thể thay đổi tùy theo cân nặng, tình trạng và nhu cầu cụ thể của thú cưng. 💰";
        
        if (!empty($petType) || !empty($serviceType)) {
            $message .= "\n\nBạn có thể xem chi tiết bảng giá đầy đủ tại trang Bảng giá của chúng tôi.";
        }
        
        return $message;
    }

    private function getBookingInfo($entities = []) {
        $message = "Để đặt lịch khám tại PetCare, bạn có thể:\n";
        $message .= "• Đặt lịch trực tiếp qua trang web\n";
        $message .= "• Đặt lịch theo bác sĩ yêu thích\n";
        $message .= "• Đặt lịch dịch vụ tiêm chủng\n";
        $message .= "• Đặt lịch dịch vụ spa/chăm sóc\n\n";
        $message .= "Bạn muốn đặt lịch cho dịch vụ nào? ��";
        
        return $message;
    }

    private function getEmergencyInfo() {
        $message = "🚨 TRƯỜNG HỢP KHẨN CẤP 🚨\n\n";
        $message .= "Vui lòng gọi ngay số điện thoại cấp cứu: 0123.456.789\n\n";
        $message .= "Chúng tôi luôn sẵn sàng hỗ trợ 24/7!";
        
        return $message;
    }

    // Thêm phương thức mới để xác định và tạo nút điều hướng
    private function generateNavigationButtons($question)
    {
        $questionLower = mb_strtolower($question, 'UTF-8');
        $buttons = [];
        $directNavigationMatch = false;
        
        // Kiểm tra xem có phải là yêu cầu điều hướng trực tiếp không
        $directNavigationPatterns = [
            '/(vào|đi tới|mở|xem|chuyển tới) trang (.*?)( |$)/ui',
            '/(vào|đi tới|mở|xem|chuyển tới) (.*?)( |$)/ui'
        ];
        
        foreach ($directNavigationPatterns as $pattern) {
            if (preg_match($pattern, $questionLower, $matches)) {
                $directNavigationMatch = true;
                $target = mb_strtolower(trim($matches[2]), 'UTF-8');
                
                // Xác định trang cần chuyển đến
                if (mb_strpos($target, 'dịch vụ') !== false) {
                    $buttons[] = [
                        'text' => 'Xem dịch vụ',
                        'route' => '/client/xem-dich-vu',
                        'icon' => '🐾'
                    ];
                    $buttons[] = [
                        'text' => 'Chọn dịch vụ',
                        'route' => '/client/chon-dich-vu',
                        'icon' => '✅'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'đăng nhập') !== false || mb_strpos($target, 'login') !== false) {
                    $buttons[] = [
                        'text' => 'Đăng nhập',
                        'route' => '/client/dang-nhap-dang-ky',
                        'icon' => '🔑'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'bác sĩ') !== false || mb_strpos($target, 'doctor') !== false) {
                    $buttons[] = [
                        'text' => 'Danh sách bác sĩ',
                        'route' => '/client/xem-bs/0',
                        'icon' => '👨‍⚕️'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'đặt lịch') !== false || mb_strpos($target, 'booking') !== false) {
                    $buttons[] = [
                        'text' => 'Đặt lịch khám',
                        'route' => '/client/dat-lich',
                        'icon' => '📅'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'giá') !== false || mb_strpos($target, 'price') !== false) {
                    $buttons[] = [
                        'text' => 'Bảng giá dịch vụ',
                        'route' => '/client/bang-gia',
                        'icon' => '💰'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'chủ') !== false || mb_strpos($target, 'home') !== false) {
                    $buttons[] = [
                        'text' => 'Trang chủ',
                        'route' => '/',
                        'icon' => '🏠'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'lịch sử') !== false || mb_strpos($target, 'history') !== false) {
                    $buttons[] = [
                        'text' => 'Lịch sử khám',
                        'route' => '/client/lich-su-kham',
                        'icon' => '📋'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'tin tức') !== false || mb_strpos($target, 'news') !== false) {
                    $buttons[] = [
                        'text' => 'Tin tức & Bài viết',
                        'route' => '/client/tin-tuc',
                        'icon' => '📰'
                    ];
                    break;
                }
                elseif (mb_strpos($target, 'liên hệ') !== false || mb_strpos($target, 'contact') !== false) {
                    $buttons[] = [
                        'text' => 'Liên hệ',
                        'route' => '/client/lien-he',
                        'icon' => '📞'
                    ];
                    break;
                }
            }
        }
        
        // Nếu không tìm thấy mẫu điều hướng trực tiếp, sử dụng phương pháp từ khóa
        if (!$directNavigationMatch) {
            // Danh sách từ khóa và route
            $navigationMap = [
                'đăng nhập' => [
                    'keywords' => ['đăng nhập', 'login', 'sign in', 'signin', 'vào tài khoản', 'vào trang đăng nhập', 'đăng nhập khách hàng'],
                    'text' => 'Đăng nhập',
                    'route' => '/client/dang-nhap-dang-ky',
                    'icon' => '🔑'
                ],
                'đăng ký' => [
                    'keywords' => ['đăng ký', 'tạo tài khoản', 'tạo account', 'sign up', 'signup', 'register', 'chưa có tài khoản'],
                    'text' => 'Đăng ký tài khoản',
                    'route' => '/client/dang-nhap-dang-ky',
                    'icon' => '📝'
                ],
                'bác sĩ' => [
                    'keywords' => ['bác sĩ', 'y tá', 'nhân viên', 'đội ngũ', 'chuyên gia'],
                    'text' => 'Danh sách bác sĩ',
                    'route' => '/client/xem-bs/0', // ID 0 sẽ hiển thị tất cả bác sĩ
                    'icon' => '👨‍⚕️'
                ],
                'dịch vụ' => [
                    'keywords' => ['dịch vụ', 'chăm sóc', 'cung cấp'],
                    'text' => 'Xem dịch vụ',
                    'route' => '/client/xem-dich-vu',
                    'icon' => '🐾'
                ],
                'chọn dịch vụ' => [
                    'keywords' => ['chọn dịch vụ', 'đăng ký dịch vụ'],
                    'text' => 'Chọn dịch vụ',
                    'route' => '/client/chon-dich-vu',
                    'icon' => '✅'
                ],
                'đặt lịch' => [
                    'keywords' => ['đặt lịch', 'hẹn', 'khám', 'lịch hẹn', 'đăng ký khám', 'kiểm tra'],
                    'text' => 'Đặt lịch khám',
                    'route' => '/client/dat-lich',
                    'icon' => '📅'
                ],
                'đặt lịch bác sĩ' => [
                    'keywords' => ['đặt lịch bác sĩ', 'đặt lịch theo bác sĩ', 'chọn bác sĩ khám'],
                    'text' => 'Đặt lịch theo bác sĩ',
                    'route' => '/client/dat-lich-theo-bac-si',
                    'icon' => '👨‍⚕️'
                ],
                'đặt lịch tiêm chủng' => [
                    'keywords' => ['tiêm chủng', 'tiêm phòng', 'vaccine', 'tiêm ngừa'],
                    'text' => 'Đặt lịch tiêm chủng',
                    'route' => '/client/dat-lich-tiem-chung',
                    'icon' => '💉'
                ],
                'đặt lịch spa' => [
                    'keywords' => ['spa', 'tắm', 'cắt lông', 'cắt móng', 'chăm sóc'],
                    'text' => 'Đặt lịch chăm sóc/Spa',
                    'route' => '/client/dat-lich-cham-soc',
                    'icon' => '✂️'
                ],
                'hồ sơ' => [
                    'keywords' => ['hồ sơ', 'profile', 'thông tin cá nhân', 'thông tin của tôi', 'tài khoản của tôi'],
                    'text' => 'Thông tin cá nhân',
                    'route' => '/client/thong-tin-ca-nhan',
                    'icon' => '👤'
                ],
                'thú cưng' => [
                    'keywords' => ['thú cưng', 'pet', 'chó', 'mèo', 'thú cưng của tôi', 'thêm thú cưng'],
                    'text' => 'Quản lý thú cưng',
                    'route' => '/client/pet',
                    'icon' => '🐶'
                ],
                'giá' => [
                    'keywords' => ['giá', 'chi phí', 'bảng giá', 'phí dịch vụ', 'tiền', 'thanh toán', 'bao nhiêu tiền'],
                    'text' => 'Bảng giá dịch vụ',
                    'route' => '/client/bang-gia',
                    'icon' => '💰'
                ],
                'thanh toán' => [
                    'keywords' => ['thanh toán', 'trả tiền', 'đóng tiền', 'hóa đơn'],
                    'text' => 'Thanh toán',
                    'route' => '/client/thanh-toan',
                    'icon' => '💳'
                ],
                'giới thiệu' => [
                    'keywords' => ['giới thiệu', 'về chúng tôi', 'thông tin'],
                    'text' => 'Giới thiệu',
                    'route' => '/client/gioi-thieu',
                    'icon' => '📋'
                ],
                'đánh giá' => [
                    'keywords' => ['đánh giá', 'review', 'feedback', 'phản hồi'],
                    'text' => 'Đánh giá',
                    'route' => '/client/danh-gia',
                    'icon' => '⭐'
                ],
                'trang chủ' => [
                    'keywords' => ['trang chủ', 'home', 'màn hình chính', 'bắt đầu', 'chính', 'trang đầu'],
                    'text' => 'Trang chủ',
                    'route' => '/',
                    'icon' => '🏠'
                ],
                'lịch sử khám' => [
                    'keywords' => ['lịch sử khám', 'lịch sử', 'đã khám', 'kết quả khám', 'hồ sơ khám'],
                    'text' => 'Lịch sử khám',
                    'route' => '/client/lich-su-kham',
                    'icon' => '📋'
                ],
                'tin tức' => [
                    'keywords' => ['tin tức', 'bài viết', 'blog', 'kiến thức', 'thông tin', 'mẹo'],
                    'text' => 'Tin tức & Bài viết',
                    'route' => '/client/tin-tuc',
                    'icon' => '📰'
                ],
                'liên hệ' => [
                    'keywords' => ['liên hệ', 'địa chỉ', 'số điện thoại', 'email', 'phòng khám', 'bản đồ', 'vị trí'],
                    'text' => 'Liên hệ',
                    'route' => '/client/lien-he',
                    'icon' => '📞'
                ]
            ];

            // Phân tích sâu hơn với từng nhóm từ khóa
            foreach ($navigationMap as $category => $navItem) {
                // Kiểm tra từng từ khóa trong nhóm
                foreach ($navItem['keywords'] as $keyword) {
                    if (mb_strpos($questionLower, $keyword) !== false) {
                        $buttons[] = [
                            'text' => $navItem['text'],
                            'route' => $navItem['route'],
                            'icon' => $navItem['icon']
                        ];
                        
                        // Thêm nút liên quan
                        if ($category === 'đăng nhập') {
                            $buttons[] = [
                                'text' => 'Đăng ký tài khoản mới',
                                'route' => '/client/dang-nhap-dang-ky',
                                'icon' => '📝'
                            ];
                        } 
                        else if ($category === 'đặt lịch') {
                            // Thêm các lựa chọn đặt lịch khác
                            $buttons[] = [
                                'text' => 'Đặt lịch theo bác sĩ',
                                'route' => '/client/dat-lich-theo-bac-si',
                                'icon' => '👨‍⚕️'
                            ];
                        }
                        break;
                    }
                }
            }
        }

        // Nếu vẫn không tìm thấy nút nào, thêm nút trang chủ
        if (empty($buttons)) {
            $buttons[] = [
                'text' => 'Trang chủ',
                'route' => '/',
                'icon' => '🏠'
            ];
        }

        return $buttons;
    }

    // Hàm xử lý chức năng chatbot
    public function handleChatbotFunction(Request $request)
    {
        $question = $request->input('question');
        \Log::info('ChatbotController: Received question', ['question' => $question]);

        if (empty($question)) {
            return response()->json(['error' => 'Vui lòng nhập câu hỏi'], 400);
        }

        $session_id = $request->input('session_id');
        if (empty($session_id)) {
            $session_id = uniqid();
        }

        // Lưu câu hỏi vào session history
        $this->storeMessage($session_id, 'user', $question);

        try {
            // Lấy lịch sử hội thoại
            $history = $this->getSessionHistory($session_id);
            $context = $this->buildConversationContext($history);
            
            // Gọi Gemini API với context
            $response = $this->callGeminiApiForWebSearchWithContext($question, $context);
            
            // Kiểm tra nếu response chứa thông báo timeout hoặc lỗi
            $isErrorResponse = false;
            if (strpos($response, 'câu hỏi của bạn có vẻ cần nhiều thời gian') !== false || 
                strpos($response, 'Xin lỗi, tôi đang gặp vấn đề') !== false) {
                $isErrorResponse = true;
            }
            
            // Lưu câu trả lời vào session history (chỉ khi không phải lỗi timeout)
            if (!$isErrorResponse) {
                $this->storeMessage($session_id, 'assistant', $response);
            }
            
            return response()->json([
                'response' => $response,
                'session_id' => $session_id,
                'isError' => $isErrorResponse
            ]);
        } catch (\Exception $e) {
            \Log::error('ChatbotController error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Đã xảy ra lỗi khi xử lý câu hỏi của bạn.',
                'session_id' => $session_id,
                'isError' => true
            ], 500);
        }
    }
} 
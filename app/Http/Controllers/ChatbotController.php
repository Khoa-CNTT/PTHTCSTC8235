<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ChatbotLearningService;
use App\Services\OllamaChatbotService;
use App\Models\ChatbotInteraction;
use App\Models\ChatbotFeedback;
use Exception;

class ChatbotController extends Controller
{
    private $ollamaService;
    private $learningService;
    private static $conversationHistory = [];

    public function __construct(ChatbotLearningService $learningService, OllamaChatbotService $ollamaService)
    {
        $this->learningService = $learningService;
        $this->ollamaService = $ollamaService;
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
            if ($this->ollamaService->isInappropriateContent($question)) {
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
            
            // Nếu không tìm thấy câu trả lời phù hợp, gọi Ollama Service
            if (!$response) {
                $response = $this->ollamaService->generateResponse($question, $history);
            }
            
            // Tạo nút điều hướng
            $navigationButtons = $this->generateNavigationButtons($question);
            
            // Lưu lịch sử
            $interaction = $this->storeChatHistory($userId, $question, $response);
            
            // Kiểm tra nếu có yêu cầu đăng nhập trực tiếp
            if (is_array($response) && isset($response['direct_navigation'])) {
                return response()->json([
                    'success' => true,
                    'message' => $response['message'],
                    'navigation_buttons' => $navigationButtons,
                    'interaction_id' => $interaction->id,
                    'direct_navigation' => $response['direct_navigation']
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => is_array($response) ? $response['message'] : $response,
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
                
            case 'nutrition':
                // Xử lý câu hỏi về dinh dưỡng
                $entities = $analysis['entities'] ?? [];
                return $this->getNutritionInfo($entities);
                
            case 'diagnosis':
                // Xử lý câu hỏi về chẩn đoán bệnh và triệu chứng
                $entities = $analysis['entities'] ?? [];
                return $this->getDiagnosisInfo($entities, $question);
            
            case 'emergency':
                return [
                    'success' => true,
                    'message' => "🚨 TRƯỜNG HỢP KHẨN CẤP 🚨\n\nVui lòng gọi ngay số điện thoại cấp cứu: 0123.456.789\n\nChúng tôi luôn sẵn sàng hỗ trợ 24/7!"
                ];
            
            default:
                // Nếu không nhận dạng được ý định cụ thể, tìm kiếm từ các nguồn thông tin khác
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
                $response = "## ĐỘI NGŨ BÁC SĨ PETCARE\n\n";
                
                foreach ($bacSi as $bs) {
                    $chucVu = $bs->chuc_vu ? $bs->chuc_vu->ten_chuc_vu : 'Bác sĩ';
                    
                    // Tạo card thông tin bác sĩ theo mẫu trong ảnh
                    $response .= "### " . ($chucVu ? "Ths BS. " : "BS. ") . $bs->ten_nv . "\n\n";
                    $response .= "**Chuyên khoa:** " . ($chucVu ?: "Thú y") . "\n";
                    
                    if (!empty($bs->mo_ta)) {
                        $response .= "**Mô tả:** " . $bs->mo_ta . "\n";
                    } else {
                        // Tạo mô tả mặc định nếu không có
                        $response .= "**Mô tả:** Tốt nghiệp Đại học Nông Lâm TPHCM, bác sĩ " . $bs->ten_nv . " có thể mạnh trong việc điều trị các bệnh lý nội khoa như tiêu hóa, hô hấp và da liễu cho chó mèo.\n";
                    }
                    
                    if (!empty($bs->gioi_tinh)) {
                        $response .= "**Giới tính:** " . ($bs->gioi_tinh == 1 ? "Nam" : "Nữ") . "\n";
                    }
                    
                    $response .= "**Tình trạng:** Đang hoạt động\n\n";
                    $response .= "---\n\n";
                }
                
                $response .= "Bạn có thể đặt lịch khám với bác sĩ mình mong muốn thông qua mục đặt lịch. Đội ngũ bác sĩ của chúng tôi luôn sẵn sàng phục vụ và chăm sóc thú cưng của bạn với sự tận tâm nhất! 🩺\n\n";
                
                // Thêm nút điều hướng
                $navigationButtons = [
                    [
                        'text' => 'Xem chi tiết đội ngũ bác sĩ',
                        'route' => '/client/xem-bs/0',
                        'icon' => '👨‍⚕️'
                    ],
                    [
                        'text' => 'Đặt lịch khám',
                        'route' => '/client/dat-lich',
                        'icon' => '📅'
                    ]
                ];
                
                return [
                    'success' => true,
                    'message' => $response,
                    'navigation_buttons' => $navigationButtons
                ];
            }
        } catch (Exception $e) {
            Log::error('Error getting doctor info: ' . $e->getMessage());
        }
                    
        return [
            'success' => true,
            'message' => "Phòng khám của chúng tôi có đội ngũ bác sĩ giàu kinh nghiệm trong lĩnh vực thú y. Bạn có thể xem thông tin chi tiết về các bác sĩ trong mục 'Đội ngũ bác sĩ' trên trang web hoặc đặt lịch khám trực tiếp. 👨‍⚕️",
            'navigation_buttons' => [
                [
                    'text' => 'Xem đội ngũ bác sĩ',
                    'route' => '/client/xem-bs/0',
                    'icon' => '👨‍⚕️'
                ]
            ]
        ];
    }

    private function searchFromWeb($question)
    {
        $q = mb_strtolower($question, 'UTF-8');

        // Phân tích trước để xác định liệu có phải câu hỏi về triệu chứng hay dinh dưỡng
        $analysis = $this->analyzeQuestion($question);
        
        // Nếu câu hỏi liên quan đến dinh dưỡng
        if ($analysis['intent'] === 'nutrition') {
            return $this->getNutritionInfo($analysis['entities'] ?? []);
        }
        
        // Nếu câu hỏi liên quan đến chẩn đoán
        if ($analysis['intent'] === 'diagnosis') {
            return $this->getDiagnosisInfo($analysis['entities'] ?? [], $question);
        }
        
        // Xử lý các trường hợp triệu chứng cụ thể khi không phải là intent rõ ràng
        // Nhận diện triệu chứng phổ biến
        if (preg_match('/(sổ mũi|sổ mui|chảy nước mũi|hắt hơi|ho|khò khè|nghẹt mũi|thở khò khè)/ui', $q)) {
            // Chuyển hướng đến phương thức getDiagnosisInfo với entity 'respiratory'
            return $this->getDiagnosisInfo(['symptom' => 'respiratory'], $question);
        }
        // Nhận diện triệu chứng tiêu chảy, nôn, bỏ ăn
        if (preg_match('/(tiêu chảy|nôn|ói|mửa|bỏ ăn|không ăn|biếng ăn|phân lỏng|đi ngoài|bụng trướng|đầy hơi|táo bón)/ui', $q)) {
            // Xác định triệu chứng phổ biến nhất
            if (preg_match('/(tiêu chảy|phân lỏng|đi ngoài)/ui', $q)) {
                return $this->getDiagnosisInfo(['symptom' => 'diarrhea'], $question);
            } 
            else if (preg_match('/(nôn|ói|mửa)/ui', $q)) {
                return $this->getDiagnosisInfo(['symptom' => 'vomit'], $question);
            }
            else if (preg_match('/(bỏ ăn|không ăn|biếng ăn)/ui', $q)) {
                return $this->getDiagnosisInfo(['symptom' => 'appetite'], $question);
            }
            else {
                return $this->getDiagnosisInfo(['symptom' => 'unknown'], $question);
            }
        }
        // Nhận diện triệu chứng ngứa, rụng lông, gãi
        if (preg_match('/(ngứa|gãi|rụng lông|nấm|ghẻ|vảy|da đỏ|da khô|viêm da|mảng đỏ|mụn|nổi cục)/ui', $q)) {
            return $this->getDiagnosisInfo(['symptom' => 'skin'], $question);
        }
        // Nhận diện triệu chứng đau mắt, chảy nước mắt, đỏ mắt
        if (preg_match('/(đau mắt|chảy nước mắt|mắt đỏ|mắt sưng|mắt mờ|mắt đục|ghèn|viêm mắt|mắt nhắm|không mở được mắt)/ui', $q)) {
            return $this->getDiagnosisInfo(['symptom' => 'eye'], $question);
        }
        // Nhận diện triệu chứng đau tai, lắc đầu, ngứa tai
        if (preg_match('/(ngứa tai|gãi tai|lắc đầu|hôi tai|viêm tai|ráy tai|chảy mủ tai|tai đỏ|tai sưng)/ui', $q)) {
            return $this->getDiagnosisInfo(['symptom' => 'ear'], $question);
        }
        // Nhận diện triệu chứng về tiết niệu
        if (preg_match('/(tiểu khó|đi tiểu nhiều|tiểu ra máu|không đi tiểu được|tiểu nhỏ giọt|tiểu thường xuyên|tiểu ngoài khay)/ui', $q)) {
            return $this->getDiagnosisInfo(['symptom' => 'urinary'], $question);
        }
        // Nhận diện triệu chứng về xương khớp, đi khập khiễng
        if (preg_match('/(đi khập khiễng|đau khớp|sưng khớp|không đi được|khó đứng|khó leo cầu thang|đau lưng|không nhảy được|tê liệt|yếu chân|run chân|cõi xương|gãy chân)/ui', $q)) {
            return $this->getDiagnosisInfo(['symptom' => 'leg'], $question);
        }
        
        // Nhận diện câu hỏi về dinh dưỡng nếu không trùng với các pattern trên
        if (preg_match('/(thức ăn|đồ ăn|dinh dưỡng|cho ăn|ăn gì|khẩu phần|thức ăn)/ui', $q)) {
            // Xác định loại thú cưng
            $entities = [];
            if (preg_match('/(chó|dog|cún|puppies|puppy)/ui', $q)) {
                $entities['pet_type'] = 'dog';
            } 
            else if (preg_match('/(mèo|cat|kitty|kitten|con meo)/ui', $q)) {
                $entities['pet_type'] = 'cat';
            }
            
            // Xác định độ tuổi
            if (preg_match('/(con|nhỏ|sơ sinh|mới sinh|puppy|kitten)/ui', $q)) {
                $entities['age'] = 'baby';
            } 
            else if (preg_match('/(già|cao tuổi|lớn tuổi|senior)/ui', $q)) {
                $entities['age'] = 'senior';
            }
            
            return $this->getNutritionInfo($entities);
        }
        // Nếu không khớp triệu chứng nào, gọi Ollama để trả lời
        try {
            Log::info('Calling Ollama for unconventional question', ['question' => $question]);
            $aiResponse = $this->ollamaService->generateResponse($question, []);
            
            // Log the response
            Log::info('Received AI response', ['response' => $aiResponse]);

            // Kiểm duyệt tự động (nếu cần)
            if ($this->ollamaService->isInappropriateContent($aiResponse)) {
                Log::warning('AI response filtered by inappropriate content filter', ['response' => $aiResponse]);
                return [
                    'success' => true,
                    'message' => 'Xin lỗi, tôi chưa có thông tin phù hợp để trả lời câu hỏi này. Bạn vui lòng liên hệ trực tiếp với phòng khám để được hỗ trợ nhé!'
                ];
            }
                    
            return [
                'success' => true,
                'message' => $aiResponse
            ];
        } catch (\Exception $e) {
            Log::error('Error calling Ollama API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to a generic response about pet health
            return [
                'success' => true,
                'message' => 'Xin lỗi, tôi gặp khó khăn khi xử lý câu hỏi của bạn. Với các vấn đề sức khỏe của thú cưng, tốt nhất nên đến gặp bác sĩ thú y để được khám và tư vấn trực tiếp. Bạn có thể đặt lịch khám ở mục "Đặt lịch" trên trang web của chúng tôi.'
            ];
        }
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

        // 1. Ưu tiên lấy pattern từ database
        try {
            $patterns = \App\Models\ChatbotPattern::orderByDesc('confidence')->orderByDesc('updated_at')->get();
            foreach ($patterns as $pattern) {
                if (preg_match('/' . $pattern->pattern . '/ui', $questionLower, $matches)) {
                    $analysis['intent'] = $pattern->intent;
                    if (!empty($pattern->entities)) {
                        $analysis['entities'] = $pattern->entities;
                    }
                    // Nếu pattern có group, lấy entity động
                    if (isset($matches[1])) {
                        $analysis['entities']['matched'] = $matches[1];
                    }
                    return $analysis;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error loading chatbot patterns: ' . $e->getMessage());
        }

        // 2. Nếu không khớp pattern nào, fallback về logic cũ (giữ nguyên phần code hard-code của bạn ở đây)
        // Phân tích ý định
        if (preg_match('/(giá|phí|chi phí|bao nhiêu tiền)/ui', $questionLower)) {
            $analysis['intent'] = 'price';
        } 
        elseif (preg_match('/(đặt lịch|hẹn|lịch khám|khám)/ui', $questionLower)) {
            $analysis['intent'] = 'booking';
        } 
        elseif (preg_match('/(bác sĩ|y tá|nhân viên|đội ngũ|bs|thầy thuốc|thú y)/ui', $questionLower)) {
            $analysis['intent'] = 'doctor';
        } 
        elseif (preg_match('/(dịch vụ|trang dịch vụ|vào trang dịch vụ|xem dịch vụ|chọn dịch vụ)/ui', $questionLower)) {
            $analysis['intent'] = 'service';
            $analysis['entities']['direct_navigation'] = true;
        } 
        elseif (preg_match('/(cấp cứu|khẩn cấp|nguy hiểm)/ui', $questionLower)) {
            $analysis['intent'] = 'emergency';
        }
        // Thêm nhận diện chi tiết về dinh dưỡng
        elseif (preg_match('/(dinh dưỡng|thức ăn|đồ ăn|chế độ ăn|cho ăn|uống|vitamin|thực phẩm|supplement|khẩu phần|ăn gì|an gi|cho ăn|cho an|thực đơn|thuc don|chế độ ăn|che do an|đồ ăn|do an|khẩu phần|khau phan|bữa ăn|bua an|bữa sáng|bua sang|bữa trưa|bua trua|bữa tối|bua toi|dinh dưỡng)/ui', $questionLower)) {
            $analysis['intent'] = 'nutrition';
            
            // Phân tích loại thú cưng
            if (preg_match('/(chó|dog|cún|cho|cún cưng|con chó|puppies|puppy)/ui', $questionLower)) {
                $analysis['entities']['pet_type'] = 'dog';
            } else if (preg_match('/(mèo|cat|meow|meo|con mèo|con meo|kitty|kitten)/ui', $questionLower)) {
                $analysis['entities']['pet_type'] = 'cat';
            }
            
            // Phân tích độ tuổi
            if (preg_match('/(con|nhỏ|sơ sinh|mới sinh|mới đẻ|moi de|mới mua|puppy|kitten|mới nuôi|moi nuoi)/ui', $questionLower)) {
                $analysis['entities']['age'] = 'baby';
            } else if (preg_match('/(già|cao tuổi|lớn tuổi|old|senior)/ui', $questionLower)) {
                $analysis['entities']['age'] = 'senior';
            } else {
                $analysis['entities']['age'] = 'adult'; // mặc định
            }
            
            // Phân tích thêm về vấn đề dinh dưỡng cụ thể
            if (preg_match('/(khô|hạt|hat|dry food|khô)/ui', $questionLower)) {
                $analysis['entities']['food_type'] = 'dry';
            } else if (preg_match('/(ướt|pate|wet food|đóng hộp|dong hop|lon|đóng lon|dong lon)/ui', $questionLower)) {
                $analysis['entities']['food_type'] = 'wet';
            } else if (preg_match('/(tự nấu|tu nau|tự làm|tu lam|thức ăn nhà|thuc an nha|homemade)/ui', $questionLower)) {
                $analysis['entities']['food_type'] = 'homemade';
            }
        }
        // Nhận diện câu hỏi về chẩn đoán bệnh hoặc triệu chứng
        elseif (preg_match('/(bệnh|benh|ốm|om|triệu chứng|trieu chung|đau|dau|sưng|sung|chẩn đoán|chan doan|chẩn bệnh|chan benh|khám bệnh|kham benh|khỏi bệnh|khoi benh|điều trị|dieu tri|chữa|chua|thuốc|thuoc|đỡ bệnh|do benh|thầy thuốc|thay thuoc|viêm|viem|nhiễm|nhiem|bị|bi|cõi xương|khập khiễng|gãy|chân đau)/ui', $questionLower)) {
            $analysis['intent'] = 'diagnosis';
            
            // Phân tích loại thú cưng
            if (preg_match('/(chó|dog|cún|cho|cún cưng|con chó|puppies|puppy)/ui', $questionLower)) {
                $analysis['entities']['pet_type'] = 'dog';
            } else if (preg_match('/(mèo|cat|meow|meo|con mèo|con meo|kitty|kitten)/ui', $questionLower)) {
                $analysis['entities']['pet_type'] = 'cat';
            }
            
            // Phân tích các triệu chứng cụ thể
            if (preg_match('/(tiêu chảy|tieu chay|đi ngoài|di ngoai|phân lỏng|phan long|đi ỉa|di ia|đi nhiều lần|di nhieu lan)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'diarrhea';
            } else if (preg_match('/(nôn|non|ói|oi|mửa|mua|buồn nôn|buon non)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'vomit';
            } else if (preg_match('/(ho|sổ mũi|so mui|hắt hơi|hat hoi|khò khè|kho khe|thở|tho|thở khó|tho kho|nghẹt mũi|nghet mui|chảy nước mũi|chay nuoc mui)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'respiratory';
            } else if (preg_match('/(ngứa|ngua|gãi|gai|rụng lông|rung long|lông rụng|long rung|da|ghẻ|ghe|vẩy|vay|nấm|nam|viêm da|viem da|mảng đỏ|mang do|mụn|mun|nổi cục|noi cuc)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'skin';
            } else if (preg_match('/(không ăn|khong an|biếng ăn|bieng an|bỏ ăn|bo an|chán ăn|chan an|không thèm ăn|khong them an)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'appetite';
            } else if (preg_match('/(tiểu|tieu|đái|dai|đi tiểu|di tieu|tiểu ra máu|tieu ra mau|tiểu nhiều|tieu nhieu|tiểu khó|tieu kho|không đi tiểu được|khong di tieu duoc)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'urinary';
            } else if (preg_match('/(đau mắt|dau mat|mắt đỏ|mat do|mắt sưng|mat sung|chảy nước mắt|chay nuoc mat|ghèn|ghen|viêm mắt|viem mat)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'eye';
            } else if (preg_match('/(lắc đầu|lac dau|ngứa tai|ngua tai|hôi tai|hoi tai|viêm tai|viem tai|tai đỏ|tai do|ráy tai|ray tai)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'ear';
            } else if (preg_match('/(đi khập khiễng|di khap khieng|đau chân|dau chan|sưng chân|sung chan|gãy|gay|khớp|khop|xương|xuong|bong gân|bong gan|trật khớp|trat khop|cõi xương|gãy chân)/ui', $questionLower)) {
                $analysis['entities']['symptom'] = 'leg';
            }
        }
        
        // Phát hiện thêm các entity từ câu hỏi
        // Nhận diện loại dịch vụ
        if (preg_match('/(spa|tắm|gội|cắt lông|cat long|tam|goi|vệ sinh|ve sinh)/ui', $questionLower)) {
            $analysis['entities']['service'] = 'spa';
        } else if (preg_match('/(tiêm phòng|vaccine|vắc xin|vac xin|tiem phong|phòng bệnh)/ui', $questionLower)) {
            $analysis['entities']['service'] = 'vaccine';
        } else if (preg_match('/(phẫu thuật|phau thuat|mổ|mo|triệt sản|triet san|cắt|cat)/ui', $questionLower)) {
            $analysis['entities']['service'] = 'surgery';
        } else if (preg_match('/(khám|kham|tư vấn|tu van|chẩn đoán|chan doan)/ui', $questionLower) && !isset($analysis['entities']['service'])) {
            $analysis['entities']['service'] = 'exam';
        }

        return $analysis;
    }

    private function getContextualResponse($question, $analysis, $history) {
        // Chỉ kiểm tra hội thoại cũ nếu intent không phải là điều hướng
        $navigationIntents = [
            'navigation', 'service', 'doctor', 'login', 'booking', 'price', 'home', 'emergency', 'logout', 'confirm_navigation',
            'nutrition', 'diagnosis' // Thêm intent mới cho dinh dưỡng và chẩn đoán
        ];
        if (!in_array($analysis['intent'], $navigationIntents)) {
            $similarQuestion = $this->findSimilarQuestion($question, $history);
            if ($similarQuestion) {
                return $similarQuestion['response'];
            }
        }

        // Xử lý các intent điều hướng đều trả về direct_navigation ngay lập tức
        switch ($analysis['intent']) {
            case 'navigation':
                $targetPage = $analysis['entities']['target_page'] ?? '';
                switch ($targetPage) {
                    case 'service':
                        $resp = [
                            'success' => true,
                            'message' => "Đã sẵn sàng! Mình sẽ đưa bạn đến trang chọn dịch vụ ngay nhé. Nếu bạn muốn xem thêm dịch vụ khác, hãy hỏi mình bất cứ lúc nào 🐾",
                            'direct_navigation' => '/client/xem-dich-vu',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Chọn dịch vụ ngay',
                                    'route' => '/client/xem-dich-vu',
                                    'icon' => '✅'
                                ]
                            ]
                        ];
                        break;
                    case 'login':
                        $resp = [
                            'success' => true,
                            'message' => "Bạn cần đăng nhập để tiếp tục. Mình sẽ chuyển bạn đến trang đăng nhập nhé!",
                            'direct_navigation' => '/client/dang-nhap-dang-ky',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Đăng nhập ngay',
                                    'route' => '/client/dang-nhap-dang-ky',
                                    'icon' => '🔑'
                                ]
                            ]
                        ];
                        break;
                    case 'doctor':
                        $resp = [
                            'success' => true,
                            'message' => "Mình sẽ đưa bạn đến trang danh sách bác sĩ nhé! Nếu cần tư vấn thêm về bác sĩ nào, bạn cứ hỏi mình nha 👨‍⚕️",
                            'direct_navigation' => '/client/xem-bs/0',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Xem danh sách bác sĩ',
                                    'route' => '/client/xem-bs/0',
                                    'icon' => '👨‍⚕️'
                                ]
                            ]
                        ];
                        break;
                    case 'booking':
                        $resp = [
                            'success' => true,
                            'message' => "Bạn muốn đặt lịch khám cho thú cưng? Mình sẽ chuyển bạn đến trang đặt lịch ngay nhé! 📅",
                            'direct_navigation' => '/client/dat-lich',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Đặt lịch khám',
                                    'route' => '/client/dat-lich',
                                    'icon' => '📅'
                                ]
                            ]
                        ];
                        break;
                    case 'price':
                        $resp = [
                            'success' => true,
                            'message' => "Bạn muốn xem bảng giá dịch vụ? Mình sẽ chuyển bạn đến trang bảng giá ngay nhé! 💰",
                            'direct_navigation' => '/client/bang-gia',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Xem bảng giá',
                                    'route' => '/client/bang-gia',
                                    'icon' => '💰'
                                ]
                            ]
                        ];
                        break;
                    case 'home':
                        $resp = [
                            'success' => true,
                            'message' => "Mình sẽ đưa bạn về trang chủ nhé! Nếu cần hỗ trợ gì thêm, bạn cứ hỏi mình nha 🏠",
                            'direct_navigation' => '/',
                            'navigation_buttons' => [
                                [
                                    'text' => 'Trang chủ',
                                    'route' => '/',
                                    'icon' => '🏠'
                                ]
                            ]
                        ];
                        break;
                    default:
                        $resp = null;
                }
                if ($resp && isset($resp['direct_navigation'])) {
                    session(['last_navigation_intent' => $resp['direct_navigation']]);
                }
                return $resp;
            case 'service':
                // Luôn trả về direct_navigation cho intent service
                $resp = [
                    'success' => true,
                    'message' => "Đã sẵn sàng! Mình sẽ đưa bạn đến trang chọn dịch vụ ngay nhé. Nếu bạn muốn xem thêm dịch vụ khác, hãy hỏi mình bất cứ lúc nào 🐾",
                    'direct_navigation' => '/client/xem-dich-vu',
                    'navigation_buttons' => [
                        [
                            'text' => 'Chọn dịch vụ ngay',
                            'route' => '/client/xem-dich-vu',
                            'icon' => '✅'
                        ]
                    ]
                ];
                session(['last_navigation_intent' => '/client/xem-dich-vu']);
                return $resp;
            case 'doctor':
                $resp = [
                    'success' => true,
                    'message' => "Mình sẽ đưa bạn đến trang danh sách bác sĩ nhé! Nếu cần tư vấn thêm về bác sĩ nào, bạn cứ hỏi mình nha 👨‍⚕️",
                    'direct_navigation' => '/client/xem-bs/0',
                    'navigation_buttons' => [
                        [
                            'text' => 'Xem danh sách bác sĩ',
                            'route' => '/client/xem-bs/0',
                            'icon' => '👨‍⚕️'
                        ]
                    ]
                ];
                session(['last_navigation_intent' => '/client/xem-bs/0']);
                return $resp;
            case 'login':
                $resp = [
                    'success' => true,
                    'message' => "Bạn cần đăng nhập để tiếp tục. Mình sẽ chuyển bạn đến trang đăng nhập nhé!",
                    'direct_navigation' => '/client/dang-nhap-dang-ky',
                    'navigation_buttons' => [
                        [
                            'text' => 'Đăng nhập ngay',
                            'route' => '/client/dang-nhap-dang-ky',
                            'icon' => '🔑'
                        ]
                    ]
                ];
                session(['last_navigation_intent' => '/client/dang-nhap-dang-ky']);
                return $resp;
            case 'booking':
                $resp = [
                    'success' => true,
                    'message' => "Bạn muốn đặt lịch khám cho thú cưng? Mình sẽ chuyển bạn đến trang đặt lịch ngay nhé! 📅",
                    'direct_navigation' => '/client/dat-lich',
                    'navigation_buttons' => [
                        [
                            'text' => 'Đặt lịch khám',
                            'route' => '/client/dat-lich',
                            'icon' => '📅'
                        ]
                    ]
                ];
                session(['last_navigation_intent' => '/client/dat-lich']);
                return $resp;
            case 'price':
                return $this->getPriceInfo();
            case 'home':
                $resp = [
                    'success' => true,
                    'message' => "Mình sẽ đưa bạn về trang chủ nhé! Nếu cần hỗ trợ gì thêm, bạn cứ hỏi mình nha 🏠",
                    'direct_navigation' => '/',
                    'navigation_buttons' => [
                        [
                            'text' => 'Trang chủ',
                            'route' => '/',
                            'icon' => '🏠'
                        ]
                    ]
                ];
                session(['last_navigation_intent' => '/']);
                return $resp;
            case 'emergency':
                return $this->getEmergencyInfo();
            case 'logout':
                // Trả về chỉ dẫn cho FE gọi API logout và nút Đăng nhập lại
                return [
                    'success' => true,
                    'message' => 'Mình đã đăng xuất tài khoản của bạn khỏi hệ thống rồi ạ. 😊',
                    'direct_action' => 'logout',
                    'navigation_buttons' => [
                        [
                            'text' => 'Đăng nhập lại',
                            'route' => '/client/dang-nhap-dang-ky',
                            'icon' => '🔑'
                        ]
                    ]
                ];
            case 'confirm_navigation':
                $lastNav = session('last_navigation_intent');
                if (!$lastNav) {
                    // Tìm trong history hội thoại gần nhất có direct_navigation
                    foreach (array_reverse($history) as $item) {
                        if (isset($item['direct_navigation'])) {
                            $lastNav = $item['direct_navigation'];
                            break;
                        }
                    }
                }
                if ($lastNav) {
                    return [
                        'success' => true,
                        'message' => 'Đang chuyển bạn đến trang bạn vừa yêu cầu nhé! Nếu cần hỗ trợ gì thêm, bạn cứ hỏi mình nha 😊',
                        'direct_navigation' => $lastNav
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Bạn muốn vào trang nào? Hãy nói rõ hơn nhé!'
                ];
            
            // Xử lý tư vấn dinh dưỡng
            case 'nutrition':
                return $this->getNutritionInfo($analysis['entities'] ?? []);
            
            // Xử lý chẩn đoán sơ bộ
            case 'diagnosis':
                return $this->getDiagnosisInfo($analysis['entities'] ?? [], $question);
                
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
        $message .= "Bạn muốn đặt lịch cho dịch vụ nào? 🐾";
        
        return $message;
    }

    private function getEmergencyInfo() {
        $message = "🚨 TRƯỜNG HỢP KHẨN CẤP 🚨\n\n";
        $message .= "Vui lòng gọi ngay số điện thoại cấp cứu: 0123.456.789\n\n";
        $message .= "Chúng tôi luôn sẵn sàng hỗ trợ 24/7!";
        
        return $message;
    }

    /**
     * Cung cấp thông tin dinh dưỡng dựa trên loại thú cưng và độ tuổi
     * @param array $entities Thông tin phân tích từ câu hỏi
     * @return array Thông tin dinh dưỡng
     */
    private function getNutritionInfo($entities = []) 
    {
        $petType = $entities['pet_type'] ?? 'general';
        $age = $entities['age'] ?? 'adult';
        
        // Thông tin chung về dinh dưỡng
        $generalInfo = "Để thú cưng của bạn có một sức khỏe tốt, chế độ dinh dưỡng cần đáp ứng đủ các nhóm dinh dưỡng thiết yếu:\n\n";
        $generalInfo .= "• Protein (đạm): xây dựng và phục hồi cơ bắp\n";
        $generalInfo .= "• Chất béo: cung cấp năng lượng và hỗ trợ hấp thụ vitamin\n";
        $generalInfo .= "• Carbohydrate: năng lượng cho hoạt động hàng ngày\n";
        $generalInfo .= "• Vitamin và khoáng chất: hỗ trợ chức năng miễn dịch và trao đổi chất\n";
        $generalInfo .= "• Nước: luôn đảm bảo thú cưng được uống đủ nước sạch\n\n";
        
        $specificInfo = "";
        
        // Thông tin riêng cho chó
        if ($petType == 'dog') {
            if ($age == 'baby') {
                $specificInfo = "Với chó con (dưới 1 tuổi):\n";
                $specificInfo .= "• Nên cho ăn thức ăn dành riêng cho chó con với hàm lượng protein cao hơn (khoảng 25-30%)\n";
                $specificInfo .= "• Chia nhỏ bữa ăn: 3-4 bữa/ngày cho chó dưới 3 tháng, 2-3 bữa/ngày cho chó 3-6 tháng\n";
                $specificInfo .= "• Không nên cho ăn thức ăn người vì có thể gây rối loạn tiêu hóa\n";
                $specificInfo .= "• Bổ sung DHA giúp phát triển não bộ và thị lực\n";
                $specificInfo .= "• Thức ăn cần được làm mềm hoặc chọn loại phù hợp với kích thước\n";
            } elseif ($age == 'senior') {
                $specificInfo = "Với chó già (trên 7 tuổi):\n";
                $specificInfo .= "• Giảm lượng calo nhưng vẫn đảm bảo đủ protein chất lượng cao\n";
                $specificInfo .= "• Bổ sung glucosamine và chondroitin cho sức khỏe xương khớp\n";
                $specificInfo .= "• Thức ăn dễ tiêu hóa, ít muối\n";
                $specificInfo .= "• Thêm omega-3 giúp giảm viêm và hỗ trợ não bộ\n";
                $specificInfo .= "• Ăn nhiều bữa nhỏ trong ngày (2-3 bữa)\n";
            } else {
                $specificInfo = "Với chó trưởng thành:\n";
                $specificInfo .= "• Protein chất lượng cao từ thịt, cá, trứng (20-25% khẩu phần)\n";
                $specificInfo .= "• Cần cân đối giữa thức ăn khô và thức ăn ướt\n";
                $specificInfo .= "• Chia 1-2 bữa/ngày tùy kích thước giống chó\n";
                $specificInfo .= "• Đảm bảo lượng thức ăn phù hợp với mức độ hoạt động\n";
                $specificInfo .= "• Thực phẩm nên tránh: chocolate, nho, nho khô, hành, tỏi, cà phê, rượu\n";
            }
        }
        // Thông tin riêng cho mèo
        else if ($petType == 'cat') {
            if ($age == 'baby') {
                $specificInfo = "Với mèo con (dưới 1 tuổi):\n";
                $specificInfo .= "• Cần nhiều protein (khoảng 30-40%) và chất béo để phát triển\n";
                $specificInfo .= "• Chia 3-4 bữa nhỏ mỗi ngày\n";
                $specificInfo .= "• Thức ăn đặc biệt cho mèo con hoặc thức ăn ướt phù hợp với răng nhỏ\n";
                $specificInfo .= "• Bổ sung taurine - axit amin thiết yếu cho mèo\n";
                $specificInfo .= "• Khởi đầu với thức ăn ướt, sau đó kết hợp với thức ăn khô\n";
            } elseif ($age == 'senior') {
                $specificInfo = "Với mèo già (trên 11 tuổi):\n";
                $specificInfo .= "• Protein dễ tiêu hóa, ít chất béo, ít phosphor\n";
                $specificInfo .= "• Bổ sung các axit béo omega-3 hỗ trợ khớp và não\n";
                $specificInfo .= "• Thêm vitamin E và các chất chống oxy hóa\n";
                $specificInfo .= "• Ăn nhiều bữa nhỏ trong ngày với thức ăn mềm\n";
                $specificInfo .= "• Đảm bảo đủ nước vì mèo già dễ bị mất nước\n";
            } else {
                $specificInfo = "Với mèo trưởng thành:\n";
                $specificInfo .= "• Protein cao (35-40%) từ thịt, cá là thiết yếu\n";
                $specificInfo .= "• Mèo là động vật ăn thịt bắt buộc, không thể theo chế độ ăn chay\n";
                $specificInfo .= "• Cần bổ sung taurine đầy đủ\n";
                $specificInfo .= "• Chia 2-3 bữa nhỏ mỗi ngày\n";
                $specificInfo .= "• Thức ăn nên tránh: sữa, chocolate, cà phê, rượu, hành, tỏi\n";
            }
        }
        // Thông tin chung nếu không xác định được loại thú cưng hoặc tuổi
        else {
            $specificInfo = "Lời khuyên chung cho thú cưng:\n";
            $specificInfo .= "• Sử dụng thức ăn chất lượng cao phù hợp với loài, tuổi và kích thước\n";
            $specificInfo .= "• Chuyển đổi thức ăn mới từ từ để tránh rối loạn tiêu hóa\n";
            $specificInfo .= "• Không cho ăn quá nhiều đồ ăn vặt và đồ thừa từ bàn ăn\n";
            $specificInfo .= "• Tham khảo ý kiến bác sĩ thú y về chế độ ăn phù hợp\n";
            $specificInfo .= "• Đảm bảo luôn có nước sạch\n";
        }
        
        // Kết luận và kêu gọi hành động
        $conclusion = "\nĐể có chế độ dinh dưỡng tối ưu được cá nhân hóa cho thú cưng của bạn, hãy đặt lịch tư vấn với bác sĩ thú y tại PetCare. Chúng tôi có thể đánh giá tình trạng sức khỏe và nhu cầu riêng của thú cưng để đưa ra phương án dinh dưỡng phù hợp nhất. 🍲";
        
        // Thêm thông tin nguồn tham khảo uy tín
        $sources = "\n\n📚 Nguồn tham khảo:\n";
        $sources .= "• Hiệp hội Dinh dưỡng Thú cưng Thế giới (WSAVA): https://wsava.org/guidelines/global-nutrition-guidelines/\n";
        $sources .= "• Hiệp hội Bác sĩ Thú y Hoa Kỳ (AVMA): https://www.avma.org/resources/pet-owners/petcare/nutrition\n";
        $sources .= "• Journal of Animal Science: https://academic.oup.com/jas\n";
        $sources .= "• Journal of Feline Medicine and Surgery: https://journals.sagepub.com/home/jfm\n";
        $sources .= "• Trung tâm Dinh dưỡng Thú cưng Đại học Cornell: https://www.vet.cornell.edu/departments-centers-and-institutes/cornell-university-hospital-animals/departments/clinical-nutrition";
        
        // Tạo nút điều hướng đến trang đặt lịch
        $navigationButtons = [
            [
                'text' => 'Đặt lịch tư vấn dinh dưỡng',
                'route' => '/client/dat-lich',
                'icon' => '📅'
            ]
        ];
        
        return [
            'success' => true,
            'message' => $generalInfo . $specificInfo . $conclusion . $sources,
            'navigation_buttons' => $navigationButtons
        ];
    }

    /**
     * Cung cấp thông tin chẩn đoán sơ bộ dựa vào triệu chứng
     * @param array $entities Thông tin phân tích từ câu hỏi
     * @param string $question Câu hỏi gốc của người dùng
     * @return array|string Thông tin chẩn đoán
     */
    private function getDiagnosisInfo($entities = [], $question) 
    {
        $symptom = $entities['symptom'] ?? 'unknown';
        $info = "";
        $severity = "medium"; // mức độ nghiêm trọng: low, medium, high
        
        switch ($symptom) {
            case 'diarrhea':
                $info = "Tiêu chảy ở thú cưng có thể do nhiều nguyên nhân:\n\n";
                $info .= "• Thay đổi thức ăn đột ngột\n";
                $info .= "• Nhiễm ký sinh trùng đường ruột\n";
                $info .= "• Ăn phải thức ăn không phù hợp hoặc có hại\n";
                $info .= "• Nhiễm trùng đường tiêu hóa\n";
                $info .= "• Stress hoặc lo âu\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Nhịn ăn 12-24 giờ (chỉ áp dụng cho thú cưng trưởng thành)\n";
                $info .= "• Đảm bảo cung cấp đủ nước để tránh mất nước\n";
                $info .= "• Sau khi nhịn ăn, cho ăn thức ăn dễ tiêu như cơm trắng với thịt gà luộc\n";
                $info .= "• Chia thành nhiều bữa nhỏ\n";
                $severity = "medium";
                break;
                
            case 'vomit':
                $info = "Nôn ở thú cưng có thể do nhiều nguyên nhân:\n\n";
                $info .= "• Ăn quá nhanh hoặc quá nhiều\n";
                $info .= "• Dị ứng hoặc không dung nạp thức ăn\n";
                $info .= "• Nhiễm trùng đường tiêu hóa\n";
                $info .= "• Ăn phải dị vật\n";
                $info .= "• Bệnh về nội tạng\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Ngừng cho ăn trong 12 giờ\n";
                $info .= "• Cung cấp nước nhỏ giọt và thường xuyên\n";
                $info .= "• Sau đó cho ăn thức ăn mềm, dễ tiêu với khẩu phần nhỏ\n";
                $severity = "medium";
                break;
                
            case 'respiratory':
                $info = "Các vấn đề về hô hấp như ho, hắt hơi, sổ mũi có thể do:\n\n";
                $info .= "• Cảm lạnh thông thường\n";
                $info .= "• Dị ứng mùa\n";
                $info .= "• Nhiễm trùng đường hô hấp trên\n";
                $info .= "• Kennel cough (đối với chó)\n";
                $info .= "• Bệnh mèo mũi chảy nước mắt (đối với mèo)\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Giữ thú cưng trong môi trường ấm áp, tránh gió lùa\n";
                $info .= "• Đảm bảo không gian sống sạch sẽ, thông thoáng\n";
                $info .= "• Làm ẩm đường thở bằng cách cho thú cưng vào phòng có máy tạo độ ẩm\n";
                $info .= "• Vệ sinh mũi bằng nước muối sinh lý\n";
                $severity = "medium";
                break;
                
            case 'skin':
                $info = "Các vấn đề về da như ngứa, lở, ghẻ, rụng lông có thể do:\n\n";
                $info .= "• Viêm da dị ứng (thức ăn, môi trường)\n";
                $info .= "• Ký sinh trùng ngoài da (ve, bọ chét, ghẻ)\n";
                $info .= "• Nhiễm nấm da\n";
                $info .= "• Viêm da tiết bã nhờn\n";
                $info .= "• Tự miễn dịch\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Tắm bằng sữa tắm dịu nhẹ dành cho thú cưng\n";
                $info .= "• Ngăn không cho thú cưng cào gãi vùng bị ảnh hưởng\n";
                $info .= "• Sử dụng cổ áo Elizabeth nếu cần thiết\n";
                $info .= "• Tránh các chất kích ứng và thay đổi môi trường\n";
                $severity = "medium";
                break;
                
            case 'appetite':
                $info = "Biếng ăn, bỏ ăn có thể do nhiều nguyên nhân:\n\n";
                $info .= "• Stress hoặc thay đổi môi trường\n";
                $info .= "• Vấn đề về răng miệng\n";
                $info .= "• Rối loạn tiêu hóa\n";
                $info .= "• Cảm nhiệt, sốt\n";
                $info .= "• Bệnh lý tiềm ẩn\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Thử thay đổi thức ăn hấp dẫn hơn\n";
                $info .= "• Hâm nóng nhẹ thức ăn để tăng mùi thơm\n";
                $info .= "• Cho ăn bằng tay để khuyến khích\n";
                $info .= "• Kiểm tra miệng xem có vấn đề về răng không\n";
                $severity = "medium";
                break;
                
            case 'urinary':
                $info = "Các vấn đề đường tiết niệu như khó đi tiểu, tiểu ra máu rất nguy hiểm:\n\n";
                $info .= "• Viêm đường tiết niệu\n";
                $info .= "• Sỏi bàng quang hoặc niệu đạo\n";
                $info .= "• Bệnh thận\n";
                $info .= "• Tắc nghẽn niệu đạo (đặc biệt nguy hiểm ở mèo đực)\n\n";
                $info .= "⚠️ Đây là tình trạng KHẨN CẤP nếu thú cưng không thể đi tiểu!\n\n";
                $info .= "Cần đưa đến bác sĩ thú y NGAY LẬP TỨC, không trì hoãn!";
                $severity = "high";
                break;
                
            case 'eye':
                $info = "Vấn đề về mắt như đỏ, chảy nước mắt, ghèn có thể do:\n\n";
                $info .= "• Viêm kết mạc\n";
                $info .= "• Dị vật trong mắt\n";
                $info .= "• Nhiễm trùng\n";
                $info .= "• Dị ứng\n";
                $info .= "• Chấn thương\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Làm sạch mắt nhẹ nhàng bằng gạc sạch và nước muối sinh lý\n";
                $info .= "• Không để thú cưng cào vào mắt\n";
                $info .= "• Tránh sử dụng thuốc mắt người cho thú cưng\n";
                $severity = "medium";
                break;
                
            case 'ear':
                $info = "Các vấn đề về tai như ngứa tai, hôi tai, viêm tai có thể do:\n\n";
                $info .= "• Viêm tai ngoài\n";
                $info .= "• Nhiễm nấm men\n";
                $info .= "• Ve tai\n";
                $info .= "• Tích tụ ráy tai\n";
                $info .= "• Dị vật trong tai\n\n";
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• KHÔNG tự chữa trị viêm tai tại nhà\n";
                $info .= "• Ngăn không cho thú cưng gãi tai hoặc lắc đầu quá nhiều\n";
                $info .= "• Không cho nước vào tai\n";
                $info .= "• Không dùng tăm bông chọc sâu vào tai thú cưng\n";
                $severity = "medium";
                break;
                
            case 'leg':
                $info = "Thú cưng đi khập khiễng hoặc có vấn đề về chân có thể do:\n\n";
                $info .= "• Chấn thương: bong gân, rách dây chằng\n";
                $info .= "• Viêm khớp\n";
                $info .= "• Dị vật đâm vào chân/bàn chân\n";
                $info .= "• Gãy xương\n";
                $info .= "• Vấn đề thần kinh\n\n";
                
                if (strpos($question, 'cõi xương') !== false || strpos($question, 'khập khiễng') !== false) {
                    $info .= "🔍 Đối với chó bị cõi xương hoặc đi khập khiễng:\n";
                    $info .= "• Cho thú cưng nghỉ ngơi và hạn chế vận động mạnh\n";
                    $info .= "• Bổ sung thực phẩm giàu canxi và vitamin D (như sữa, phô mai, sữa chua)\n";
                    $info .= "• Bổ sung glucosamine và chondroitin (thực phẩm bổ sung cho xương khớp)\n";
                    $info .= "• Thức ăn giàu omega-3 giúp giảm viêm (dầu cá, cá hồi)\n";
                    $info .= "• Cần đưa thú cưng đến bác sĩ thú y để được chẩn đoán chính xác nguyên nhân\n\n";
                }
                
                $info .= "Giải pháp tạm thời:\n";
                $info .= "• Hạn chế vận động của thú cưng\n";
                $info .= "• Kiểm tra bàn chân xem có dị vật không\n";
                $info .= "• Chườm đá nếu có sưng tấy (15 phút mỗi lần)\n";
                $info .= "• Không tự dùng thuốc giảm đau người cho thú cưng\n";
                $severity = "medium";
                break;
                
            default:
                // Trường hợp không xác định được triệu chứng cụ thể
                $response = $this->ollamaService->generateResponse($question, []);
                if (!empty($response)) {
                    return [
                        'success' => true,
                        'message' => $response . "\n\n⚠️ Lưu ý: Đây chỉ là thông tin tham khảo. Để chẩn đoán chính xác, vui lòng đưa thú cưng đến khám trực tiếp tại phòng khám PetCare.",
                        'navigation_buttons' => [
                            [
                                'text' => 'Đặt lịch khám',
                                'route' => '/client/dat-lich',
                                'icon' => '📅'
                            ]
                        ]
                    ];
                }
                
                $info = "Dựa vào mô tả của bạn, chúng tôi chưa thể xác định chính xác vấn đề sức khỏe mà thú cưng của bạn đang gặp phải. Các triệu chứng có thể do nhiều nguyên nhân khác nhau và cần được bác sĩ thú y đánh giá trực tiếp.\n\n";
                $info .= "Một số lời khuyên chung:\n";
                $info .= "• Theo dõi sát các triệu chứng, ghi lại thời gian xuất hiện và diễn biến\n";
                $info .= "• Kiểm tra nhiệt độ, nhịp thở và nhịp tim nếu có thể\n";
                $info .= "• Đảm bảo thú cưng được nghỉ ngơi trong môi trường yên tĩnh\n";
                $info .= "• Cung cấp đủ nước sạch\n";
                $severity = "medium";
                break;
        }
        
        // Thêm lời khuyến cáo dựa trên mức độ nghiêm trọng
        $conclusion = "\n\n";
        
        if ($severity === "high") {
            $conclusion .= "⚠️ CẢNH BÁO: Đây là tình trạng CẤP CỨU cần được xử lý NGAY LẬP TỨC! Vui lòng liên hệ phòng khám PetCare theo số 0123.456.789 hoặc đưa thú cưng đến cơ sở y tế thú y gần nhất càng sớm càng tốt.";
        } else {
            $conclusion .= "⚠️ Lưu ý: Thông tin trên chỉ mang tính chất tham khảo và không thay thế cho việc thăm khám trực tiếp. Để có chẩn đoán chính xác và phương pháp điều trị phù hợp, bạn nên đưa thú cưng đến khám tại phòng khám PetCare trong thời gian sớm nhất.";
        }
        
        // Thêm nguồn tham khảo uy tín
        $sources = "\n\n📚 Nguồn tham khảo:\n";
        $sources .= "• Hiệp hội Bác sĩ Thú y Thế giới (WSAVA): https://wsava.org/guidelines/\n";
        $sources .= "• Tổ chức Sức khỏe Động vật Thế giới (OIE): https://www.woah.org/\n";
        $sources .= "• Viện Nghiên cứu Y khoa Thú y (VetMed): https://www.vin.com/\n";
        $sources .= "• Trung tâm Kiểm soát và Phòng ngừa Dịch bệnh (CDC) - Sức khỏe Thú cưng: https://www.cdc.gov/healthypets/\n";
        $sources .= "• Journal of Veterinary Medicine: https://onlinelibrary.wiley.com/journal/14390264";

        // Tạo nút điều hướng phù hợp
        $navigationButtons = [];
        
        if ($severity === "high") {
            // Nút cấp cứu
            $navigationButtons[] = [
                'text' => '🚑 GỌI CẤP CỨU',
                'route' => 'tel:0123456789',
                'icon' => '🚨',
                'description' => 'Liên hệ ngay dịch vụ cấp cứu'
            ];
        } 
        
        $navigationButtons[] = [
            'text' => 'Đặt lịch khám',
            'route' => '/client/dat-lich',
            'icon' => '📅',
            'description' => 'Đặt lịch khám cho thú cưng'
        ];
        
        return [
            'success' => true,
            'message' => $info . $conclusion . $sources,
            'navigation_buttons' => $navigationButtons,
            'severity' => $severity
        ];
    }

    // Thêm phương thức mới để xác định và tạo nút điều hướng
    private function generateNavigationButtons($question)
    {
        $questionLower = mb_strtolower($question, 'UTF-8');
        $buttons = [];
        $directNavigationMatch = false;
        
        // Kiểm tra xem có phải là yêu cầu điều hướng trực tiếp không
        $directNavigationPatterns = [
            '/(vào trang|đi tới|mở|xem|chuyển tới) trang (.*?)( |$)/ui',
            '/(vào trang|đi tới|mở|xem|chuyển tới) (.*?)( |$)/ui'
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
                    // Thay vì chuyển hướng đến trang bảng giá không tồn tại,
                    // chỉ hiển thị thông tin giá trực tiếp trong chat
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
                    'keywords' => ['đăng nhập', 'login', 'sign in', 'signin', 'vào tài khoản', 'vào trang đăng nhập', 'đăng nhập khách hàng', 'đăng nhập vào hệ thống', 'đăng nhập vào website', 'đăng nhập vào trang web', 'đăng nhập vào petcare', 'đăng nhập vào phòng khám'],
                    'text' => 'Đăng nhập',
                    'route' => '/client/dang-nhap-dang-ky',
                    'icon' => '🔑'
                ],
                'đăng ký' => [
                    'keywords' => ['đăng ký', 'tạo tài khoản', 'tạo account', 'sign up', 'signup', 'register', 'chưa có tài khoản', 'đăng ký mới', 'đăng ký thành viên', 'đăng ký khách hàng', 'đăng ký vào hệ thống', 'đăng ký vào website', 'đăng ký vào trang web', 'đăng ký vào petcare', 'đăng ký vào phòng khám'],
                    'text' => 'Đăng ký tài khoản',
                    'route' => '/client/dang-nhap-dang-ky',
                    'icon' => '📝'
                ],
                'bác sĩ' => [
                    'keywords' => ['bác sĩ', 'y tá', 'nhân viên', 'đội ngũ', 'chuyên gia', 'bác sĩ thú y', 'y tá thú y', 'nhân viên thú y', 'đội ngũ bác sĩ', 'đội ngũ y tá', 'đội ngũ nhân viên', 'chuyên gia thú y', 'bác sĩ giỏi', 'bác sĩ có kinh nghiệm', 'bác sĩ chuyên môn cao'],
                    'text' => 'Danh sách bác sĩ',
                    'route' => '/client/xem-bs/0', // ID 0 sẽ hiển thị tất cả bác sĩ
                    'icon' => '👨‍⚕️'
                ],
                'dịch vụ' => [
                    'keywords' => ['dịch vụ', 'chăm sóc', 'cung cấp', 'dịch vụ thú y', 'dịch vụ chăm sóc', 'dịch vụ khám bệnh', 'dịch vụ tiêm phòng', 'dịch vụ spa', 'dịch vụ phẫu thuật', 'dịch vụ cắt tỉa', 'dịch vụ tắm rửa', 'dịch vụ vệ sinh', 'dịch vụ chữa bệnh', 'dịch vụ điều trị'],
                    'text' => 'Xem dịch vụ',
                    'route' => '/client/xem-dich-vu',
                    'icon' => '🐾'
                ],
                'chọn dịch vụ' => [
                    'keywords' => ['chọn dịch vụ', 'đăng ký dịch vụ', 'đặt dịch vụ', 'mua dịch vụ', 'sử dụng dịch vụ', 'dùng dịch vụ', 'thuê dịch vụ', 'đăng ký khám', 'đăng ký tiêm', 'đăng ký spa', 'đăng ký phẫu thuật', 'đăng ký cắt tỉa', 'đăng ký tắm rửa', 'đăng ký vệ sinh'],
                    'text' => 'Chọn dịch vụ',
                    'route' => '/client/chon-dich-vu',
                    'icon' => '✅'
                ],
                'đặt lịch' => [
                    'keywords' => ['đặt lịch', 'hẹn', 'khám', 'lịch hẹn', 'đăng ký khám', 'kiểm tra', 'đặt lịch khám', 'đặt lịch hẹn', 'đặt lịch kiểm tra', 'đặt lịch tư vấn', 'đặt lịch thăm khám', 'đặt lịch điều trị', 'đặt lịch chữa bệnh', 'đặt lịch chăm sóc'],
                    'text' => 'Đặt lịch khám',
                    'route' => '/client/dat-lich',
                    'icon' => '📅'
                ],
                'đặt lịch bác sĩ' => [
                    'keywords' => ['đặt lịch bác sĩ', 'đặt lịch theo bác sĩ', 'chọn bác sĩ khám', 'hẹn bác sĩ', 'khám bác sĩ', 'đặt lịch với bác sĩ', 'đặt lịch khám bác sĩ', 'đặt lịch hẹn bác sĩ', 'đặt lịch kiểm tra bác sĩ', 'đặt lịch tư vấn bác sĩ', 'đặt lịch thăm khám bác sĩ', 'đặt lịch điều trị bác sĩ'],
                    'text' => 'Đặt lịch theo bác sĩ',
                    'route' => '/client/dat-lich-theo-bac-si',
                    'icon' => '👨‍⚕️'
                ],
                'đặt lịch tiêm chủng' => [
                    'keywords' => ['tiêm chủng', 'tiêm phòng', 'vaccine', 'tiêm ngừa', 'đặt lịch tiêm', 'đặt lịch tiêm chủng', 'đặt lịch tiêm phòng', 'đặt lịch vaccine', 'đặt lịch tiêm ngừa', 'hẹn tiêm', 'hẹn tiêm chủng', 'hẹn tiêm phòng', 'hẹn vaccine', 'hẹn tiêm ngừa'],
                    'text' => 'Đặt lịch tiêm chủng',
                    'route' => '/client/dat-lich-tiem-chung',
                    'icon' => '💉'
                ],
                'đặt lịch spa' => [
                    'keywords' => ['spa', 'tắm', 'cắt lông', 'cắt móng', 'chăm sóc', 'đặt lịch spa', 'đặt lịch tắm', 'đặt lịch cắt lông', 'đặt lịch cắt móng', 'đặt lịch chăm sóc', 'hẹn spa', 'hẹn tắm', 'hẹn cắt lông', 'hẹn cắt móng', 'hẹn chăm sóc'],
                    'text' => 'Đặt lịch chăm sóc/Spa',
                    'route' => '/client/dat-lich-cham-soc',
                    'icon' => '✂️'
                ],
                'hồ sơ' => [
                    'keywords' => ['hồ sơ', 'profile', 'thông tin cá nhân', 'thông tin của tôi', 'tài khoản của tôi', 'thông tin khách hàng', 'thông tin thành viên', 'thông tin người dùng', 'thông tin đăng ký', 'thông tin đăng nhập', 'thông tin liên hệ', 'thông tin cá nhân của tôi', 'thông tin tài khoản của tôi'],
                    'text' => 'Thông tin cá nhân',
                    'route' => '/client/thong-tin-ca-nhan',
                    'icon' => '👤'
                ],
                'thú cưng' => [
                    'keywords' => ['thú cưng', 'pet', 'chó', 'mèo', 'thú cưng của tôi', 'thêm thú cưng', 'quản lý thú cưng', 'thông tin thú cưng', 'hồ sơ thú cưng', 'thú cưng đã đăng ký', 'thú cưng đã khám', 'thú cưng đã tiêm', 'thú cưng đã spa'],
                    'text' => 'Quản lý thú cưng',
                    'route' => '/client/pet',
                    'icon' => '🐶'
                ],
                // Thông tin giá được hiển thị trực tiếp trong chat, không điều hướng đến trang riêng
                'giá' => [
                    'keywords' => ['giá', 'chi phí', 'bảng giá', 'phí dịch vụ', 'tiền', 'thanh toán', 'bao nhiêu tiền', 'giá khám', 'giá tiêm', 'giá spa', 'giá phẫu thuật', 'giá cắt tỉa', 'giá tắm rửa', 'giá vệ sinh'],
                    'text' => 'Thông tin giá dịch vụ',
                    'action' => 'getPriceInfo', // Gọi phương thức hiển thị thông tin giá
                    'icon' => '💰'
                ],
                'thanh toán' => [
                    'keywords' => ['thanh toán', 'trả tiền', 'đóng tiền', 'hóa đơn', 'thanh toán dịch vụ', 'thanh toán khám', 'thanh toán tiêm', 'thanh toán spa', 'thanh toán phẫu thuật', 'thanh toán cắt tỉa', 'thanh toán tắm rửa', 'thanh toán vệ sinh', 'thanh toán chữa bệnh', 'thanh toán điều trị'],
                    'text' => 'Thanh toán',
                    'route' => '/client/thanh-toan',
                    'icon' => '💳'
                ],
                'giới thiệu' => [
                    'keywords' => ['giới thiệu', 'về chúng tôi', 'thông tin', 'giới thiệu phòng khám', 'giới thiệu dịch vụ', 'giới thiệu bác sĩ', 'giới thiệu nhân viên', 'giới thiệu cơ sở', 'giới thiệu trang web', 'giới thiệu website', 'giới thiệu petcare', 'giới thiệu thú y', 'giới thiệu chăm sóc', 'giới thiệu điều trị'],
                    'text' => 'Giới thiệu',
                    'route' => '/client/gioi-thieu',
                    'icon' => '📋'
                ],
                'đánh giá' => [
                    'keywords' => ['đánh giá', 'review', 'feedback', 'phản hồi', 'đánh giá dịch vụ', 'đánh giá khám', 'đánh giá tiêm', 'đánh giá spa', 'đánh giá phẫu thuật', 'đánh giá cắt tỉa', 'đánh giá tắm rửa', 'đánh giá vệ sinh', 'đánh giá chữa bệnh', 'đánh giá điều trị'],
                    'text' => 'Đánh giá',
                    'route' => '/client/danh-gia',
                    'icon' => '⭐'
                ],
                'trang chủ' => [
                    'keywords' => ['trang chủ', 'home', 'màn hình chính', 'bắt đầu', 'chính', 'trang đầu', 'trang chính', 'trang mặc định', 'trang mở đầu', 'trang khởi đầu', 'trang bắt đầu', 'trang đầu tiên', 'trang chính thức', 'trang mặc định'],
                    'text' => 'Trang chủ',
                    'route' => '/',
                    'icon' => '🏠'
                ],
                'lịch sử khám' => [
                    'keywords' => ['lịch sử khám', 'lịch sử', 'đã khám', 'kết quả khám', 'hồ sơ khám', 'lịch sử điều trị', 'lịch sử chữa bệnh', 'lịch sử tiêm', 'lịch sử spa', 'lịch sử phẫu thuật', 'lịch sử cắt tỉa', 'lịch sử tắm rửa', 'lịch sử vệ sinh', 'lịch sử chăm sóc'],
                    'text' => 'Lịch sử khám',
                    'route' => '/client/lich-su-kham',
                    'icon' => '📋'
                ],
                'tin tức' => [
                    'keywords' => ['tin tức', 'bài viết', 'blog', 'kiến thức', 'thông tin', 'mẹo', 'tin tức thú y', 'bài viết thú y', 'blog thú y', 'kiến thức thú y', 'thông tin thú y', 'mẹo thú y', 'tin tức chăm sóc', 'bài viết chăm sóc'],
                    'text' => 'Tin tức & Bài viết',
                    'route' => '/client/tin-tuc',
                    'icon' => '📰'
                ],
                'liên hệ' => [
                    'keywords' => ['liên hệ', 'địa chỉ', 'số điện thoại', 'email', 'phòng khám', 'bản đồ', 'vị trí', 'liên hệ phòng khám', 'địa chỉ phòng khám', 'số điện thoại phòng khám', 'email phòng khám', 'phòng khám ở đâu', 'bản đồ phòng khám', 'vị trí phòng khám'],
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

        // Nếu vẫn không tìm thấy nút nào phù hợp, chỉ khi không phải các intent điều hướng phổ biến mới thêm Trang chủ
        if (empty($buttons) && $this->shouldShowHomeButton($questionLower)) {
            $buttons[] = [
                'text' => 'Trang chủ',
                'route' => '/',
                'icon' => '🏠'
            ];
        }

        return $buttons;
    }

    // Thêm hàm kiểm tra có nên show nút Trang chủ không
    private function shouldShowHomeButton($questionLower) {
        $notShowKeywords = [
            'đăng nhập', 'đăng xuất', 'dịch vụ', 'bác sĩ', 'đặt lịch', 'giá', 'liên hệ', 'tin tức', 'hồ sơ', 'thú cưng',
            'đặt', 'chọn', 'hỏi', 'thắc mắc', 'vấn đề', 'triệu chứng', 'bệnh', 'lịch', 'tiêm chủng', 'spa', 'khám'
        ];
        
        foreach ($notShowKeywords as $kw) {
            if (mb_strpos($questionLower, $kw) !== false) return false;
        }
        return true;
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
            
            // Gọi Ollama API với history
            $response = $this->ollamaService->generateResponse($question, $history);
            
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

    /**
     * Store a message in the session history
     *
     * @param string $sessionId
     * @param string $role 'user' or 'assistant'
     * @param string $content
     * @return void
     */
    private function storeMessage($sessionId, $role, $content)
    {
        $history = session("chatbot_session_$sessionId", []);
        $history[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->timestamp
        ];
        
        // Limit history to 10 messages
        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }
        
        session(["chatbot_session_$sessionId" => $history]);
    }
    
    /**
     * Get the chat history for a session
     *
     * @param string $sessionId
     * @return array
     */
    private function getSessionHistory($sessionId)
    {
        return session("chatbot_session_$sessionId", []);
    }

    // API logout cho chatbot
    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'success' => true,
            'message' => 'Bạn đã đăng xuất thành công!'
        ]);
    }

    public function getSuggestedQuestions()
    {
        try {
            $suggestedQuestions = [
                'Phòng khám mở cửa mấy giờ?',
                'Làm sao để đặt lịch khám?',
                'Cần chuẩn bị gì khi đi khám?',
                'Chi phí tiêm phòng cho mèo?',
                'Cần tiêm phòng những bệnh gì?',
                'Dịch vụ spa cho thú cưng gồm những gì?',
                'Triệt sản cho thú cưng có tốt không?',
                'Thú cưng tôi có vấn đề về da',
                'Thú cưng tôi gặp vấn đề tiêu hóa',
                'Tư vấn chăm sóc chó con',
                'Tư vấn chăm sóc mèo con',
                'Chó tôi bị tiêu chảy, tôi nên làm gì?',
                'Mèo tôi không ăn uống, có nguy hiểm không?',
                'Chế độ dinh dưỡng cho chó già',
                'Mèo con cần ăn gì để khỏe mạnh?',
                'Thức ăn tốt cho thú cưng là gì?',
                'Lịch tiêm vaccine cho thú cưng',
                'Có dịch vụ chăm sóc thú cưng tại nhà không?',
                'Thú cưng tôi ho và hắt hơi, có nghiêm trọng không?'
            ];
            
            return response()->json([
                'success' => true,
                'predefined_questions' => $suggestedQuestions
            ]);
            
        } catch (Exception $e) {
            Log::error('Get suggested questions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách câu hỏi gợi ý.'
            ]);
        }
    }

    // Hàm để gọi Ollama service với context
    private function callOllamaWithContext($question, $context)
    {
        // Convert context format from our internal format to what OllamaChatbotService expects
        $history = [];
        foreach ($context as $message) {
            if (isset($message['role']) && isset($message['parts'][0]['text'])) {
                $role = $message['role'] === 'model' ? 'bot' : 'user';
                $history[] = [
                    'role' => $role,
                    'content' => $message['parts'][0]['text']
                ];
            }
        }
        
        return $this->ollamaService->generateResponse($question, $history);
    }
    
    /**
     * Check if Ollama service is running
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOllamaStatus()
    {
        try {
            $status = $this->ollamaService->checkModelInstallation();
            
            return response()->json([
                'success' => $status['status'],
                'message' => $status['message'],
                'model' => env('OLLAMA_MODEL', 'gemma:2b')
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking Ollama status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking Ollama status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze the project structure for context
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeProject()
    {
        try {
            // Static project analysis to help the chatbot understand context
            $projectAnalysis = [
                'success' => true,
                'analysis' => 'PetCare là hệ thống quản lý phòng khám thú cưng với các chức năng đặt lịch, quản lý thú cưng và dịch vụ.',
                'structure' => [
                    'controllers' => ['ChatbotController', 'BookingController', 'ServiceController', 'PetController'],
                    'models' => ['DichVu', 'NhanVien', 'Pet', 'LichHen', 'LichHenPet'],
                    'features' => ['Đặt lịch khám', 'Quản lý thú cưng', 'Tư vấn AI', 'Hồ sơ bệnh án', 'Quản lý dịch vụ']
                ],
                'type' => 'veterinary',
                'domain' => 'pet care',
                'languages' => ['vi']
            ];
            
            return response()->json($projectAnalysis);
        } catch (\Exception $e) {
            Log::error('Error analyzing project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách dịch vụ phòng khám để hiển thị trong chatbot
     * 
     * @return array Thông tin về các dịch vụ
     */
    public function getServicesList()
    {
        try {
            // Lấy danh sách các dịch vụ tiêm chủng
            $tiemChungServices = \App\Models\DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
                ->where('dich_vus.id_loaidv', 1)
                ->where('dich_vus.tinh_trang', 1)
                ->select('dich_vus.id', 'dich_vus.ten_dv', 'dich_vus.mo_ta', 'dich_vus.gia')
                ->get();
                
            // Lấy danh sách các dịch vụ chăm sóc/spa
            $chamSocServices = \App\Models\DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
                ->where('dich_vus.id_loaidv', 2)
                ->where('dich_vus.tinh_trang', 1)
                ->select('dich_vus.id', 'dich_vus.ten_dv', 'dich_vus.mo_ta', 'dich_vus.gia')
                ->get();
                
            // Lấy danh sách các dịch vụ khám bệnh
            $khamBenhServices = \App\Models\DichVu::join('loai_dich_vus', 'loai_dich_vus.id', '=', 'dich_vus.id_loaidv')
                ->where('dich_vus.id_loaidv', 4)
                ->where('dich_vus.tinh_trang', 1)
                ->select('dich_vus.id', 'dich_vus.ten_dv', 'dich_vus.mo_ta', 'dich_vus.gia')
                ->get();
                
            return [
                'success' => true,
                'tiemChung' => $tiemChungServices,
                'chamSoc' => $chamSocServices,
                'khamBenh' => $khamBenhServices,
            ];
                
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách dịch vụ: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách dịch vụ: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * API endpoint để lấy danh sách dịch vụ dạng nút cho chatbot
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicesForChatbot()
    {
        $services = $this->getServicesList();
        
        if (!$services['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách dịch vụ'
            ]);
        }
        
        $serviceButtons = [];
        
        // Tạo nút cho các dịch vụ tiêm chủng
        if (count($services['tiemChung']) > 0) {
            $serviceButtons[] = [
                'text' => '💉 Dịch vụ tiêm chủng',
                'type' => 'service_category',
                'category' => 'tiemChung',
                'icon' => '💉'
            ];
            
            foreach ($services['tiemChung'] as $service) {
                $serviceButtons[] = [
                    'text' => $service['ten_dv'],
                    'type' => 'service',
                    'service_id' => $service['id'],
                    'category' => 'tiemChung',
                    'price' => number_format($service['gia'], 0, ',', '.') . 'đ',
                    'description' => $service['mo_ta'] ?? 'Dịch vụ tiêm chủng',
                    'hidden' => true
                ];
            }
        }
        
        // Tạo nút cho các dịch vụ chăm sóc
        if (count($services['chamSoc']) > 0) {
            $serviceButtons[] = [
                'text' => '✂️ Dịch vụ chăm sóc/Spa',
                'type' => 'service_category',
                'category' => 'chamSoc',
                'icon' => '✂️'
            ];
            
            foreach ($services['chamSoc'] as $service) {
                $serviceButtons[] = [
                    'text' => $service['ten_dv'],
                    'type' => 'service',
                    'service_id' => $service['id'],
                    'category' => 'chamSoc',
                    'price' => number_format($service['gia'], 0, ',', '.') . 'đ',
                    'description' => $service['mo_ta'] ?? 'Dịch vụ chăm sóc',
                    'hidden' => true
                ];
            }
        }
        
        // Tạo nút cho các dịch vụ khám bệnh
        if (count($services['khamBenh']) > 0) {
            $serviceButtons[] = [
                'text' => '🩺 Dịch vụ khám bệnh',
                'type' => 'service_category',
                'category' => 'khamBenh',
                'icon' => '🩺'
            ];
            
            foreach ($services['khamBenh'] as $service) {
                $serviceButtons[] = [
                    'text' => $service['ten_dv'],
                    'type' => 'service',
                    'service_id' => $service['id'],
                    'category' => 'khamBenh',
                    'price' => number_format($service['gia'], 0, ',', '.') . 'đ',
                    'description' => $service['mo_ta'] ?? 'Dịch vụ khám bệnh',
                    'hidden' => true
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'service_buttons' => $serviceButtons
        ]);
    }
    
    /**
     * Lấy các khung giờ trống cho việc đặt lịch
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $serviceId = $request->input('service_id');
            $date = $request->input('date', date('Y-m-d'));
            $doctorId = $request->input('doctor_id');
            
            if (!$serviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn một dịch vụ'
                ]);
            }
            
            // Lấy thông tin dịch vụ
            $service = \App\Models\DichVu::find($serviceId);
            
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dịch vụ'
                ]);
            }
            
            // Lấy tất cả slot mẫu (không theo ngày)
            $slots = \App\Models\LichHen::where('tinh_trang', 1)->get();
            
            // Lấy số lượng đã đặt cho từng slot theo ngày
            $bookedSlots = \DB::table('lich_hen_pets')
                ->select('id_lich', \DB::raw('COUNT(*) as so_luot'))
                ->where('ngay', $date)
                ->groupBy('id_lich')
                ->pluck('so_luot', 'id_lich')
                ->toArray();
            
            $maxBookingsPerSlot = 2;
            $formattedSlots = [];
            foreach ($slots as $slot) {
                $bookedCount = $bookedSlots[$slot->id] ?? 0;
                $availableSpots = max(0, $maxBookingsPerSlot - $bookedCount);
                $isAvailable = $availableSpots > 0;
                if ($isAvailable) {
                    $formattedSlots[] = [
                        'id' => $slot->id,
                        'khung_gio' => $slot->khung_gio,
                        'available' => true,
                        'booked_count' => $bookedCount,
                        'total_slots' => $maxBookingsPerSlot
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'service' => $service,
                'date' => $date,
                'time_slots' => $formattedSlots,
                'alternative_dates' => []
            ]);
        } catch (\Exception $e) {
            \Log::error('Lỗi lấy khung giờ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy khung giờ: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tạo lịch hẹn tự động từ chatbot
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBookingFromChatbot(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'service_id' => 'required|exists:dich_vus,id',
                'time_slot_id' => 'required|exists:lich_hens,id', 
                'user_id' => 'required|exists:khach_hangs,id',
                'pet_id' => 'required|exists:pets,id', // Thú cưng là bắt buộc
                'notes' => 'nullable|string',
                'payment_id' => 'nullable|string', // ID thanh toán từ PayPal
                'payment_method' => 'nullable|string', // Phương thức thanh toán
                'payment_details' => 'nullable|array' // Chi tiết thanh toán
            ]);
            
            // Lấy thông tin khách hàng
            $customer = \App\Models\KhachHang::find($validatedData['user_id']);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin khách hàng'
                ]);
            }
            
            // Lấy thông tin time slot
            $timeSlot = \App\Models\LichHen::find($validatedData['time_slot_id']);
            
            // Lấy thông tin dịch vụ
            $service = \App\Models\DichVu::find($validatedData['service_id']);
            
            if (!$service || !$timeSlot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin dịch vụ hoặc khung giờ'
                ]);
            }
            
            // Kiểm tra thú cưng có thuộc về khách hàng này không
            $pet = \App\Models\Pet::where('id', $validatedData['pet_id'])
                ->where('id_khach_hang', $validatedData['user_id'])
                ->first();
                
            if (!$pet) {
                // Lấy danh sách thú cưng của khách hàng
                $pets = \App\Models\Pet::where('id_khach_hang', $validatedData['user_id'])->get();
                
                if ($pets->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn chưa có thú cưng nào. Vui lòng thêm thú cưng trước khi đặt lịch.',
                        'requires_pet' => true,
                        'redirect_url' => '/client/quan-ly-pet',
                        'data' => $validatedData
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Thú cưng không hợp lệ hoặc không thuộc về bạn. Vui lòng chọn thú cưng khác.',
                    'requires_pet_selection' => true,
                    'pets' => $pets,
                    'data' => $validatedData
                ]);
            }
            
            // Kiểm tra cân nặng của thú cưng có phù hợp với dịch vụ không
            if ($service->can_nang_min !== null && $service->can_nang_max !== null) {
                if ($pet->can_nang < $service->can_nang_min || $pet->can_nang > $service->can_nang_max) {
                    return response()->json([
                        'success' => false,
                        'message' => "Thú cưng này không phù hợp với dịch vụ (cân nặng yêu cầu: {$service->can_nang_min}kg - {$service->can_nang_max}kg).",
                        'requires_pet_selection' => true,
                        'pets' => \App\Models\Pet::where('id_khach_hang', $validatedData['user_id'])->get(),
                        'data' => $validatedData
                    ]);
                }
            }
            
            // Kiểm tra slot đã đầy chưa
            $bookedCount = \App\Models\LichHenPet::where('id_lich', $validatedData['time_slot_id'])
                ->where('ngay', $timeSlot->ngay)
                ->count();
                
            if ($bookedCount >= 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ này đã đầy. Vui lòng chọn khung giờ khác.'
                ]);
            }
            
            // Kiểm tra nếu là ngày hôm nay, không cho phép đặt lịch vào giờ đã qua
            if ($timeSlot->ngay === date('Y-m-d')) {
                $timeString = explode(' - ', $timeSlot->khung_gio)[0];
                $slotTime = strtotime($timeSlot->ngay . ' ' . $timeString);
                
                if ($slotTime < time()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể đặt lịch vào khung giờ đã qua. Vui lòng chọn khung giờ khác.'
                    ]);
                }
            }
            
            // Tính giá dịch vụ dựa trên cân nặng (nếu là dịch vụ spa)
            $gia = $service->gia;
            if ($service->id_loaidv === 2) { // Nếu là dịch vụ spa
                $heSo = 1;
                if ($pet->can_nang > 30) {
                    $heSo = 1.6;
                } else if ($pet->can_nang > 20) {
                    $heSo = 1.4;
                } else if ($pet->can_nang > 10) {
                    $heSo = 1.2;
                }
                $gia = round($service->gia * $heSo);
            }
            
            // Tính tiền cọc (25% giá dịch vụ)
            $tienCoc = round($gia * 0.25);
            
            // Kiểm tra thanh toán
            $paymentStatus = 0; // Chưa thanh toán
            $paymentMethod = $validatedData['payment_method'] ?? 'online';
            $paymentId = $validatedData['payment_id'] ?? null;
            $paymentDetails = $validatedData['payment_details'] ?? null;
            
            // Nếu có thông tin thanh toán PayPal, đánh dấu là đã thanh toán
            if ($paymentId && $paymentMethod === 'paypal') {
                $paymentStatus = 1; // Đã thanh toán
            }
            
            // Tạo lịch hẹn
            $booking = new \App\Models\LichHenPet([
                'id_dich_vu' => $validatedData['service_id'],
                'id_kh' => $validatedData['user_id'],
                'id_nhanvien' => $timeSlot->id_nhanvien,
                'id_pet' => $validatedData['pet_id'],
                'id_lich' => $validatedData['time_slot_id'],
                'ngay' => $timeSlot->ngay,
                'gio_bat_dau' => $timeSlot->gio_bat_dau,
                'gio_ket_thuc' => $timeSlot->gio_ket_thuc,
                'trang_thai' => $paymentStatus ? 2 : 1, // 2: Đã xác nhận (đã thanh toán), 1: Chờ xác nhận
                'ghi_chu' => $validatedData['notes'] ?? '',
                'is_deleted' => 0,
                'gia' => $gia,
                'tien_coc' => $tienCoc,
                'payment_method' => $paymentMethod,
                'payment_id' => $paymentId,
                'payment_details' => $paymentDetails ? json_encode($paymentDetails) : null,
                'payment_status' => $paymentStatus,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $booking->save();
            
            // Lưu thông tin tương tác chatbot
            $interaction = new \App\Models\ChatbotInteraction([
                'user_id' => $validatedData['user_id'],
                'message' => "Đặt lịch dịch vụ {$service->ten_dv} cho thú cưng {$pet->ten_pet}",
                'response' => "Đặt lịch thành công! ID: {$booking->id}",
                'booking_id' => $booking->id,
                'created_at' => now()
            ]);
            $interaction->save();
            
            // Gửi email xác nhận đặt lịch (nếu có)
            try {
                if ($customer->email) {
                    // Gửi email xác nhận đặt lịch
                    \Mail::to($customer->email)->send(new \App\Mail\BookingConfirmation($booking, $customer, $pet, $service, $timeSlot));
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email xác nhận đặt lịch: ' . $e->getMessage());
                // Không trả về lỗi nếu gửi email thất bại, vẫn xem như đặt lịch thành công
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch thành công! Vui lòng kiểm tra email và đến đúng giờ.',
                'booking_id' => $booking->id,
                'redirect_url' => '/client/thong-tin-ca-nhan', // Trang xem lịch đã đặt
                'navigation_buttons' => [
                    [
                        'text' => 'Xem lịch đã đặt',
                        'route' => '/client/thong-tin-ca-nhan',
                        'icon' => '📅'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Lỗi đặt lịch từ chatbot: ' . $e->getMessage());
            
            // Nếu lỗi là do validation
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                // Kiểm tra nếu thiếu pet_id
                if (isset($e->errors()['pet_id'])) {
                    // Lấy user_id từ request
                    $userId = $request->input('user_id');
                    if ($userId) {
                        // Lấy danh sách thú cưng của khách hàng
                        $pets = \App\Models\Pet::where('id_khach_hang', $userId)->get();
                        
                        if ($pets->isEmpty()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Bạn chưa có thú cưng nào. Vui lòng thêm thú cưng trước khi đặt lịch.',
                                'requires_pet' => true,
                                'redirect_url' => '/client/quan-ly-pet',
                                'data' => $request->all()
                            ]);
                        }
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Vui lòng chọn thú cưng cho cuộc hẹn',
                            'requires_pet_selection' => true,
                            'pets' => $pets,
                            'data' => $request->all()
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể đặt lịch: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available time slots for the chatbot
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimeSlots(Request $request)
    {
        try {
            $serviceId = $request->input('service_id');
            $date = $request->input('date', date('Y-m-d'));
            
            if (!$serviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn một dịch vụ'
                ]);
            }
            
            // Lấy thông tin dịch vụ
            $service = \App\Models\DichVu::find($serviceId);
            
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dịch vụ'
                ]);
            }
            
            // Lấy tất cả slot mẫu (không theo ngày)
            $slots = \App\Models\LichHen::where('tinh_trang', 1)->get();
            
            // Lấy số lượng đã đặt cho từng slot theo ngày
            $bookedSlots = \DB::table('lich_hen_pets')
                ->select('id_lich', \DB::raw('COUNT(*) as so_luot'))
                ->where('ngay', $date)
                ->groupBy('id_lich')
                ->pluck('so_luot', 'id_lich')
                ->toArray();
            
            $maxBookingsPerSlot = 2;
            $formattedSlots = [];
            foreach ($slots as $slot) {
                $bookedCount = $bookedSlots[$slot->id] ?? 0;
                $availableSpots = max(0, $maxBookingsPerSlot - $bookedCount);
                
                // Kiểm tra nếu là ngày hôm nay, lọc ra các khung giờ đã qua
                $isAvailable = $availableSpots > 0;
                if ($date === date('Y-m-d')) {
                    $now = new \DateTime();
                    $timeString = explode(' - ', $slot->khung_gio)[0];
                    $slotTime = strtotime($date . ' ' . $timeString);
                    
                    if ($slotTime < time()) {
                        $isAvailable = false;
                    }
                }
                
                if ($isAvailable) {
                    $formattedSlots[] = [
                        'id' => $slot->id,
                        'khung_gio' => $slot->khung_gio,
                        'available' => true,
                        'booked_count' => $bookedCount,
                        'total_slots' => $maxBookingsPerSlot
                    ];
                }
            }
            
            // Tạo danh sách ngày trong khoảng 7 ngày (hôm nay + 6 ngày tiếp theo)
            $today = new \DateTime();
            $nextDates = [];
            
            for ($i = 0; $i < 7; $i++) {
                $nextDate = clone $today;
                $nextDate->modify("+$i days");
                $nextDates[] = $nextDate->format('Y-m-d');
            }
            
            return response()->json([
                'success' => true,
                'service' => $service,
                'date' => $date,
                'time_slots' => $formattedSlots,
                'slotInfo' => $bookedSlots,
                'nextDates' => $nextDates
            ]);
        } catch (\Exception $e) {
            \Log::error('Lỗi lấy khung giờ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy khung giờ: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create booking from chatbot
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'service_id' => 'required|exists:dich_vus,id',
                'time_slot_id' => 'required|exists:lich_hens,id', 
                'user_id' => 'required',
                'pet_id' => 'required|exists:pets,id',
                'notes' => 'nullable|string'
            ]);
            
            // Lấy thông tin khách hàng
            $customer = \App\Models\KhachHang::find($validatedData['user_id']);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin khách hàng'
                ]);
            }
            
            // Lấy thông tin time slot
            $timeSlot = \App\Models\LichHen::find($validatedData['time_slot_id']);
            
            // Lấy thông tin dịch vụ
            $service = \App\Models\DichVu::find($validatedData['service_id']);
            
            if (!$service || !$timeSlot) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin dịch vụ hoặc khung giờ'
                ]);
            }
            
            // Kiểm tra xem slot đã đầy chưa
            $bookedCount = \App\Models\LichHenPet::where('id_lich', $validatedData['time_slot_id'])
                ->where('ngay', date('Y-m-d'))
                ->count();
                
            if ($bookedCount >= 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ này đã đầy. Vui lòng chọn khung giờ khác.'
                ]);
            }
            
            // Tạo lịch hẹn
            $booking = new \App\Models\LichHenPet([
                'id_dv' => $validatedData['service_id'],
                'id_kh' => $validatedData['user_id'],
                'id_pet' => $validatedData['pet_id'],
                'id_lich' => $validatedData['time_slot_id'],
                'ngay' => date('Y-m-d'),
                'gio' => $timeSlot->khung_gio,
                'tinh_trang' => 0, // Chờ xác nhận
                'tien_coc' => $service->gia * 0.25 // 25% giá dịch vụ
            ]);
            
            $booking->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch thành công! Vui lòng kiểm tra email và đến đúng giờ.',
                'booking_id' => $booking->id,
                'redirect_url' => '/client/lich-su-kham'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Lỗi đặt lịch từ chatbot: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể đặt lịch: ' . $e->getMessage()
            ]);
        }
    }
} 
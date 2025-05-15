<?php

namespace App\Services;

use App\Models\ChatbotInteraction;
use App\Models\ChatbotPattern;
use App\Models\ChatbotFeedback;
use Illuminate\Support\Str;

class ChatbotLearningService
{
    private $similarityThreshold = 0.7;

    public function findSimilarInteraction($question)
    {
        $interactions = ChatbotInteraction::where('success_rate', '>=', 0.7)
            ->orderBy('usage_count', 'desc')
            ->get();

        $bestMatch = null;
        $highestSimilarity = 0;

        foreach ($interactions as $interaction) {
            $similarity = $this->calculateSimilarity($question, $interaction->question);
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $interaction;
            }
        }

        if ($highestSimilarity >= $this->similarityThreshold) {
            $bestMatch->increment('usage_count');
            return $bestMatch;
        }

        return null;
    }

    public function storeInteraction($question, $response, $context = null)
    {
        return ChatbotInteraction::create([
            'question' => $question,
            'response' => $response,
            'context' => $context,
            'success_rate' => 1,
            'usage_count' => 1
        ]);
    }

    public function storeFeedback($interactionId, $userId, $isHelpful, $comment = null)
    {
        $feedback = ChatbotFeedback::create([
            'interaction_id' => $interactionId,
            'user_id' => $userId,
            'is_helpful' => $isHelpful,
            'comment' => $comment
        ]);

        $interaction = ChatbotInteraction::find($interactionId);
        if ($interaction) {
            $interaction->updateSuccessRate();
        }

        return $feedback;
    }

    public function learnPattern($question, $intent, $entities = null)
    {
        $pattern = ChatbotPattern::create([
            'pattern' => $question,
            'intent' => $intent,
            'entities' => $entities,
            'confidence' => 1
        ]);

        return $pattern;
    }

    private function calculateSimilarity($str1, $str2)
    {
        // Chuẩn hóa chuỗi
        $str1 = mb_strtolower($str1, 'UTF-8');
        $str2 = mb_strtolower($str2, 'UTF-8');

        // Tách từ
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);

        // Đếm từ chung
        $commonWords = array_intersect($words1, $words2);
        $totalWords = count(array_unique(array_merge($words1, $words2)));

        if ($totalWords === 0) {
            return 0;
        }

        return count($commonWords) / $totalWords;
    }

    public function analyzeQuestion($question)
    {
        // Phân tích cấu trúc câu
        $tokens = explode(' ', mb_strtolower($question, 'UTF-8'));
        
        // Xác định ý định
        $intent = $this->detectIntent($tokens);
        
        // Trích xuất thông tin
        $entities = $this->extractEntities($tokens);
        
        return [
            'intent' => $intent,
            'entities' => $entities
        ];
    }

    private function detectIntent($tokens)
    {
        $intents = [
            'greeting' => [
                'xin chào', 'hello', 'hi', 'chào', 'hey', 'chào bạn', 'chào anh', 'chào chị', 
                'chào em', 'chào cô', 'chào bác', 'chào thầy', 'chào cậu', 'chào mày', 'chào đứa kia',
                'chào buổi sáng', 'chào buổi trưa', 'chào buổi tối', 'chào buổi chiều'
            ],
            'booking' => [
                'đặt lịch', 'hẹn', 'lịch khám', 'đăng ký khám', 'đặt hẹn', 'hẹn khám', 
                'đặt lịch khám', 'đặt lịch hẹn', 'đặt lịch với bác sĩ', 'hẹn bác sĩ',
                'đặt lịch khám bệnh', 'hẹn khám bệnh', 'đặt lịch tư vấn', 'hẹn tư vấn'
            ],
            'price' => [
                'giá', 'chi phí', 'phí', 'bao nhiêu', 'tiền', 'hết bao nhiêu', 
                'giá cả', 'chi phí khám', 'phí khám', 'tiền khám', 'giá khám',
                'bao nhiêu tiền', 'hết mấy tiền', 'tốn bao nhiêu', 'đắt không'
            ],
            'service' => [
                'dịch vụ', 'khám', 'điều trị', 'tiêm', 'phẫu thuật', 'tắm', 'cắt tỉa',
                'khám bệnh', 'chữa bệnh', 'tiêm phòng', 'tiêm vaccine', 'tắm rửa',
                'cắt móng', 'cắt lông', 'tỉa lông', 'spa', 'massage', 'chăm sóc'
            ],
            'doctor' => [
                'bác sĩ', 'bác sỹ', 'bác', 'bs', 'thú y', 'bác sĩ thú y', 
                'bác sỹ thú y', 'bác sĩ khám', 'bác sỹ khám', 'bác sĩ chữa',
                'bác sỹ chữa', 'bác sĩ điều trị', 'bác sỹ điều trị'
            ],
            'emergency' => [
                'cấp cứu', 'khẩn cấp', 'nguy hiểm', 'tai nạn', 'gấp', 'cấp bách',
                'nguy kịch', 'trầm trọng', 'nặng', 'xấu', 'tồi tệ', 'đau nhiều',
                'chảy máu', 'khó thở', 'co giật', 'hôn mê', 'bất tỉnh'
            ]
        ];

        $question = implode(' ', $tokens);
        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($question, $keyword)) {
                    return $intent;
                }
            }
        }

        return 'unknown';
    }

    private function extractEntities($tokens)
    {
        $entities = [];
        
        // Phát hiện loại thú cưng
        $petTypes = [
            'chó', 'mèo', 'cún', 'miu', 'dog', 'cat', 'cún con', 'mèo con',
            'chó con', 'mèo lớn', 'chó lớn', 'cún cưng', 'mèo cưng', 'thú cưng',
            'pet', 'boss', 'hoàng thượng', 'sen', 'cún yêu', 'mèo yêu'
        ];
        foreach ($petTypes as $type) {
            if (in_array($type, $tokens)) {
                $entities['pet_type'] = $type;
                break;
            }
        }
        
        // Phát hiện dịch vụ
        $services = [
            'spa', 'khám', 'tiêm', 'phẫu thuật', 'tắm', 'cắt tỉa',
            'khám bệnh', 'chữa bệnh', 'tiêm phòng', 'tiêm vaccine',
            'tắm rửa', 'cắt móng', 'cắt lông', 'tỉa lông', 'massage',
            'chăm sóc', 'tẩy giun', 'sổ giun', 'khám tổng quát',
            'khám sức khỏe', 'khám định kỳ', 'tư vấn', 'tư vấn sức khỏe'
        ];
        foreach ($services as $service) {
            if (in_array($service, $tokens)) {
                $entities['service'] = $service;
                break;
            }
        }
        
        return $entities;
    }
} 
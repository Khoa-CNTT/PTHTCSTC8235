<?php

namespace App\Services;

use App\Models\ChatbotInteraction;
use App\Models\ChatbotPattern;
use App\Models\ChatbotFeedback;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ChatbotLearningService
{
    private $similarityThreshold = 0.7;
    private const CACHE_TTL = 1440; // 24 hours in minutes

    public function findSimilarInteraction($question)
    {
        // Check cache first
        $cacheKey = 'similar_interaction_' . md5($question);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get successful interactions ordered by usage and success rate
        $interactions = ChatbotInteraction::where('success_rate', '>=', $this->similarityThreshold)
            ->orderByDesc('usage_count')
            ->orderByDesc('success_rate')
            ->limit(100) // Limit for performance
            ->get();

        $bestMatch = null;
        $highestSimilarity = 0;

        foreach ($interactions as $interaction) {
            // Use combined similarity methods for better matching
            $similarity = $this->calculateCombinedSimilarity($question, $interaction->question);
            
            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $interaction;
            }
        }

        if ($highestSimilarity >= $this->similarityThreshold) {
            $bestMatch->increment('usage_count');
            
            // Cache this result
            Cache::put($cacheKey, $bestMatch, self::CACHE_TTL);
            
            return $bestMatch;
        }

        return null;
    }

    public function storeInteraction($question, $response, $context = null)
    {
        // Check for duplicate questions to avoid redundancy
        $existingInteraction = ChatbotInteraction::where('question', $question)->first();
        
        if ($existingInteraction) {
            $existingInteraction->increment('usage_count');
            return $existingInteraction;
        }
        
        return ChatbotInteraction::create([
            'question' => $question,
            'response' => $response,
            'context' => $context,
            'success_rate' => 1,
            'usage_count' => 1,
            'analysis' => json_encode($this->analyzeQuestion($question))
        ]);
    }

    public function storeFeedback($interactionId, $userId, $isHelpful, $comment = null, $sentiment = null)
    {
        $feedback = ChatbotFeedback::create([
            'interaction_id' => $interactionId,
            'user_id' => $userId,
            'is_helpful' => $isHelpful,
            'comment' => $comment,
            'sentiment' => $sentiment
        ]);

        $interaction = ChatbotInteraction::find($interactionId);
        if ($interaction) {
            $interaction->updateSuccessRate();
            
            // If the feedback is negative, decrease the interaction's success rate more
            if (!$isHelpful && $sentiment === 'negative') {
                $interaction->success_rate = max(0, $interaction->success_rate - 0.1);
                $interaction->save();
            }
            
            // Invalidate cache for this interaction's question
            $cacheKey = 'similar_interaction_' . md5($interaction->question);
            Cache::forget($cacheKey);
        }

        return $feedback;
    }

    public function learnPattern($question, $intent, $entities = null)
    {
        // Check for existing pattern to avoid duplicates
        $existingPattern = ChatbotPattern::where('pattern', $question)
                                        ->where('intent', $intent)
                                        ->first();
        
        if ($existingPattern) {
            $existingPattern->confidence = min(1, $existingPattern->confidence + 0.1);
            $existingPattern->save();
            return $existingPattern;
        }
        
        $pattern = ChatbotPattern::create([
            'pattern' => $question,
            'intent' => $intent,
            'entities' => $entities,
            'confidence' => 1
        ]);

        return $pattern;
    }

    // Combined similarity using multiple methods for better matching
    private function calculateCombinedSimilarity($str1, $str2)
    {
        // Normalize strings
        $str1 = mb_strtolower(trim($str1), 'UTF-8');
        $str2 = mb_strtolower(trim($str2), 'UTF-8');
        
        // Calculate different similarity methods
        $jaccardSim = $this->calculateJaccardSimilarity($str1, $str2);
        $levenshteinSim = $this->calculateLevenshteinSimilarity($str1, $str2);
        $cosineSim = $this->calculateCosineSimilarity($str1, $str2);
        
        // Weighted average of multiple similarity metrics for better results
        return ($jaccardSim * 0.4) + ($levenshteinSim * 0.3) + ($cosineSim * 0.3);
    }

    private function calculateJaccardSimilarity($str1, $str2)
    {
        // Split into words
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);

        // Count common words
        $commonWords = array_intersect($words1, $words2);
        $totalWords = count(array_unique(array_merge($words1, $words2)));

        if ($totalWords === 0) {
            return 0;
        }

        return count($commonWords) / $totalWords;
    }
    
    private function calculateLevenshteinSimilarity($str1, $str2)
    {
        $levDist = levenshtein($str1, $str2);
        $maxLength = max(mb_strlen($str1), mb_strlen($str2));
        
        if ($maxLength === 0) {
            return 1; // Both strings are empty, consider them identical
        }
        
        return 1 - ($levDist / $maxLength);
    }
    
    private function calculateCosineSimilarity($str1, $str2)
    {
        // Create term frequency vectors
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);
        
        $vector1 = array_count_values($words1);
        $vector2 = array_count_values($words2);
        
        // Calculate dot product
        $dotProduct = 0;
        foreach ($vector1 as $word => $count) {
            if (isset($vector2[$word])) {
                $dotProduct += $count * $vector2[$word];
            }
        }
        
        // Calculate magnitudes
        $mag1 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector1)));
        $mag2 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector2)));
        
        if ($mag1 === 0 || $mag2 === 0) {
            return 0;
        }
        
        return $dotProduct / ($mag1 * $mag2);
    }

    public function analyzeQuestion($question)
    {
        // Standardize input
        $questionLower = mb_strtolower(trim($question), 'UTF-8');
        $tokens = explode(' ', $questionLower);
        
        // Determine intent
        $intent = $this->detectIntent($tokens, $questionLower);
        
        // Extract entities
        $entities = $this->extractEntities($tokens, $questionLower);
        
        // Additional language analysis
        $sentiment = $this->analyzeSentiment($questionLower);
        
        return [
            'intent' => $intent,
            'entities' => $entities,
            'sentiment' => $sentiment,
            'question_clean' => $questionLower,
            'tokens' => $tokens
        ];
    }

    private function detectIntent($tokens, $questionLower)
    {
        // Check for patterns in database first
        $patterns = ChatbotPattern::where('confidence', '>=', 0.7)
                                  ->orderBy('confidence', 'desc')
                                  ->get();
        
        foreach ($patterns as $pattern) {
            $similarity = $this->calculateCombinedSimilarity($questionLower, $pattern->pattern);
            if ($similarity >= 0.8) {
                return $pattern->intent;
            }
        }
        
        // Core intents with comprehensive keywords
        $intents = [
            'greeting' => [
                'xin chào', 'hello', 'hi', 'chào', 'hey', 'chào bạn', 'chào anh', 'chào chị', 
                'chào em', 'chào cô', 'chào bác', 'chào thầy', 'chào cậu', 'chào mày', 'chào đứa kia',
                'chào buổi sáng', 'chào buổi trưa', 'chào buổi tối', 'chào buổi chiều', 'good morning',
                'good afternoon', 'good evening', 'buổi sáng tốt lành', 'buổi tối vui vẻ'
            ],
            'booking' => [
                'đặt lịch', 'hẹn', 'book', 'đăng ký khám', 'đặt hẹn', 'hẹn khám', 
                'đặt lịch khám', 'đặt lịch hẹn', 'đặt lịch với bác sĩ', 'hẹn bác sĩ',
                'đặt lịch khám bệnh', 'hẹn khám bệnh', 'đặt lịch tư vấn', 'hẹn tư vấn',
                'lịch hẹn', 'cuộc hẹn', 'booking', 'reservation', 'muốn đặt'
            ],
            'price' => [
                'giá', 'chi phí', 'phí', 'bao nhiêu', 'tiền', 'hết bao nhiêu', 
                'giá cả', 'chi phí khám', 'phí khám', 'tiền khám', 'giá khám',
                'bao nhiêu tiền', 'hết mấy tiền', 'tốn bao nhiêu', 'đắt không',
                'pricing', 'bảng giá', 'mắc không', 'rẻ không'
            ],
            'service' => [
                'dịch vụ', 'khám', 'điều trị', 'tiêm', 'phẫu thuật', 'tắm', 'cắt tỉa',
                'khám bệnh', 'chữa bệnh', 'tiêm phòng', 'tiêm vaccine', 'tắm rửa',
                'cắt móng', 'cắt lông', 'tỉa lông', 'spa', 'massage', 'chăm sóc',
                'service', 'các dịch vụ', 'hỗ trợ gì', 'làm gì được', 'chữa được gì'
            ],
            'doctor' => [
                'bác sĩ', 'bác sỹ', 'bác', 'bs', 'thú y', 'bác sĩ thú y', 
                'bác sỹ thú y', 'bác sĩ khám', 'bác sỹ khám', 'bác sĩ chữa',
                'bác sỹ chữa', 'bác sĩ điều trị', 'bác sỹ điều trị', 'thú y sĩ',
                'chuyên gia', 'chuyên viên', 'người khám', 'ai khám', 'người chữa'
            ],
            'emergency' => [
                'cấp cứu', 'khẩn cấp', 'nguy hiểm', 'tai nạn', 'gấp', 'cấp bách',
                'nguy kịch', 'trầm trọng', 'nặng', 'xấu', 'tồi tệ', 'đau nhiều',
                'chảy máu', 'khó thở', 'co giật', 'hôn mê', 'bất tỉnh', 'sốc',
                'emergency', 'urgent', 'need help', 'help', 'sos', 'cứu'
            ],
            'farewell' => [
                'tạm biệt', 'chào tạm biệt', 'bye', 'goodbye', 'bye bye', 'hẹn gặp lại',
                'gặp lại sau', 'tạm biệt nhé', 'chào nhé', 'đi đây', 'kết thúc'
            ],
            'thanks' => [
                'cảm ơn', 'cám ơn', 'thank', 'thanks', 'thank you', 'thank u', 'cảm tạ', 
                'tri ân', 'biết ơn', 'mang ơn', 'xin cảm ơn', 'thanks a lot'
            ],
            'help' => [
                'giúp đỡ', 'giúp', 'hỗ trợ', 'help', 'support', 'assist', 'cần giúp',
                'cần hỗ trợ', 'cần tư vấn', 'tư vấn', 'chỉ dẫn', 'hướng dẫn'
            ]
        ];

        // Check for intent using regex for more flexible matching
        foreach ($intents as $intent => $keywords) {
            $regexPattern = '/\b(' . implode('|', array_map(function($keyword) {
                return preg_quote($keyword, '/');
            }, $keywords)) . ')\b/ui';
            
            if (preg_match($regexPattern, $questionLower)) {
                return $intent;
            }
        }
        
        // Multi-intent detection
        $intentScores = [];
        foreach ($intents as $intent => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($questionLower, $keyword) !== false) {
                    $score += 1;
                }
            }
            if ($score > 0) {
                $intentScores[$intent] = $score / count($tokens); // Normalize by question length
            }
        }
        
        // Return highest scoring intent or 'general' if none found
        if (!empty($intentScores)) {
            arsort($intentScores);
            return key($intentScores);
        }

        return 'general';
    }

    private function extractEntities($tokens, $questionLower)
    {
        $entities = [];
        
        // Pet types with more comprehensive detection
        $petTypes = [
            'chó' => ['/\b(chó|cún|dog|chó\s*cưng|puppy|cậu\s*vàng|chó\s*con|cún\s*con|cún\s*cưng)\b/ui'],
            'mèo' => ['/\b(mèo|cat|kitty|mèo\s*cưng|meow|miu|miu\s*miu|mèo\s*con)\b/ui'],
            'chim' => ['/\b(chim|bird|vẹt|yến|chim\s*cảnh|chim\s*cưng)\b/ui'],
            'cá' => ['/\b(cá|fish|cá\s*cảnh|cá\s*vàng|cá\s*koi)\b/ui'],
            'thỏ' => ['/\b(thỏ|rabbit|bunny|thỏ\s*con|thỏ\s*cưng)\b/ui'],
            'chuột' => ['/\b(chuột|hamster|mouse|chuột\s*hamster|chuột\s*cảnh|chuột\s*bạch)\b/ui']
        ];
        
        // Check for pet types
        foreach ($petTypes as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $questionLower, $matches)) {
                    $entities['pet_type'] = $type;
                    break 2;
                }
            }
        }
        
        // Service types with comprehensive detection
        $services = [
            'spa' => ['/\b(spa|tắm|cắt\s*tỉa|vệ\s*sinh|làm\s*đẹp|cắt\s*móng|cắt\s*lông|tỉa\s*lông)\b/ui'],
            'khám' => ['/\b(khám|kiểm\s*tra|check|soi|tư\s*vấn|kiểm\s*tra\s*sức\s*khỏe|khám\s*bệnh|chẩn\s*đoán)\b/ui'],
            'tiêm' => ['/\b(tiêm\s*phòng|tiêm\s*chủng|vaccine|vaccin|vắc\s*xin|phòng\s*bệnh|mũi\s*tiêm|tiêm\s*ngừa)\b/ui'],
            'phẫu thuật' => ['/\b(phẫu\s*thuật|mổ|triệt\s*sản|thiến|cắt\s*bỏ|giải\s*phẫu|thủ\s*thuật)\b/ui'],
            'dinh dưỡng' => ['/\b(dinh\s*dưỡng|thức\s*ăn|đồ\s*ăn|ăn\s*uống|dinh\s*dướng|thực\s*phẩm|thức\s*ăn|pet\s*food)\b/ui'],
            'tư vấn' => ['/\b(tư\s*vấn|hỏi\s*đáp|tham\s*vấn|trao\s*đổi|hỏi\s*ý\s*kiến|consult|consultation)\b/ui'],
        ];
        
        // Check for service types
        foreach ($services as $service => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $questionLower, $matches)) {
                    $entities['service'] = $service;
                    break 2;
                }
            }
        }
        
        // Advanced time and date extraction
        if (preg_match_all('/\b(\d{1,2}[\s:]\d{1,2}|\d{1,2}\s*(giờ|h|tiếng|am|pm))/ui', $questionLower, $matches)) {
            $entities['time'] = $matches[0];
        }
        
        if (preg_match_all('/\b(\d{1,2}\/\d{1,2}\/\d{2,4}|\d{1,2}-\d{1,2}-\d{2,4}|\d{1,2}\s*tháng\s*\d{1,2}|\d{1,2}\s*\-\s*\d{1,2})/ui', $questionLower, $matches)) {
            $entities['date'] = $matches[0];
        }
        
        // Extract locations/directions
        $locationPatterns = [
            '/\b(ở\s*đâu|chỗ\s*nào|địa\s*chỉ|đường|quận|thành\s*phố|tp|tỉnh|hướng\s*dẫn\s*đường|bản\s*đồ|map|location)\b/ui'
        ];
        
        foreach ($locationPatterns as $pattern) {
            if (preg_match($pattern, $questionLower)) {
                $entities['needs_location'] = true;
                break;
            }
        }
        
        return $entities;
    }
    
    private function analyzeSentiment($text)
    {
        // Basic sentiment analysis
        $positive = [
            'tốt', 'hay', 'tuyệt', 'xuất sắc', 'thích', 'yêu', 'thú vị', 'tuyệt vời', 
            'hài lòng', 'vui', 'happy', 'tuyệt vời', 'great', 'good', 'excellent'
        ];
        
        $negative = [
            'tệ', 'kém', 'không tốt', 'chán', 'buồn', 'thất vọng', 'khó chịu', 
            'không hài lòng', 'không thích', 'ghét', 'tồi tệ', 'bad', 'terrible', 'awful'
        ];
        
        $positiveScore = 0;
        $negativeScore = 0;
        
        foreach ($positive as $word) {
            if (strpos($text, $word) !== false) {
                $positiveScore++;
            }
        }
        
        foreach ($negative as $word) {
            if (strpos($text, $word) !== false) {
                $negativeScore++;
            }
        }
        
        if ($positiveScore > $negativeScore) {
            return 'positive';
        } elseif ($negativeScore > $positiveScore) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }
} 
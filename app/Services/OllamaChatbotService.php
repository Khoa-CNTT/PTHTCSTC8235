<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaChatbotService
{
    protected $baseUrl;
    protected $model;
    protected $systemPrompt = "Bạn là trợ lý ảo phòng khám thú y PetCare. Hãy trả lời ngắn gọn, thân thiện, dễ hiểu, có thể dùng emoji nếu phù hợp. Nếu không chắc chắn, hãy khuyên khách liên hệ bác sĩ. Chỉ trả lời bằng tiếng Việt. Không được thêm hashtag (như #PetCare, #BacSiThuY) vào cuối câu trả lời. Đối với các câu hỏi không liên quan đến thú cưng hoặc dịch vụ phòng khám, hãy trả lời chi tiết và đầy đủ hơn để thể hiện kiến thức rộng, nhưng vẫn giữ giọng điệu thân thiện và chuyên nghiệp.";
    
    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_API_URL', 'http://localhost:11434/api');
        $this->model = env('OLLAMA_MODEL', 'gemma:2b');
        
        // Allow overriding system prompt from env
        $envPrompt = env('OLLAMA_SYSTEM_PROMPT');
        if ($envPrompt) {
            $this->systemPrompt = $envPrompt;
        }
        
        Log::info('OllamaChatbotService initialized', [
            'baseUrl' => $this->baseUrl,
            'model' => $this->model
        ]);
    }
    
    /**
     * Set a different model
     * 
     * @param string $model
     * @return $this
     */
    public function setModel(string $model)
    {
        $this->model = $model;
        return $this;
    }
    
    /**
     * Set a custom system prompt
     * 
     * @param string $prompt
     * @return $this
     */
    public function setSystemPrompt(string $prompt)
    {
        $this->systemPrompt = $prompt;
        return $this;
    }
    
    /**
     * Generate a response for the user's question
     * 
     * @param string $question The user's question
     * @param array $history Previous conversation history
     * @return string
     */
    public function generateResponse(string $question, array $history = [])
    {
        try {
            // Prepare the prompt with history
            $messages = $this->prepareMessages($question, $history);
            
            $payload = [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => false,
                'temperature' => 0.7,
                'top_p' => 0.9,
                'max_tokens' => 1500
            ];
            
            Log::info('Calling Ollama API', [
                'model' => $this->model, 
                'question' => $question
            ]);
            
            // Add timeout and retry logic for better reliability
            $maxRetries = 1;
            $retry = 0;
            $response = null;
            
            while ($retry <= $maxRetries) {
                try {
                    $response = Http::timeout(60)->post("{$this->baseUrl}/chat", $payload);
                    if ($response->successful()) {
                        break;
                    }
                    $retry++;
                    if ($retry <= $maxRetries) {
                        Log::warning("Retrying Ollama API call (attempt {$retry}/{$maxRetries})", [
                            'status' => $response ? $response->status() : 'unknown'
                        ]);
                        sleep(1); // Wait 1 second before retrying
                    }
                } catch (\Exception $e) {
                    Log::error("Error in Ollama API call attempt {$retry}", [
                        'error' => $e->getMessage()
                    ]);
                    $retry++;
                    if ($retry <= $maxRetries) {
                        sleep(1);
                    } else {
                        throw $e;
                    }
                }
            }
            
            if ($response && $response->successful()) {
                $result = $response->json('message.content', null);
                
                if ($result) {
                    Log::info('Successful Ollama response', [
                        'response_length' => strlen($result),
                        'response_preview' => substr($result, 0, 100) . '...'
                    ]);
                    
                    // Post-process the response to remove hashtags
                    $result = $this->removeHashtags($result);
                    
                    return $result;
                }
                
                Log::error('Empty response from Ollama', [
                    'response' => $response->json()
                ]);
            } else {
                Log::error('Failed to generate response from Ollama', [
                    'status' => $response ? $response->status() : 'unknown',
                    'body' => $response ? $response->body() : 'no response'
                ]);
            }
            
            return 'Xin lỗi, tôi đang gặp vấn đề khi xử lý câu hỏi của bạn. Vui lòng thử lại sau.';
        } catch (\Exception $e) {
            Log::error('Error calling Ollama API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 'Xin lỗi, tôi đang gặp vấn đề kết nối. Vui lòng thử lại sau.';
        }
    }
    
    /**
     * Remove hashtags from the response
     *
     * @param string $text
     * @return string
     */
    private function removeHashtags(string $text)
    {
        // Remove hashtags like #PetCare, #ThúYPet, etc. at the end of the message
        $text = preg_replace('/\s*(\*\*)?#[A-Za-z0-9_àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđĐ]+(\*\*)?/u', '', $text);
        
        // Remove lines that only contain hashtags
        $text = preg_replace('/^(\*\*)?#[A-Za-z0-9_àáảãạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệìíỉĩịòóỏõọôồốổỗộơờớởỡợùúủũụưừứửữựỳýỷỹỵđĐ\s]+(\*\*)?$/mu', '', $text);
        
        // Remove asterisks around hashtags
        $text = str_replace('**#', '', $text);
        $text = str_replace('#**', '', $text);
        
        // Clean up multiple newlines that might be left
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        return trim($text);
    }
    
    /**
     * Check for inappropriate content
     * 
     * @param string $text
     * @return bool
     */
    public function isInappropriateContent(string $text) 
    {
        // Danh sách từ khóa cấm
        $badWords = [
            'sex', 'khiêu dâm', 'bạo lực', 'chửi', 'tục', 'phản động', 'chính trị', 
            'đánh nhau', 'ma túy', 'cờ bạc', 'lừa đảo', 'hack', 'crack', 'xxx', '18+', 
            // Removed these keywords as they are not actually inappropriate and might
            // be blocking legitimate responses:
            // 'bán thuốc', 'bán hàng', 'liên hệ', 'số điện thoại', 'facebook', 'zalo', 
            // 'telegram', 'viber', 'email', 'địa chỉ', 'website', 'link', 'http', 'https', 'www.'
        ];
        
        $textLower = mb_strtolower($text, 'UTF-8');
        foreach ($badWords as $word) {
            if (mb_strpos($textLower, $word) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Prepare conversation messages in the format Ollama expects
     * 
     * @param string $question
     * @param array $history
     * @return array
     */
    private function prepareMessages(string $question, array $history)
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt]
        ];
        
        // Add history in the correct format
        foreach ($history as $message) {
            if (isset($message['role']) && isset($message['content'])) {
                // Convert history format from 'bot' to 'assistant'
                $role = $message['role'] === 'bot' ? 'assistant' : $message['role'];
                
                // Only use roles that Ollama expects
                if (in_array($role, ['user', 'assistant'])) {
                    $messages[] = [
                        'role' => $role,
                        'content' => $message['content']
                    ];
                }
            }
        }
        
        // Add the current question
        $messages[] = ['role' => 'user', 'content' => $question];
        
        return $messages;
    }
    
    /**
     * Check if Ollama service is running
     * 
     * @return bool
     */
    public function isOllamaRunning()
    {
        try {
            $response = Http::get("{$this->baseUrl}/version");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if the specified model is installed on Ollama
     * 
     * @return array With status and message
     */
    public function checkModelInstallation()
    {
        try {
            if (!$this->isOllamaRunning()) {
                return [
                    'status' => false,
                    'message' => "Ollama service is not running. Please make sure Ollama is installed and running. Visit https://ollama.ai/ for installation instructions."
                ];
            }
            
            // Check if the model is in the list of available models
            $response = Http::get("{$this->baseUrl}/tags");
            
            if (!$response->successful()) {
                return [
                    'status' => false,
                    'message' => "Failed to get list of models from Ollama."
                ];
            }
            
            $models = $response->json('models', []);
            $modelName = $this->model;
            
            $modelExists = false;
            foreach ($models as $model) {
                if (isset($model['name']) && $model['name'] === $modelName) {
                    $modelExists = true;
                    break;
                }
            }
            
            if (!$modelExists) {
                return [
                    'status' => false,
                    'message' => "Model '{$modelName}' is not installed. Please run 'ollama pull {$modelName}' to install it."
                ];
            }
            
            return [
                'status' => true,
                'message' => "Model '{$modelName}' is installed and ready to use."
            ];
        } catch (\Exception $e) {
            Log::error('Error checking model installation', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => false,
                'message' => "Error checking model installation: {$e->getMessage()}"
            ];
        }
    }
} 
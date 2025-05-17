<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $baseUrl = 'http://localhost:11434/api';
    protected $model = 'deepseek-r1:7b'; // Default model
    
    /**
     * Set the model to use
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
     * Get available models
     * 
     * @return array
     */
    public function getAvailableModels()
    {
        try {
            $response = Http::get("{$this->baseUrl}/tags");
            
            if ($response->successful()) {
                return $response->json('models', []);
            }
            
            Log::error('Failed to fetch Ollama models', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('Error connecting to Ollama API', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
    
    /**
     * Generate completion using the Ollama API
     * 
     * @param string $prompt
     * @param array $options
     * @return string|null
     */
    public function generateCompletion(string $prompt, array $options = [])
    {
        try {
            $payload = array_merge([
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false
            ], $options);
            
            $response = Http::post("{$this->baseUrl}/generate", $payload);
            
            if ($response->successful()) {
                return $response->json('response', null);
            }
            
            Log::error('Failed to generate completion', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error generating completion with Ollama', [
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Analyze project code using AI
     * 
     * @param string $codeContent
     * @return array|null
     */
    public function analyzeCode(string $codeContent)
    {
        $prompt = "Analyze this code and provide insights:\n\n" . $codeContent;
        $response = $this->generateCompletion($prompt);
        
        return [
            'analysis' => $response,
            'model_used' => $this->model
        ];
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
} 
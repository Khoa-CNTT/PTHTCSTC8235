<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotInteraction extends Model
{
    protected $fillable = [
        'question',
        'response',
        'context',
        'success_rate',
        'usage_count',
        'status'
    ];

    protected $casts = [
        'context' => 'array',
        'success_rate' => 'float'
    ];

    public function feedbacks()
    {
        return $this->hasMany(ChatbotFeedback::class, 'interaction_id');
    }

    public function updateSuccessRate()
    {
        $feedbacks = $this->feedbacks;
        if ($feedbacks->count() > 0) {
            $helpfulCount = $feedbacks->where('is_helpful', true)->count();
            $this->success_rate = $helpfulCount / $feedbacks->count();
            $this->save();
        }
    }
} 
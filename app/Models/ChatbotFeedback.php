<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFeedback extends Model
{
    protected $fillable = [
        'interaction_id',
        'user_id',
        'is_helpful',
        'comment'
    ];

    protected $casts = [
        'is_helpful' => 'boolean'
    ];

    public function interaction()
    {
        return $this->belongsTo(ChatbotInteraction::class, 'interaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 
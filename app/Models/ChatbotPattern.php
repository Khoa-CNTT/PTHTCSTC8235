<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotPattern extends Model
{
    protected $fillable = [
        'pattern',
        'intent',
        'entities',
        'confidence'
    ];

    protected $casts = [
        'entities' => 'array',
        'confidence' => 'float'
    ];

    public function updateConfidence($isSuccessful)
    {
        $this->confidence = ($this->confidence * 0.9) + ($isSuccessful ? 0.1 : 0);
        $this->save();
    }
} 
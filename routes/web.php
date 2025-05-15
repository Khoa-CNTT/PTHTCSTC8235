<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route cho chatbot logout
Route::post('/chatbot/logout', [\App\Http\Controllers\ChatbotController::class, 'logout']);

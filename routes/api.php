<?php

use App\Http\Controllers\PetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/them-pet', [PetController::class, 'Them']);
Route::post('/xoa-pet', [PetController::class, 'Xoa']);
Route::post('/sua-pet', [PetController::class, 'Sua']);
Route::post('/thay-doi-tt-pet', [PetController::class, 'Doitt']);
Route::get('/load-pet', [PetController::class, 'Load']);
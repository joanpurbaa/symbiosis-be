<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'cors'])->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/user', [AuthController::class, 'user']);

  // Document routes
  Route::get('/documents', [DocumentController::class, 'index']);
  Route::post('/documents', [DocumentController::class, 'store']);
  Route::get('/documents/{id}/download', [DocumentController::class, 'download']);

  // Admin only routes
  Route::middleware('admin')->group(function () {
    Route::put('/documents/{id}', [DocumentController::class, 'update']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
  });
});

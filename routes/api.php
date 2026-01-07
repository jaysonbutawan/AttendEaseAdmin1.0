<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassSessionController;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()
    ]);
});


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::post('/google-auth', [AuthController::class, 'googleAuth']);
    Route::get('/firebase-register', function () {
        return response()->json(['debug' => 'GET route works']);
    });
//student routes
    Route::post('/firebase-register', [AuthController::class, 'firebaseRegister']);
    Route::post('/update-profile', [StudentController::class, 'updateProfile']);
    Route::post('/get-profile', [StudentController::class, 'getProfile']);
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

//teacher routes
    Route::post('/teacher/update-profile', [TeacherController::class, 'updateProfile']);
    Route::post('/teacher/get-profile', [TeacherController::class, 'getProfile']);
});

// Class Sessions routes
Route::get('/class-sessions', [ClassSessionController::class, 'index']);
Route::post('/class-sessions', [ClassSessionController::class, 'store']);
Route::get('/class-sessions/{id}', [ClassSessionController::class, 'show']);
Route::put('/class-sessions/{id}', [ClassSessionController::class, 'update']);
Route::delete('/class-sessions/{id}', [ClassSessionController::class, 'destroy']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::patch('/profile', [AuthController::class, 'updateProfile']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()
            ]
        ]);
    });
});

// Management routes for Students and Teachers (approve/reject/delete)
Route::prefix('students')->group(function () {
    Route::delete('/{id}', [StudentController::class, 'deleteById']);
    Route::post('/{id}/approve', [StudentController::class, 'approve']);
    Route::post('/{id}/reject', [StudentController::class, 'reject']);
});

Route::prefix('teachers')->group(function () {
    Route::delete('/{id}', [TeacherController::class, 'deleteById']);
    Route::post('/{id}/approve', [TeacherController::class, 'approve']);
    Route::post('/{id}/reject', [TeacherController::class, 'reject']);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});
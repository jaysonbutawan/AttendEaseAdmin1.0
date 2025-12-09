<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check route
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()
    ]);
});

/*
|--------------------------------------------------------------------------
| Public Authentication Routes (No Token Required)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // Email/Password Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Google OAuth Authentication
    Route::post('/google-auth', [AuthController::class, 'googleAuth']);
    Route::get('/firebase-register', function () {
        return response()->json(['debug' => 'GET route works']);
    });

    Route::post('/firebase-register', [AuthController::class, 'firebaseRegister']);
});


/*
|--------------------------------------------------------------------------
| Protected Routes (Token Required)
|--------------------------------------------------------------------------
| These routes require the user to be authenticated with Sanctum token
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile Routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::patch('/profile', [AuthController::class, 'updateProfile']);
    
    // Logout Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    
    // Account Management
    Route::delete('/account', [AuthController::class, 'deleteAccount']);
    
    // Get authenticated user (alternative to /profile)
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()
            ]
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| Additional Protected Routes
|--------------------------------------------------------------------------
| Add your other protected API routes here
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Example: Student-only routes
    Route::middleware('role:student')->prefix('student')->group(function () {
        // Route::get('/dashboard', [StudentController::class, 'dashboard']);
        // Route::get('/courses', [StudentController::class, 'courses']);
    });
    
    // Example: Teacher-only routes
    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        // Route::get('/dashboard', [TeacherController::class, 'dashboard']);
        // Route::get('/students', [TeacherController::class, 'students']);
    });
    
    // Example: Admin-only routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Route::get('/dashboard', [AdminController::class, 'dashboard']);
        // Route::get('/users', [AdminController::class, 'users']);
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route (404 Handler)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found'
    ], 404);
});
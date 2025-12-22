<?php

use App\Http\Controllers\ClassSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\RoomController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/about', function () {
    return Inertia::render('about/About');
})->name('about');

Route::get('/students', function () {
    return Inertia::render('student/Student');
})->name('students');

Route::get('/teachers', function () {
    return Inertia::render('teacher/Teacher');
})->name('teachers');

Route::get('/rooms', function () {
    return Inertia::render('rooms/Room');
})->name('rooms');

Route::get('/courses', function () {
    return Inertia::render('course/Course');
})->name('courses');

Route::get('/subjects', function () {
    return Inertia::render('subject/Subject');
})->name('subjects');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
// Move subject JSON endpoints under /api to avoid conflicts with Inertia page
Route::get('/teachers_controller', [TeacherController::class, 'index']);
Route::get('/teachers_controller', [TeacherController::class, 'index'])->name('teachers.index');


Route::get('/students_controller', [StudentController::class, 'index']);

// subject API routes (avoid conflict with Inertia page)
Route::prefix('api')->group(function () {
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
});

//course routes
Route::post('/api/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/api/courses', [CourseController::class, 'index'])->name('courses.index');
Route::delete('/api/courses/{id}', [CourseController::class, 'destroy']);

//room routes
Route::post('/room_polygon', [RoomController::class, 'store']);
Route::get('/room_polygon', [RoomController::class, 'index']);
Route::delete('/room_polygon/{id}', [RoomController::class, 'destroy']);

//class session routes
Route::post('/class_sessions', [ClassSessionController::class, 'store'])->name('class_sessions.store');






require __DIR__.'/settings.php';

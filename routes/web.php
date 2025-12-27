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
use App\Models\User;


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

Route::get('/department', function () {
    return Inertia::render('department/Department');
})->name('department');

Route::get('/subjects', function () {
    return Inertia::render('subject/Subject');
})->name('subjects');

Route::get('/sessions', function () {
    return Inertia::render('session/Session');
})->name('sessions');

Route::get('/usermanagement', function () {
    $users = User::query()
        ->select('id', 'name', 'email', 'created_at', 'updated_at')
        ->paginate(9)
        ->through(function ($user) {
            $initials = collect(explode(' ', $user->name))
                ->filter()
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('');

            $palette = ['#6366f1', '#2563eb', '#059669', '#f59e0b', '#10b981', '#ef4444'];
            $color = $palette[$user->id % count($palette)];

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'admin',
                'assigned_to' => $user->assigned_to ?? null,
                'last_activity' => optional($user->updated_at)->toIso8601String(),
                'initials' => $initials ?: 'U',
                'avatar_color' => $color,
            ];
        });

    return Inertia::render('management/UserManagement', [
        'users' => $users,
        'totalUsers' => User::count(),
        'totalTeachers' => 0,
        'totalStudents' => 0,
    ]);
})->middleware(['auth', 'verified'])->name('user.management');

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
    Route::get('/subjects/total', [SubjectController::class, 'totalCount'])->name('subjects.total');
    Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
});


//course routes
Route::post('/api/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/api/courses', [CourseController::class, 'index'])->name('courses.index');
Route::put('/api/courses/{id}', [CourseController::class, 'update'])->name('courses.update');
Route::delete('/api/courses/{id}', [CourseController::class, 'destroy']);

// enrollment metrics routes
Route::get('/api/enrollments/total', [StudentController::class, 'totalEnrollments'])->name('enrollments.total');

//room routes
Route::post('/room_polygon', [RoomController::class, 'store']);
Route::get('/room_polygon', [RoomController::class, 'index']);
Route::delete('/room_polygon/{id}', [RoomController::class, 'destroy']);

//class session routes
Route::post('/class_sessions', [ClassSessionController::class, 'store'])->name('class_sessions.store');

// teachers metrics routes
Route::get('/api/teachers/assigned-count', [TeacherController::class, 'assignedCount'])->name('teachers.assigned_count');
Route::get('/api/teachers/unassigned-count', [TeacherController::class, 'unassignedCount'])->name('teachers.unassigned_count');






require __DIR__.'/settings.php';

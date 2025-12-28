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
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagementUserController;
use App\Http\Controllers\StudentSubjectAssignmentController;


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

// use App\Http\Controllers\ClassSessionController

// Route::get('/sessions', [ClassSessionController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('sessions');

Route::get('/usermanagement', function () {
    $palette = ['#6366f1', '#2563eb', '#059669', '#f59e0b', '#10b981', '#ef4444'];

    $teachers = Teacher::query()->get()->map(function ($t) use ($palette) {
        $name = trim(($t->firstname ?? '') . ' ' . ($t->lastname ?? '')) ?: ($t->email ?? 'Teacher');
        $initials = collect(explode(' ', $name))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('') ?: 'T';
        $color = $palette[crc32($t->teacher_id) % count($palette)];
        return [
            'id' => $t->teacher_id,
            'name' => $name,
            'email' => $t->email,
            'role' => 'teacher',
            'assigned_to' => null,
            'last_activity' => optional($t->created_at)->toIso8601String(),
            'initials' => $initials,
            'avatar_color' => $color,
        ];
    });

    $students = Student::query()->get()->map(function ($s) use ($palette) {
        $name = trim(($s->firstname ?? '') . ' ' . ($s->lastname ?? '')) ?: ($s->email ?? 'Student');
        $initials = collect(explode(' ', $name))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('') ?: 'S';
        $color = $palette[crc32($s->student_id) % count($palette)];
        return [
            'id' => $s->student_id,
            'name' => $name,
            'email' => $s->email,
            'role' => 'student',
            'assigned_to' => $s->course_id ?? null,
            'last_activity' => optional($s->created_at)->toIso8601String(),
            'initials' => $initials,
            'avatar_color' => $color,
        ];
    });

    $combined = $teachers->merge($students)->values();

    // Simple pagination for combined collection
    $page = (int) request('page', 1);
    $perPage = 9;
    $total = $combined->count();
    $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();
    $paginator = new LengthAwarePaginator($items, $total, $perPage, $page, [
        'path' => request()->url(),
        'query' => request()->query(),
    ]);

    $totalTeachers = Teacher::count();
    $totalStudents = Student::count();
    $adminCount = 1; // single admin

    return Inertia::render('management/UserManagement', [
        'users' => $paginator,
        'totalUsers' => $totalTeachers + $totalStudents + $adminCount,
        'totalTeachers' => $totalTeachers,
        'totalStudents' => $totalStudents,
        'filters' => [
            'search' => request('search'),
            'role' => request('role'),
            'view' => request('view', 'cards'),
        ],
    ]);
})->middleware(['auth', 'verified'])->name('user.management');

// Management edit/view pages
Route::get('/management/users/{role}/{id}/edit', [ManagementUserController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('management.users.edit');
Route::get('/management/users/{role}/{id}', [ManagementUserController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('management.users.show');

// Update endpoints by id
Route::put('/api/teachers/{id}', [TeacherController::class, 'updateById'])->name('teachers.update_by_id');
Route::put('/api/students/{id}', [StudentController::class, 'updateById'])->name('students.update_by_id');
Route::delete('/api/teachers/{id}', [TeacherController::class, 'deleteById'])->name('teachers.delete_by_id');
Route::delete('/api/students/{id}', [StudentController::class, 'deleteById'])->name('students.delete_by_id');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
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

    // Student subject assignment (no persistence): detects conflicts and notifies admin
        Route::post('/student-subjects/assign', [StudentSubjectAssignmentController::class, 'assign'])
            ->middleware(['auth', 'verified'])
            ->name('studentSubjects.assign');
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

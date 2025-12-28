<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ManagementUserController extends Controller
{
    public function show(string $role, string $id)
    {
        if ($role === 'teacher') {
            $user = Teacher::where('teacher_id', $id)->firstOrFail();
            return Inertia::render('management/EditUser', [
                'role' => 'teacher',
                'user' => [
                    'id' => $user->teacher_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                ],
            ]);
        }

        if ($role === 'student') {
            $user = Student::where('student_id', $id)->firstOrFail();
            return Inertia::render('management/EditUser', [
                'role' => 'student',
                'user' => [
                    'id' => $user->student_id,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'course_id' => $user->course_id,
                ],
            ]);
        }

        abort(404);
    }

    public function edit(string $role, string $id)
    {
        return $this->show($role, $id);
    }
}

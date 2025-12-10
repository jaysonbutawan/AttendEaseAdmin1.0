<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '');
        })
            ->get()
            ->map(function ($student) {
                return [
                    'name' => $student->firstname . ' ' . $student->lastname,
                    'daysAgo' => now()->diffInDays($student->created_at),
                ];
            });

        return response()->json($students);
    }
}

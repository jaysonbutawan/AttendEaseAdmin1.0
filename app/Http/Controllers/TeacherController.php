<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', '');
        })
            ->get()
            ->map(function ($teacher) {
                return [
                    'name' => $teacher->firstname . ' ' . $teacher->lastname,
                    'daysAgo' => now()->diffInDays($teacher->created_at),
                ];
            });

        return response()->json($teachers);
    }
}

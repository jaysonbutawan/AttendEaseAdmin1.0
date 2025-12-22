<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
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
                    'teacher_id' => $teacher->teacher_id,
                    'name' => $teacher->firstname . ' ' . $teacher->lastname,
                    'daysAgo' => now()->diffInDays($teacher->created_at),
                ];
            });

        return response()->json($teachers);
    }

    /**
     * Count of assigned teachers (distinct teacher_id appearing in class_sessions).
     */
    public function assignedCount()
    {
        $count = DB::table('class_sessions')->distinct()->count('teacher_id');

        return response()->json([
            'success' => true,
            'assigned_teachers' => $count,
        ]);
    }

    /**
     * Count teachers not assigned to any class session.
     */
    public function unassignedCount()
    {
        $assignedIds = DB::table('class_sessions')->distinct()->pluck('teacher_id');
        $count = Teacher::whereNotIn('teacher_id', $assignedIds)->count();

        return response()->json([
            'success' => true,
            'unassigned_teachers' => $count,
        ]);
    }
}

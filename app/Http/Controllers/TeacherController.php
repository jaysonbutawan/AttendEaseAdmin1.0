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

    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'firebase_uid' => 'required',
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:15',
        ]);

        $teacher = Teacher::where('firebase_uid', $request->firebase_uid)->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->firstname = $request->firstName;
        $teacher->lastname = $request->lastName;
        $teacher->contact_number = $request->contact_number ?: null;
        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }


    public function getProfile(Request $request)
    {
        $request->validate([
            'firebase_uid' => 'required'
        ]);

        $teacher = Teacher::select(
            'teachers.*',
        )
            ->where('teachers.firebase_uid', $request->firebase_uid)
            ->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'teacher_id' => $teacher->teacher_id,
                'email' => $teacher->email,
                'firstname' => $teacher->firstname,
                'lastname' => $teacher->lastname,
                'contact_number' => $teacher->contact_number,
                'status' => $teacher->status,
                'created_at' => $teacher->created_at,
            ]
        ]);
    }

    /**
     * Update a teacher by teacher_id.
     */
    public function updateById(Request $request, string $id)
    {
        $data = $request->validate([
            'firstname' => 'nullable|string|max:100',
            'lastname' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'contact_number' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:50',
        ]);

        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        foreach ($data as $key => $value) {
            $teacher->{$key} = $value;
        }
        $teacher->save();

        return response()->json(['message' => 'Teacher updated', 'teacher_id' => $teacher->teacher_id]);
    }

    /**
     * Delete a teacher by teacher_id.
     */
    public function deleteById(string $id)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        $teacher->delete();
        return response()->json(['message' => 'Teacher deleted', 'teacher_id' => $id]);
    }

    /**
     * Approve a teacher by teacher_id.
     */
    public function approve(string $id)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        $teacher->approval_status = 'approved';
        $teacher->approved_at = now();
        $teacher->save();
        return response()->json(['message' => 'Teacher approved', 'teacher_id' => $id]);
    }

    /**
     * Reject a teacher by teacher_id.
     */
    public function reject(string $id)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        $teacher->approval_status = 'rejected';
        // Do not modify approved_at on reject
        $teacher->save();
        return response()->json(['message' => 'Teacher rejected', 'teacher_id' => $id]);
    }

    /**
     * Get all teachers with their profile data (name, email, department, assigned subjects).
     */
    public function getTeachersWithProfile()
    {
        $teachers = Teacher::with(['classSessions.subject'])
            ->get()
            ->map(function ($teacher) {
                return $teacher->getProfileData();
            });

        return response()->json([
            'success' => true,
            'teachers' => $teachers,
        ]);
    }
}

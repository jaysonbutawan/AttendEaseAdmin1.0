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
            'approval_status' => 'nullable|in:pending,approved,rejected',
        ]);

        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $teacher->{$key} = $value;
            }
        }
        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully',
            'teacher' => [
                'teacher_id' => $teacher->teacher_id,
                'firstname' => $teacher->firstname,
                'lastname' => $teacher->lastname,
                'email' => $teacher->email,
                'contact_number' => $teacher->contact_number,
                'status' => $teacher->status,
                'approval_status' => $teacher->approval_status,
            ]
        ]);
    }

    /**
     * Delete a teacher by teacher_id.
     */
    public function deleteById(string $id, Request $request)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found');
        }
        
        try {
            $teacher->delete();
            return redirect()->back()->with('success', 'Teacher deleted successfully');
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                return redirect()->back()->with('error', 'Cannot delete this teacher because they have assigned class sessions. Please remove them from all sessions first.');
            }
            return redirect()->back()->with('error', 'Error deleting teacher: ' . $e->getMessage());
        }
    }

    /**
     * Approve a teacher by teacher_id.
     */
    public function approve(string $id, Request $request)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found');
        }
        $teacher->approval_status = 'approved';
        $teacher->approved_at = now();
        $teacher->save();
        return redirect()->back()->with('success', 'Teacher approved successfully');
    }

    /**
     * Reject a teacher by teacher_id.
     */
    public function reject(string $id, Request $request)
    {
        $teacher = Teacher::where('teacher_id', $id)->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher not found');
        }
        $teacher->approval_status = 'rejected';
        // Do not modify approved_at on reject
        $teacher->save();
        return redirect()->back()->with('success', 'Teacher rejected successfully');
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

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
                    'teacher_id' => $teacher->teacher_id,
                    'name' => $teacher->firstname . ' ' . $teacher->lastname,
                    'daysAgo' => now()->diffInDays($teacher->created_at),
                ];
            });

        return response()->json($teachers);
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
}

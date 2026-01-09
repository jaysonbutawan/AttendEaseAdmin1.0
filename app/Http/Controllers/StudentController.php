<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
public function index()
{
    $students = Student::select(
        'students.student_id',
        'students.firstname',
        'students.lastname',
        'students.course_id',
        'students.year',
        'students.email',
        'students.contact_number',
        'courses.course_name'
    )
    ->leftJoin('courses', 'students.course_id', '=', 'courses.course_id')
    ->get()
    ->map(function ($student) {
        // Map year number to year level string
        $yearLevel = match($student->year) {
            1 => '1st Year',
            2 => '2nd Year',
            3 => '3rd Year',
            4 => '4th Year',
            default => null,
        };

        return [
            'id' => (string) $student->student_id,
            'name' => trim($student->firstname . ' ' . $student->lastname),
            'department' => $student->course_name ?? 'N/A',
            'year_level' => $yearLevel,
            'email' => $student->email,
            'contact' => $student->contact_number,
            'initials' =>
                strtoupper(substr($student->firstname, 0, 1)) .
                strtoupper(substr($student->lastname, 0, 1)),
            'selected' => false,
        ];
    });

    return response()->json($students);
}


    public function totalEnrollments()
    {
        $total = Student::whereNotNull('course_id')->count();

        return response()->json([
            'success' => true,
            'total_enrollments' => $total,
        ]);
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'firebase_uid' => 'required',
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'course_id' => 'nullable',
        ]);

        $student = Student::where('firebase_uid', $request->firebase_uid)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->firstname = $request->firstName;
        $student->lastname = $request->lastName;
        $student->course_id = $request->course_id;
        $student->save();

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

        $student = Student::select(
            'students.*',
            'courses.course_name'
        )
            ->leftJoin('courses', 'students.course_id', '=', 'courses.course_id')
            ->where('students.firebase_uid', $request->firebase_uid)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student_id' => $student->student_id,
                'email' => $student->email,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'contact_number' => $student->contact_number,

                'course_id' => $student->course_id,
                'course_name' => $student->course_name, 

                'year' => $student->year,
                'status' => $student->status,
                'created_at' => $student->created_at,
            ]
        ]);
    }
    
    public function updateById(Request $request, string $id)
    {
        $data = $request->validate([
            'firstname' => 'nullable|string|max:100',
            'lastname' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'contact_number' => 'nullable|string|max:20',
            'course_id' => 'nullable|integer',
            'status' => 'nullable|string|max:50',
        ]);

        $student = Student::where('student_id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        foreach ($data as $key => $value) {
            $student->{$key} = $value;
        }
        $student->save();

        return response()->json(['message' => 'Student updated', 'student_id' => $student->student_id]);
    }

    public function deleteById(string $id)
    {
        $student = Student::where('student_id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->delete();
        return response()->json(['message' => 'Student deleted', 'student_id' => $id]);
    }

    /**
     * Approve a student by student_id.
     */
    public function approve(string $id)
    {
        $student = Student::where('student_id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->approval_status = 'approved';
        $student->approved_at = now();
        $student->save();
        return response()->json(['message' => 'Student approved', 'student_id' => $id]);
    }

    /**
     * Reject a student by student_id.
     */
    public function reject(string $id)
    {
        $student = Student::where('student_id', $id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->approval_status = 'rejected';
        // Leave approved_at as-is or null; we do not modify it on reject
        $student->save();
        return response()->json(['message' => 'Student rejected', 'student_id' => $id]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClassSession;
use App\Models\Student;

class StudentClassSessionController extends Controller
{
    /**
     * Get all students not enrolled in a specific session
     */
    public function getAvailableStudents($sessionId)
    {
        ClassSession::findOrFail($sessionId);

        $enrolledStudentIds = DB::table('student_class_sessions')
            ->where('session_id', $sessionId)
            ->pluck('student_class_sessions.student_id');

        $availableStudents = Student::whereNotIn('students.student_id', $enrolledStudentIds)
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get()
            ->map(function ($student) {
                return [
                    'student_id' => $student->student_id,
                    'firstname' => $student->firstname,
                    'lastname' => $student->lastname,
                    'email' => $student->email,
                    'full_name' => trim($student->firstname . ' ' . $student->lastname),
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $availableStudents,
        ]);
    }

    /**
     * Assign multiple students to multiple class sessions.
     * This creates a many-to-many relationship.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'session_ids' => ['required', 'array', 'min:1'],
            'session_ids.*' => ['integer', 'exists:class_sessions,session_id'],

            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['string', 'exists:students,student_id'],
        ]);

        $enrollmentData = [];
        $newEnrollments = 0;
        $existingEnrollments = 0;

        DB::transaction(function () use ($data, &$enrollmentData, &$newEnrollments, &$existingEnrollments) {
            // Prepare the pivot data for each student
            $pivotData = collect($data['student_ids'])->mapWithKeys(function ($studentId) {
                return [
                    $studentId => [
                        'enrollment_status' => 'enrolled',
                        'enrolled_at' => now(),
                    ]
                ];
            })->toArray();

            // Assign students to each session
            foreach ($data['session_ids'] as $sessionId) {
                $session = ClassSession::with('subject')->find($sessionId);
                
                if (!$session) {
                    continue;
                }

                // Get existing enrollments for this session
                $existingStudentIds = DB::table('student_class_sessions')
                    ->where('session_id', $sessionId)
                    ->pluck('student_class_sessions.student_id')
                    ->toArray();
                
                // Sync without detaching (preserves existing enrollments)
                $session->students()->syncWithoutDetaching($pivotData);
                
                // Count new vs existing
                foreach ($data['student_ids'] as $studentId) {
                    if (in_array($studentId, $existingStudentIds)) {
                        $existingEnrollments++;
                    } else {
                        $newEnrollments++;
                    }
                }

                $enrollmentData[] = [
                    'session_id' => $sessionId,
                    'subject' => $session->subject->subject_name ?? 'Unknown',
                    'students_count' => count($data['student_ids']),
                ];
            }
        });

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Successfully assigned %d students to %d session(s). New enrollments: %d, Already enrolled: %d',
                count($data['student_ids']),
                count($data['session_ids']),
                $newEnrollments,
                $existingEnrollments
            ),
            'data' => [
                'total_sessions' => count($data['session_ids']),
                'total_students' => count($data['student_ids']),
                'new_enrollments' => $newEnrollments,
                'existing_enrollments' => $existingEnrollments,
                'enrollments' => $enrollmentData,
            ]
        ]);
    }

    /**
     * Remove a student from a class session
     */
    public function destroy($sessionId, $studentId)
    {
        $session = ClassSession::findOrFail($sessionId);
        $student = Student::findOrFail($studentId);

        $deleted = DB::table('student_class_sessions')
            ->where('session_id', $sessionId)
            ->where('student_id', $studentId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Student %s removed from session successfully',
                    trim($student->firstname . ' ' . $student->lastname)
                ),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Student was not enrolled in this session',
        ], 404);
    }
}


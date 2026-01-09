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

        // Check for schedule conflicts
        $conflicts = $this->checkScheduleConflicts($data['student_ids'], $data['session_ids']);
        
        if (!empty($conflicts)) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule conflicts detected',
                'conflicts' => $conflicts,
            ], 422);
        }

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
     * Check for schedule conflicts when assigning students to sessions
     */
    private function checkScheduleConflicts(array $studentIds, array $sessionIds)
    {
        $conflicts = [];
        
        // Get the sessions being assigned
        $newSessions = ClassSession::with('subject')
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        foreach ($studentIds as $studentId) {
            // Get student info
            $student = Student::find($studentId);
            if (!$student) continue;

            $studentName = trim($student->firstname . ' ' . $student->lastname);

            // Get all sessions the student is currently enrolled in
            $enrolledSessions = ClassSession::with('subject')
                ->whereHas('students', function ($query) use ($studentId) {
                    $query->where('student_class_sessions.student_id', $studentId);
                })
                ->get();

            // Check each new session against enrolled sessions
            foreach ($newSessions as $newSession) {
                $newDays = is_string($newSession->session_days) 
                    ? json_decode($newSession->session_days, true) 
                    : $newSession->session_days;
                
                if (!is_array($newDays)) $newDays = [];
                
                // Normalize days to lowercase
                $newDays = array_map('strtolower', $newDays);

                foreach ($enrolledSessions as $enrolledSession) {
                    // Skip if it's the same session
                    if ($enrolledSession->session_id === $newSession->session_id) {
                        continue;
                    }

                    $enrolledDays = is_string($enrolledSession->session_days)
                        ? json_decode($enrolledSession->session_days, true)
                        : $enrolledSession->session_days;
                    
                    if (!is_array($enrolledDays)) $enrolledDays = [];
                    
                    // Normalize days to lowercase
                    $enrolledDays = array_map('strtolower', $enrolledDays);

                    // Check if days overlap
                    $overlappingDays = array_intersect($newDays, $enrolledDays);
                    
                    if (!empty($overlappingDays)) {
                        // Check if times overlap
                        if ($this->timesOverlap(
                            $newSession->start_time,
                            $newSession->end_time,
                            $enrolledSession->start_time,
                            $enrolledSession->end_time
                        )) {
                            $conflicts[] = [
                                'student_id' => $studentId,
                                'student_name' => $studentName,
                                'new_subject' => $newSession->subject->subject_name ?? 'Unknown',
                                'new_time' => $newSession->start_time . ' - ' . $newSession->end_time,
                                'new_days' => ucwords(implode(', ', $newDays)),
                                'conflicting_subject' => $enrolledSession->subject->subject_name ?? 'Unknown',
                                'conflicting_time' => $enrolledSession->start_time . ' - ' . $enrolledSession->end_time,
                                'conflicting_days' => ucwords(implode(', ', $enrolledDays)),
                            ];
                        }
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check if two time ranges overlap
     */
    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        $start1 = strtotime($start1);
        $end1 = strtotime($end1);
        $start2 = strtotime($start2);
        $end2 = strtotime($end2);

        // Two ranges overlap if one starts before the other ends
        return ($start1 < $end2) && ($end1 > $start2);
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


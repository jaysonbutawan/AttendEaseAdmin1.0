<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ClassSessionController extends Controller
{
    public function store(Request $request)
    {
        Log::info('STORE REQUEST RECEIVED', [
            'payload' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        $validated = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,subject_id'],
            'teacher_id' => ['required', 'string', 'max:50', 'exists:teachers,teacher_id'],
            'room_id'    => ['required', 'integer', 'exists:rooms,room_id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'session_days' => ['required', 'array', 'min:1'],
            'session_days.*' => [
                'string',
                function ($attr, $value, $fail) {
                    if (!in_array(trim(strtolower($value)), [
                        'monday',
                        'tuesday',
                        'wednesday',
                        'thursday',
                        'friday'
                    ])) {
                        $fail("Invalid day value.");
                    }
                }
            ],
            'session_status' => ['nullable', 'string', Rule::in(['active', 'ended', 'pending'])],
            'qr_code' => ['nullable', 'string', 'max:255'],
            'qr_valid' => ['nullable', 'boolean'],
            'allowance_time' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);

        $session = ClassSession::create([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'room_id'    => $validated['room_id'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'session_days' => $validated['session_days'],
            'session_status' => $validated['session_status'] ?? 'pending',
            'qr_code' => $validated['qr_code'] ?? null,
            'qr_valid' => $validated['qr_valid'] ?? false,
            'allowance_time' => $validated['allowance_time'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class session created successfully.',
            'session' => $session,
        ], 201);
    }

    public function index()
    {
        $sessions = ClassSession::with(['subject', 'teacher', 'room'])->get();

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    public function show($id)
    {
        $session = ClassSession::with(['subject', 'teacher', 'room'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    public function update(Request $request, $id)
    {
        $session = ClassSession::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => ['sometimes', 'integer', 'exists:subjects,subject_id'],
            'teacher_id' => ['sometimes', 'string', 'max:50', 'exists:teachers,teacher_id'],
            'room_id'    => ['sometimes', 'integer', 'exists:rooms,room_id'],
            'start_time' => ['sometimes', 'date_format:H:i:s'],
            'end_time'   => ['sometimes', 'date_format:H:i:s', 'after:start_time'],
            'session_date' => ['sometimes', 'date'],
            'session_status' => ['sometimes', 'string', Rule::in(['active', 'ended', 'pending'])],
            'qr_code' => ['nullable', 'string', 'max:255'],
            'qr_valid' => ['nullable', 'boolean'],
            'allowance_time' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class session updated successfully.',
            'session' => $session,
        ]);
    }

    public function destroy($id)
    {
        $session = ClassSession::findOrFail($id);
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class session deleted successfully.',
        ]);
    }

    public function getReadableSessions()
    {
        $sessions = ClassSession::with(['subject', 'teacher', 'room'])->get();

        $data = $sessions->map(function ($session) {
            return [
                'session_id'   => $session->session_id,

                'subject_id'   => $session->subject_id,
                'subject_name' => $session->subject?->subject_name,

                'teacher_id'   => $session->teacher_id,
                'teacher_name' => $session->teacher
                    ? trim($session->teacher->firstname . ' ' . $session->teacher->lastname)
                    : null,
                    
                'room_id'      => $session->room_id,
                'room_name'    => $session->room?->room_name,

                'session_days' => collect($session->session_days)
                    ->map(fn($d) => ucfirst($d))
                    ->values(),

                'start_time'   => $session->start_time,
                'end_time'     => $session->end_time,

                'session_status' => $session->session_status,
            ];
        });

        return response()->json([
            'success' => true,
            'sessions' => $data,
        ]);
    }

    /**
     * Get all students enrolled in a specific class session
     */
    public function getSessionStudents($sessionId)
    {
        $session = ClassSession::findOrFail($sessionId);

        $students = $session->students()
            ->select(
                'students.student_id',
                'students.firstname',
                'students.lastname',
                'students.email',
                'student_class_sessions.enrollment_status',
                'student_class_sessions.enrolled_at'
            )
            ->get()
            ->map(function ($student) {
                return [
                    'student_id' => $student->student_id,
                    'firstname' => $student->firstname,
                    'lastname' => $student->lastname,
                    'email' => $student->email,
                    'enrollment_status' => $student->enrollment_status ?? 'enrolled',
                    'enrolled_at' => $student->enrolled_at ?? $student->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }
}

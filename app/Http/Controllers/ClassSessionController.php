<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassSessionController extends Controller
{
    public function store(Request $request)
    {
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
            'session_days' => json_encode($validated['session_days']),
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
}

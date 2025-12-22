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

            'session_days' => ['required', 'array', 'min:1', 'max:3'],
            'session_days.*' => ['required', 'string', Rule::in([
                'monday','tuesday','wednesday','thursday','friday','saturday','sunday'
            ])],

            'allowance_time' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);

        $session = ClassSession::create([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'room_id'    => $validated['room_id'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'session_days' => $validated['session_days'], 
            'session_status' => 'pending',
            'qr_valid' => false,
            'allowance_time' => $validated['allowance_time'] ?? null,
        ]);

        return response()->json([
            'message' => 'Schedule saved.',
            'session_id' => $session->session_id,
        ], 201);
    }
}

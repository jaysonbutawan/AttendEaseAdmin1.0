<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassSessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:255'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'room_name'    => ['required', 'string', 'max:255'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
            'session_days' => ['required', 'array', 'min:1', 'max:3'],
            'session_days.*' => [
                'required',
                'string',
                Rule::in(['monday','tuesday','wednesday','thursday','friday','saturday','sunday']),
            ],
            'allowance_time' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);

        // Find IDs by names (adjust column names if your schema differs)
        $subject = DB::table('subjects')->where('subject_name', $validated['subject_name'])->first();
        if (!$subject) {
            return response()->json(['message' => 'Subject not found.'], 422);
        }

        $teacher = DB::table('teachers')->where('name', $validated['teacher_name'])->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found.'], 422);
        }

        $room = DB::table('rooms')->where('room_name', $validated['room_name'])->first();
        if (!$room) {
            return response()->json(['message' => 'Room not found.'], 422);
        }

        // Insert schedule
        $id = DB::table('class_sessions')->insertGetId([
            'subject_id'      => $subject->subject_id,
            'room_id'         => $room->room_id,
            // Your migration uses string('teacher_id', 50) but references teachers.teacher_id.
            // If teachers.teacher_id is a string, use it directly:
            'teacher_id'      => $teacher->teacher_id ?? $teacher->id ?? null,

            'start_time'      => $validated['start_time'],
            'end_time'        => $validated['end_time'],
            'session_days'    => json_encode($validated['session_days']),

            'session_status'  => 'pending',
            'qr_code'         => null,
            'qr_valid'        => 0,
            'allowance_time'  => $validated['allowance_time'] ?? null,

            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json([
            'message' => 'Schedule saved.',
            'session_id' => $id,
        ], 201);
    }
}

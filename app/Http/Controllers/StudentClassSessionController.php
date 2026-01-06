<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClassSession;

class StudentClassSessionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'session_ids' => ['required', 'array', 'min:1'],
            'session_ids.*' => ['integer', 'exists:class_sessions,session_id'],

            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:students,student_id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['session_ids'] as $sessionId) {
                $session = ClassSession::find($sessionId);
                $session->students()->syncWithoutDetaching(
                    collect($data['student_ids'])->mapWithKeys(function ($studentId) {
                        return [
                            $studentId => [
                                'enrollment_status' => 'enrolled',
                                'enrolled_at' => now(),
                            ]
                        ];
                    })->toArray()
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Students assigned to sessions successfully'
        ]);
    }
}

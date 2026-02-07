<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        $sessions = DB::table('class_sessions')->pluck('session_id')->toArray();
        $students = DB::table('students')->pluck('student_id')->toArray();

        if (empty($sessions) || empty($students)) {
            $this->command->warn('⚠ Cannot seed enrollments: missing class sessions or students');
            return;
        }

        $enrollments = [];
        
        // Enroll students across all sessions with varied distribution
        foreach ($sessions as $sessionIndex => $sessionId) {
            // Vary the number of students per session (between 8-15)
            $numStudents = rand(8, min(15, count($students)));
            $enrolledStudents = array_rand(array_flip($students), $numStudents);
            
            foreach ($enrolledStudents as $studentId) {
                $enrollments[] = [
                    'session_id' => $sessionId,
                    'student_id' => $studentId,
                    'enrollment_status' => 'enrolled',
                    'enrolled_at' => now()->subDays(rand(1, 30)),
                ];
            }
        }

        // Remove duplicates based on session_id + student_id
        $unique = [];
        foreach ($enrollments as $enrollment) {
            $key = $enrollment['session_id'] . '-' . $enrollment['student_id'];
            if (!isset($unique[$key])) {
                $unique[$key] = $enrollment;
            }
        }

        $created = 0;
        foreach ($unique as $enrollment) {
            $exists = DB::table('student_class_sessions')
                ->where('session_id', $enrollment['session_id'])
                ->where('student_id', $enrollment['student_id'])
                ->exists();

            if (!$exists) {
                DB::table('student_class_sessions')->insert($enrollment);
                $created++;
            }
        }

        $this->command->info("✓ Created $created student enrollments");
    }
}

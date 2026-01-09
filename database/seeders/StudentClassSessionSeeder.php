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
        
        // Enroll first 5 students in first 3 sessions
        for ($i = 0; $i < min(3, count($sessions)); $i++) {
            for ($j = 0; $j < min(5, count($students)); $j++) {
                $enrollments[] = [
                    'session_id' => $sessions[$i],
                    'student_id' => $students[$j],
                    'enrollment_status' => 'enrolled',
                    'enrolled_at' => now(),
                ];
            }
        }

        // Enroll students 5-8 in session 4 (if exists)
        if (count($sessions) >= 4) {
            for ($j = 5; $j < min(8, count($students)); $j++) {
                $enrollments[] = [
                    'session_id' => $sessions[3],
                    'student_id' => $students[$j],
                    'enrollment_status' => 'enrolled',
                    'enrolled_at' => now(),
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

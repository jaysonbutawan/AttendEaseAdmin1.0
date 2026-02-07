<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing IDs from database
        $subjects = DB::table('subjects')->pluck('subject_id', 'subject_name');
        $teachers = DB::table('teachers')->pluck('teacher_id');
        $rooms = DB::table('rooms')->pluck('room_id');

        if ($subjects->isEmpty() || $teachers->isEmpty() || $rooms->isEmpty()) {
            $this->command->warn('⚠ Cannot seed class sessions: missing subjects, teachers, or rooms');
            return;
        }

        $sessions = [
            [
                'subject_id' => $subjects->get('Advanced Mathematics', $subjects->first()),
                'teacher_id' => $teachers->get(0),
                'room_id' => $rooms->get(0),
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'session_days' => json_encode(['monday', 'wednesday', 'friday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('Computer Programming', $subjects->first()),
                'teacher_id' => $teachers->get(0),
                'room_id' => $rooms->get(3),
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'session_days' => json_encode(['tuesday', 'thursday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('Database Systems', $subjects->first()),
                'teacher_id' => $teachers->get(0),
                'room_id' => $rooms->get(3),
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'session_days' => json_encode(['monday', 'wednesday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('Physics 101', $subjects->first()),
                'teacher_id' => $teachers->get(1),
                'room_id' => $rooms->get(1),
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'session_days' => json_encode(['tuesday', 'thursday']),
                'session_status' => 'pending',
            ],
            [
                'subject_id' => $subjects->get('Web Development', $subjects->first()),
                'teacher_id' => $teachers->get(1),
                'room_id' => $rooms->get(4),
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'session_days' => json_encode(['friday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('Data Structures', $subjects->first()),
                'teacher_id' => $teachers->get(2),
                'room_id' => $rooms->get(3),
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'session_days' => json_encode(['tuesday', 'thursday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('English Literature', $subjects->first()),
                'teacher_id' => $teachers->get(3),
                'room_id' => $rooms->get(2),
                'start_time' => '11:00:00',
                'end_time' => '12:30:00',
                'session_days' => json_encode(['monday', 'wednesday', 'friday']),
                'session_status' => 'active',
            ],
            [
                'subject_id' => $subjects->get('Chemistry Fundamentals', $subjects->first()),
                'teacher_id' => $teachers->get(4),
                'room_id' => $rooms->get(1),
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'session_days' => json_encode(['tuesday', 'thursday']),
                'session_status' => 'active',
            ],
        ];

        $created = 0;
        foreach ($sessions as $session) {
            $existing = DB::table('class_sessions')
                ->where('subject_id', $session['subject_id'])
                ->where('teacher_id', $session['teacher_id'])
                ->where('room_id', $session['room_id'])
                ->where('start_time', $session['start_time'])
                ->exists();

            if (!$existing) {
                DB::table('class_sessions')->insert($session);
                $created++;
            }
        }

        $this->command->info("✓ Created $created class sessions");
    }
}

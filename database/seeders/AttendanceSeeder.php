<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get all enrolled students with their sessions
        $enrollments = DB::table('student_class_sessions')
            ->join('class_sessions', 'student_class_sessions.session_id', '=', 'class_sessions.session_id')
            ->join('students', 'student_class_sessions.student_id', '=', 'students.student_id')
            ->join('subjects', 'class_sessions.subject_id', '=', 'subjects.subject_id')
            ->join('rooms', 'class_sessions.room_id', '=', 'rooms.room_id')
            ->select(
                'student_class_sessions.student_id',
                'student_class_sessions.session_id',
                'students.firstname',
                'students.lastname',
                'class_sessions.start_time',
                'class_sessions.end_time',
                'subjects.subject_name',
                'rooms.room_name'
            )
            ->get();

        if ($enrollments->isEmpty()) {
            $this->command->warn('⚠ Cannot seed attendance: no student enrollments found');
            return;
        }

        $attendanceRecords = [];
        $statuses = ['present', 'present', 'present', 'present', 'late', 'absent']; // Weighted for more present

        // Generate attendance for the last 7 days
        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = Carbon::now()->subDays($daysAgo);
            
            foreach ($enrollments as $enrollment) {
                // 80% chance of having attendance record for each day
                if (rand(1, 100) <= 80) {
                    $status = $statuses[array_rand($statuses)];
                    $startTime = Carbon::parse($enrollment->start_time);
                    
                    // Calculate time_scanned based on status
                    if ($status === 'present') {
                        // Present: arrived within 5 minutes of start time
                        $timeScanned = $startTime->copy()->subMinutes(rand(0, 5));
                    } elseif ($status === 'late') {
                        // Late: arrived 5-30 minutes after start time
                        $lateDuration = rand(5, 30);
                        $timeScanned = $startTime->copy()->addMinutes($lateDuration);
                    } else {
                        // Absent: no time_scanned
                        $timeScanned = null;
                        $lateDuration = null;
                    }

                    $attendanceRecords[] = [
                        'session_id' => $enrollment->session_id,
                        'student_id' => $enrollment->student_id,
                        'name' => trim($enrollment->firstname . ' ' . $enrollment->lastname),
                        'time_scanned' => $timeScanned ? $timeScanned->format('H:i:s') : null,
                        'status' => $status,
                        'confidence' => $status !== 'absent' ? rand(85, 99) . '%' : null,
                        'late_duration' => isset($lateDuration) ? $lateDuration : null,
                        'total_outside_time' => $status === 'present' ? rand(0, 5) : null,
                        'qr_valid' => $status !== 'absent' ? true : null,
                        'attendance_date' => $date->format('Y-m-d'),
                        'created_at' => $date->format('Y-m-d H:i:s'),
                        'updated_at' => $date->format('Y-m-d H:i:s'),
                    ];
                }
            }
        }

        // Remove duplicates (same student, session, and date)
        $unique = [];
        foreach ($attendanceRecords as $record) {
            $key = $record['session_id'] . '-' . $record['student_id'] . '-' . $record['attendance_date'];
            if (!isset($unique[$key])) {
                $unique[$key] = $record;
            }
        }

        $created = 0;
        foreach ($unique as $record) {
            $exists = DB::table('attendance')
                ->where('session_id', $record['session_id'])
                ->where('student_id', $record['student_id'])
                ->where('attendance_date', $record['attendance_date'])
                ->exists();

            if (!$exists) {
                DB::table('attendance')->insert($record);
                $created++;
            }
        }

        $this->command->info("✓ Created $created attendance records for the last 7 days");
    }
}

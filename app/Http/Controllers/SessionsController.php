<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use Carbon\Carbon;
use Inertia\Inertia;

class SessionsController extends Controller
{
    public function index()
    {
        // Load sessions with related models
        $sessions = ClassSession::with(['subject', 'teacher', 'room'])
            ->get();

        // Metrics
        $activeCount = $sessions->where('session_status', 'active')->count();

        $today = Carbon::today();
        $activeSessionIds = $sessions->where('session_status', 'active')->pluck('session_id');
        $attendanceTodayQuery = Attendance::whereDate('attendance_date', $today);
        if ($activeSessionIds->isNotEmpty()) {
            $attendanceTodayQuery->whereIn('session_id', $activeSessionIds);
        }
        $attendanceToday = $attendanceTodayQuery->get();

        $studentsTracked = $attendanceToday->count();
        $present = $attendanceToday->where('status', 'present')->count();
        $late = $attendanceToday->where('status', 'late')->count();
        $absent = $attendanceToday->where('status', 'absent')->count();
        $total = $present + $late + $absent;
        $avgAttendanceRate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        $flaggedIssues = Attendance::whereDate('attendance_date', $today)
            ->where('qr_valid', false)
            ->count();

        // Map sessions for UI
        $sessionCards = $sessions->map(function ($s) {
            $teacherName = ($s->teacher?->firstname && $s->teacher?->lastname)
                ? $s->teacher->firstname . ' ' . $s->teacher->lastname
                : ($s->teacher?->email ?? 'Teacher');

            $present = Attendance::where('session_id', $s->session_id)->where('status', 'present')->count();
            $late = Attendance::where('session_id', $s->session_id)->where('status', 'late')->count();
            $absent = Attendance::where('session_id', $s->session_id)->where('status', 'absent')->count();

            // Duration text
            $durationText = null;
            if ($s->start_time && $s->end_time) {
                try {
                    $start = Carbon::createFromFormat('H:i:s', $s->start_time);
                    $end = Carbon::createFromFormat('H:i:s', $s->end_time);
                    $minutes = $end->diffInMinutes($start);
                    $hours = intdiv($minutes, 60);
                    $mins = $minutes % 60;
                    $durationText = ($hours ? $hours . 'hr ' : '') . $mins . 'min';
                } catch (\Throwable $e) {
                    $durationText = null;
                }
            }

            return [
                'id' => $s->session_id,
                'subject' => $s->subject?->subject_name ?? '—',
                'code' => 'SES-' . $s->session_id,
                'teacher' => $teacherName,
                'room' => $s->room?->room_name ?? '—',
                'status' => $s->session_status,
                'startTime' => $s->start_time,
                'durationText' => $durationText,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'pending' => 0,
                'isLive' => $s->session_status === 'active',
            ];
        });

        return Inertia::render('session/Session', [
            'activeCount' => $activeCount,
            'studentsTracked' => $studentsTracked,
            'avgAttendanceRate' => $avgAttendanceRate,
            'flaggedIssues' => $flaggedIssues,
            'sessions' => $sessionCards,
        ]);
    }
}

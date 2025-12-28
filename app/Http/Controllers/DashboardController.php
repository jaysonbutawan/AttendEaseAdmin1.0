<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Room;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary metrics
        $totalStudents = Student::query()->count();
        $activeSessions = ClassSession::query()->where('session_status', 'active')->count();

        $roomsTotal = Room::query()->count();
        $roomsInUseCount = ClassSession::query()
            ->where('session_status', 'active')
            ->distinct('room_id')
            ->count('room_id');

        // Live attendance overview for active sessions
        $activeSessionModels = ClassSession::with(['subject', 'teacher', 'room'])
            ->where('session_status', 'active')
            ->get();

        $liveAttendance = $activeSessionModels->map(function ($s) {
            $present = Attendance::where('session_id', $s->session_id)->where('status', 'present')->count();
            $late = Attendance::where('session_id', $s->session_id)->where('status', 'late')->count();
            $absent = Attendance::where('session_id', $s->session_id)->where('status', 'absent')->count();

            $teacherName = ($s->teacher?->firstname && $s->teacher?->lastname)
                ? $s->teacher->firstname . ' ' . $s->teacher->lastname
                : ($s->teacher?->email ?? 'Teacher');

            return [
                'id' => $s->session_id,
                'subject' => $s->subject?->subject_name ?? '—',
                'course' => '—',
                'teacher' => $teacherName,
                'room' => $s->room?->room_name ?? '—',
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'isGeoFenced' => (bool) $s->qr_valid,
                'status' => 'Active',
            ];
        });

        // Room activity panel
        $rooms = Room::with(['classSessions' => function ($q) {
            $q->orderBy('start_time');
        }])->get();

        $roomActivity = $rooms->map(function ($room) {
            $current = $room->classSessions->firstWhere('session_status', 'active');
            $scheduled = $room->classSessions->firstWhere('session_status', 'pending');

            if ($current) {
                return [
                    'id' => $room->room_id,
                    'name' => $room->room_name,
                    'status' => 'occupied',
                    'currentSession' => $current->subject?->subject_name ?? '—',
                    'nextSession' => null,
                    'nextSessionTime' => null,
                ];
            }

            if ($scheduled) {
                return [
                    'id' => $room->room_id,
                    'name' => $room->room_name,
                    'status' => 'scheduled',
                    'currentSession' => null,
                    'nextSession' => $scheduled->subject?->subject_name ?? '—',
                    'nextSessionTime' => $scheduled->start_time,
                ];
            }

            return [
                'id' => $room->room_id,
                'name' => $room->room_name,
                'status' => 'idle',
                'currentSession' => null,
                'nextSession' => null,
                'nextSessionTime' => null,
            ];
        });

        // Weekly attendance (last 5 days)
        $weeklyAttendance = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $present = Attendance::whereDate('attendance_date', $date)->where('status', 'present')->count();
            $late = Attendance::whereDate('attendance_date', $date)->where('status', 'late')->count();
            $absent = Attendance::whereDate('attendance_date', $date)->where('status', 'absent')->count();
            $total = $present + $late + $absent;
            $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
            $weeklyAttendance[] = [
                'day' => $date->format('D'),
                'percentage' => $percentage,
            ];
        }

        // Subject performance (last 30 days)
        $since = Carbon::today()->subDays(30);
        $attendanceRows = Attendance::whereDate('attendance_date', '>=', $since)->get();
        $sessionsById = ClassSession::with('subject')->get()->keyBy('session_id');

        $subjectStats = [];
        foreach ($attendanceRows as $row) {
            $session = $sessionsById->get($row->session_id);
            $subjectName = $session?->subject?->subject_name ?? null;
            if (!$subjectName) {
                continue;
            }
            if (!isset($subjectStats[$subjectName])) {
                $subjectStats[$subjectName] = ['present' => 0, 'total' => 0];
            }
            $subjectStats[$subjectName]['total']++;
            if ($row->status === 'present') {
                $subjectStats[$subjectName]['present']++;
            }
        }

        $subjectPerformance = collect($subjectStats)
            ->map(function ($stats, $name) {
                $percentage = $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100) : 0;
                return [
                    'subject' => $name,
                    'percentage' => $percentage,
                ];
            })
            ->sortByDesc('percentage')
            ->values()
            ->take(5);

        return Inertia::render('Dashboard', [
            'totalStudents' => $totalStudents,
            'activeSessions' => $activeSessions,
            'roomsInUse' => [
                'inUse' => $roomsInUseCount,
                'total' => $roomsTotal,
            ],
            'liveAttendance' => $liveAttendance,
            'roomActivity' => $roomActivity,
            'weeklyAttendance' => $weeklyAttendance,
            'subjectPerformance' => $subjectPerformance,
        ]);
    }
}

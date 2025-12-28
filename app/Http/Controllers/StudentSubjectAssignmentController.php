<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class StudentSubjectAssignmentController extends Controller
{
    /**
     * Assign subjects to students, detect schedule conflicts, and notify admin if conflicts exist.
     * No persistence is performed per request requirement.
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'string',
            'subjects' => 'required|array|min:1',
            'subjects.*.id' => 'required|integer',
            'subjects.*.selectedTimeSlot' => 'required|string',
        ]);

        $subjects = $data['subjects'];
        $conflicts = $this->findTimeConflicts($subjects);

        // Notify admin if conflicts
        if (!empty($conflicts)) {
            $message = $this->formatConflictMessage($data['student_ids'], $conflicts);
            $this->notifyAdmin($message);
        }

        return response()->json([
            'success' => true,
            'conflicts' => $conflicts,
            'notified_admin' => !empty($conflicts),
        ]);
    }

    /**
     * Parse time slot strings and detect overlaps between selected subjects.
     * Expected time format: "HH:MM AM - HH:MM PM".
     */
    private function findTimeConflicts(array $subjects): array
    {
        $slots = [];
        foreach ($subjects as $s) {
            $parsed = $this->parseTimeSlot($s['selectedTimeSlot']);
            if (!$parsed) {
                // skip unparsable entries
                continue;
            }
            $slots[] = [
                'subject_id' => $s['id'],
                'start' => $parsed['start'],
                'end' => $parsed['end'],
                'label' => $s['selectedTimeSlot'],
            ];
        }

        $conflicts = [];
        $count = count($slots);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if ($this->overlaps($slots[$i]['start'], $slots[$i]['end'], $slots[$j]['start'], $slots[$j]['end'])) {
                    $conflicts[] = [
                        'subject_a' => $slots[$i]['subject_id'],
                        'subject_b' => $slots[$j]['subject_id'],
                        'slot_a' => $slots[$i]['label'],
                        'slot_b' => $slots[$j]['label'],
                    ];
                }
            }
        }
        return $conflicts;
    }

    private function overlaps(int $aStart, int $aEnd, int $bStart, int $bEnd): bool
    {
        // Overlap if ranges intersect
        return $aStart < $bEnd && $bStart < $aEnd;
    }

    /**
     * Convert a time slot string to minutes since midnight range.
     */
    private function parseTimeSlot(string $slot): ?array
    {
        // e.g. "12:00 PM - 1:00 PM"
        $parts = explode('-', $slot);
        if (count($parts) !== 2) {
            return null;
        }
        $startStr = trim($parts[0]);
        $endStr = trim($parts[1]);

        $start = $this->toMinutes($startStr);
        $end = $this->toMinutes($endStr);
        if ($start === null || $end === null) {
            return null;
        }
        return ['start' => $start, 'end' => $end];
    }

    private function toMinutes(string $timeStr): ?int
    {
        // Expect format like "H:MM AM" or "HH:MM PM"
        if (!preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $timeStr, $m)) {
            return null;
        }
        $hour = (int) $m[1];
        $min = (int) $m[2];
        $ampm = strtoupper($m[3]);
        if ($hour === 12) {
            $hour = 0;
        }
        if ($ampm === 'PM') {
            $hour += 12;
        }
        return $hour * 60 + $min;
    }

    private function formatConflictMessage(array $studentIds, array $conflicts): string
    {
        $lines = [];
        $lines[] = 'Schedule conflict detected for students: ' . implode(', ', $studentIds);
        foreach ($conflicts as $c) {
            $lines[] = sprintf(
                'Subject %d (%s) overlaps with Subject %d (%s)',
                $c['subject_a'],
                $c['slot_a'],
                $c['subject_b'],
                $c['slot_b']
            );
        }
        return implode("\n", $lines);
    }

    private function notifyAdmin(string $message): void
    {
        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::raw($message, function ($mail) use ($adminEmail) {
                    $mail->to($adminEmail)->subject('AttendEase: Schedule Conflict Notification');
                });
            } catch (\Throwable $e) {
                Log::warning('Failed to send admin email: ' . $e->getMessage());
            }
        }
        Log::info('[Schedule Conflict] ' . $message);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{

    public function index()
{
    $subjects = Subject::query()
        ->select('subject_id', 'subject_name')
        ->orderBy('subject_name')
        ->get();

    return response()->json([
        'subjects' => $subjects,
    ]);
}

    /**
     * Total number of subjects.
     */
    public function totalCount()
    {
        $total = Subject::count();

        return response()->json([
            'success' => true,
            'total_subjects' => $total,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255|unique:subjects,subject_name',
        ]);

        $subject = Subject::create([
            'subject_name' => $validated['subject_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully.',
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::where('subject_id', $id)->firstOrFail();

        $validated = $request->validate([
            'subject_name' => 'required|string|max:255|unique:subjects,subject_name,' . $subject->subject_id . ',subject_id',
        ]);

        $subject->update([
            'subject_name' => $validated['subject_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully.',
            'subject' => $subject,
        ]);
    }

        public function indexWithSessions()
    {
        $subjects = Subject::with([
            'sessions' => function ($query) {
                $query->orderBy('start_time');
            }
        ])->get();

        return response()->json([
            'subjects' => $subjects
        ]);
    }
}

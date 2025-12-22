<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{

     public function index()
    {
        $subjects = Subject::orderBy('subject_name')
            ->pluck('subject_name');

        return response()->json([
            'subjects' => $subjects,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'sometimes|integer|exists:subjects,subject_id',
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
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{

    public function index()
    {
        $courses = Course::all();
        return response()->json([
            'courses' => $courses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255|unique:courses,course_name',
        ]);

        $course = Course::create([
            'course_name' => $validated['course_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully.',
            'course' => $course,
        ]);
    }

   public function destroy($id)
{    $course = Course::find($id);

    if (!$course) {
        \Log::error("Course with id $id not found.");
        return response()->json([
            'message' => 'Course not found.',
        ], 404);  
    }
    \Log::info("Course with id $id deleted successfully.");

    $course->delete();

    return response()->json([
        'success' => true,
        'message' => 'Course deleted successfully.',
    ]);
}

}

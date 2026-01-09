<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'course_id' => 1,
                'course_name' => 'Bachelor of Science in Computer Science',
            ],
            [
                'course_id' => 2,
                'course_name' => 'Bachelor of Science in Information Technology',
            ],
        ];

        foreach ($courses as $course) {
            DB::table('courses')->updateOrInsert(
                ['course_id' => $course['course_id']],
                $course
            );
        }

        $this->command->info('✓ Created ' . count($courses) . ' courses');
    }
}

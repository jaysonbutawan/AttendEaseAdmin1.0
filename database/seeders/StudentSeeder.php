<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'student_id' => 'STU2024001',
                'firebase_uid' => 'firebase_stu001',
                'firstname' => 'Alice',
                'lastname' => 'Anderson',
                'email' => 'alice.anderson@student.edu',
                'contact_number' => '555-1001',
                'course_id' => 1,
                'year' => 1,
            ],
            [
                'student_id' => 'STU2024002',
                'firebase_uid' => 'firebase_stu002',
                'firstname' => 'Bob',
                'lastname' => 'Baker',
                'email' => 'bob.baker@student.edu',
                'contact_number' => '555-1002',
                'course_id' => 1,
                'year' => 1,
            ],
            [
                'student_id' => 'STU2024003',
                'firebase_uid' => 'firebase_stu003',
                'firstname' => 'Carol',
                'lastname' => 'Chen',
                'email' => 'carol.chen@student.edu',
                'contact_number' => '555-1003',
                'course_id' => 1,
                'year' => 2,
            ],
            [
                'student_id' => 'STU2024004',
                'firebase_uid' => 'firebase_stu004',
                'firstname' => 'Daniel',
                'lastname' => 'Davis',
                'email' => 'daniel.davis@student.edu',
                'contact_number' => '555-1004',
                'course_id' => 1,
                'year' => 2,
            ],
            [
                'student_id' => 'STU2024005',
                'firebase_uid' => 'firebase_stu005',
                'firstname' => 'Emma',
                'lastname' => 'Evans',
                'email' => 'emma.evans@student.edu',
                'contact_number' => '555-1005',
                'course_id' => 1,
                'year' => 3,
            ],
            [
                'student_id' => 'STU2024006',
                'firebase_uid' => 'firebase_stu006',
                'firstname' => 'Frank',
                'lastname' => 'Fisher',
                'email' => 'frank.fisher@student.edu',
                'contact_number' => '555-1006',
                'course_id' => 1,
                'year' => 3,
            ],
            [
                'student_id' => 'STU2024007',
                'firebase_uid' => 'firebase_stu007',
                'firstname' => 'Grace',
                'lastname' => 'Garcia',
                'email' => 'grace.garcia@student.edu',
                'contact_number' => '555-1007',
                'course_id' => 1,
                'year' => 4,
            ],
            [
                'student_id' => 'STU2024008',
                'firebase_uid' => 'firebase_stu008',
                'firstname' => 'Henry',
                'lastname' => 'Harris',
                'email' => 'henry.harris@student.edu',
                'contact_number' => '555-1008',
                'course_id' => 1,
                'year' => 4,
            ],
            [
                'student_id' => 'STU2024009',
                'firebase_uid' => 'firebase_stu009',
                'firstname' => 'Ivy',
                'lastname' => 'Jackson',
                'email' => 'ivy.jackson@student.edu',
                'contact_number' => '555-1009',
                'course_id' => 1,
                'year' => 1,
            ],
            [
                'student_id' => 'STU2024010',
                'firebase_uid' => 'firebase_stu010',
                'firstname' => 'Jack',
                'lastname' => 'Jones',
                'email' => 'jack.jones@student.edu',
                'contact_number' => '555-1010',
                'course_id' => 1,
                'year' => 2,
            ],
        ];

        foreach ($students as $student) {
            DB::table('students')->updateOrInsert(
                ['student_id' => $student['student_id']],
                $student
            );
        }

        $this->command->info('✓ Created ' . count($students) . ' students');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'teacher_id' => 'TCH001',
                'firebase_uid' => 'firebase_tch001',
                'firstname' => 'John',
                'lastname' => 'Smith',
                'email' => 'john.smith@school.edu',
                'contact_number' => '555-0101',
            ],
            [
                'teacher_id' => 'TCH002',
                'firebase_uid' => 'firebase_tch002',
                'firstname' => 'Sarah',
                'lastname' => 'Johnson',
                'email' => 'sarah.johnson@school.edu',
                'contact_number' => '555-0102',
            ],
            [
                'teacher_id' => 'TCH003',
                'firebase_uid' => 'firebase_tch003',
                'firstname' => 'Michael',
                'lastname' => 'Williams',
                'email' => 'michael.williams@school.edu',
                'contact_number' => '555-0103',
            ],
            [
                'teacher_id' => 'TCH004',
                'firebase_uid' => 'firebase_tch004',
                'firstname' => 'Emily',
                'lastname' => 'Brown',
                'email' => 'emily.brown@school.edu',
                'contact_number' => '555-0104',
            ],
            [
                'teacher_id' => 'TCH005',
                'firebase_uid' => 'firebase_tch005',
                'firstname' => 'David',
                'lastname' => 'Martinez',
                'email' => 'david.martinez@school.edu',
                'contact_number' => '555-0105',
            ],
        ];

        foreach ($teachers as $teacher) {
            DB::table('teachers')->updateOrInsert(
                ['teacher_id' => $teacher['teacher_id']],
                $teacher
            );
        }

        $this->command->info('✓ Created ' . count($teachers) . ' teachers');
    }
}

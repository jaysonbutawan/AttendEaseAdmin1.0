<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['subject_name' => 'Advanced Mathematics'],
            ['subject_name' => 'Computer Programming'],
            ['subject_name' => 'Database Systems'],
            ['subject_name' => 'Physics 101'],
            ['subject_name' => 'Chemistry Fundamentals'],
            ['subject_name' => 'English Literature'],
            ['subject_name' => 'Web Development'],
            ['subject_name' => 'Data Structures'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['subject_name' => $subject['subject_name']],
                $subject
            );
        }

        $this->command->info('✓ Created ' . count($subjects) . ' subjects');
    }
}

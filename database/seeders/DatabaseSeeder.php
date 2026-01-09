<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in order of dependencies
        $this->call([
            CourseSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            SubjectSeeder::class,
            RoomSeeder::class,
            ClassSessionSeeder::class,
            StudentClassSessionSeeder::class,
        ]);

        // Create admin user if needed
        if (!User::where('email', 'admin@attendease.edu')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@attendease.edu',
            ]);
            $this->command->info('✓ Created admin user');
        }

        $this->command->info('✅ Database seeding completed!');
    }
}

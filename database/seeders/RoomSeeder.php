<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['room_name' => 'Room 101', 'color' => '#3B82F6'],
            ['room_name' => 'Room 102', 'color' => '#10B981'],
            ['room_name' => 'Room 103', 'color' => '#F59E0B'],
            ['room_name' => 'Lab A', 'color' => '#8B5CF6'],
            ['room_name' => 'Lab B', 'color' => '#EC4899'],
            ['room_name' => 'Auditorium', 'color' => '#6366F1'],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->updateOrInsert(
                ['room_name' => $room['room_name']],
                $room
            );
        }

        $this->command->info('✓ Created ' . count($rooms) . ' rooms');
    }
}

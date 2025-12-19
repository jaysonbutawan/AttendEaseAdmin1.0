<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_name' => ['required', 'string', 'max:255'],
            'color'     => ['nullable', 'string', 'max:50'],
            'polygon'   => ['required', 'array', 'min:3'],
            'polygon.*.latitude' => ['required', 'numeric'],
            'polygon.*.longitude' => ['required', 'numeric'],
            'polygon.*.point_order' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data) {
            $roomId = DB::table('rooms')->insertGetId([
                'room_name' => $data['room_name'],
                'color' => $data['color'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'room_id');

            $points = array_map(function ($p) use ($roomId) {
                return [
                    'room_id' => $roomId,
                    'latitude' => $p['latitude'],
                    'longitude' => $p['longitude'],
                    'point_order' => $p['point_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $data['polygon']);

            DB::table('room_polygons')->insert($points);

            $room = DB::table('rooms')->where('room_id', $roomId)->first();

            return response()->json([
                'room' => $room,
                'polygon_points_saved' => count($points),
            ], 201);
        });
    }

    public function index()
{
    $rooms = DB::table('rooms')
        ->select('room_id', 'room_name', 'color')
        ->orderBy('room_id', 'desc')
        ->get()
        ->map(function ($room) {
            $points = DB::table('room_polygons')
                ->where('room_id', $room->room_id)
                ->orderBy('point_order')
                ->get(['latitude', 'longitude', 'point_order']);

            return [
                'room_id' => $room->room_id,
                'room_name' => $room->room_name,
                'color' => $room->color,
                'polygon' => $points,
            ];
        });

    return response()->json(['rooms' => $rooms]);
}

public function destroy($id)
{
    return DB::transaction(function () use ($id) {
        DB::table('room_polygons')->where('room_id', $id)->delete();
        DB::table('rooms')->where('room_id', $id)->delete();

        return response()->json(['deleted' => true]);
    });
}

}

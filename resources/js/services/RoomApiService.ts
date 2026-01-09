import axios from 'axios';

export interface Room {
    id: number;
    name: string;
    capacity: string | number;
    color: string;
}

export class RoomApiService {
    async fetchRooms() {
        const res = await axios.get('/room_polygon');
        return res.data.rooms ?? [];
    }

    async createRoom(payload: {
        room_name: string;
        capacity: number | string;
        color: string;
        polygon: Array<{ latitude: number; longitude: number; point_order: number }>;
    }) {
        const res = await axios.post('/room_polygon', payload);
        return res.data;
    }

    async updateRoom(roomId: number, payload: {
        room_name: string;
        capacity: number | string;
        color: string;
    }) {
        const res = await axios.put(`/room_polygon/${roomId}`, payload);
        return res.data;
    }

    async deleteRoom(roomId: number) {
        await axios.delete(`/room_polygon/${roomId}`);
    }
}

export class RoomDataMapper {
    static mapFromApi(apiRoom: any): Room {
        return {
            id: apiRoom.room_id,
            name: apiRoom.room_name,
            capacity: apiRoom.capacity ?? 'N/A',
            color: apiRoom.color ?? '#3b82f6',
        };
    }

    static mapToPayload(room: { name: string; capacity: number | string; color: string }) {
        return {
            room_name: room.name,
            capacity: room.capacity,
            color: room.color,
        };
    }
}

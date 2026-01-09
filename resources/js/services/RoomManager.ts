import { RoomApiService, RoomDataMapper, Room } from './RoomApiService';
import { PolygonManager } from './PolygonManager';

export class RoomManager {
    private apiService: RoomApiService;
    private polygonManager: PolygonManager | null = null;

    constructor(polygonManager?: PolygonManager) {
        this.apiService = new RoomApiService();
        this.polygonManager = polygonManager || null;
    }

    setPolygonManager(polygonManager: PolygonManager) {
        this.polygonManager = polygonManager;
    }

    async loadAndDrawRooms(
        onRoomsLoaded?: (rooms: Room[]) => void,
        onDrawRoom?: (room: any) => void,
    ) {
        try {
            const apiRooms = await this.apiService.fetchRooms();
            const rooms = apiRooms.map((r: any) => RoomDataMapper.mapFromApi(r));
            
            onRoomsLoaded?.(rooms);
            apiRooms.forEach((r: any) => onDrawRoom?.(r));
        } catch (err) {
            console.error('Failed to load rooms:', err);
            throw err;
        }
    }

    async createRoom(
        roomData: { name: string; capacity: number | string; color: string },
        polygonPath: Array<{ latitude: number; longitude: number; point_order: number }>,
    ) {
        const payload = {
            ...RoomDataMapper.mapToPayload(roomData),
            polygon: polygonPath,
        };

        const response = await this.apiService.createRoom(payload);
        return {
            room: RoomDataMapper.mapFromApi(response.room),
            apiRoom: response.room,
        };
    }

    async updateRoom(
        roomId: number,
        roomData: { name: string; capacity: number | string; color: string },
    ) {
        const payload = RoomDataMapper.mapToPayload(roomData);
        const response = await this.apiService.updateRoom(roomId, payload);

        if (this.polygonManager && response.room.color) {
            this.polygonManager.updatePolygonColor(roomId, response.room.color);
        }

        return RoomDataMapper.mapFromApi(response.room);
    }

    async deleteRoom(roomId: number) {
        await this.apiService.deleteRoom(roomId);

        // Remove polygon if polygon manager is available
        if (this.polygonManager) {
            this.polygonManager.removePolygon(roomId);
        }
    }

    validateRoomData(name: string, polygon: any): { valid: boolean; error?: string } {
        if (!name.trim()) {
            return { valid: false, error: 'Enter room name first.' };
        }

        if (!polygon || polygon.length < 3) {
            return { valid: false, error: 'Draw a valid polygon first (at least 3 points).' };
        }

        return { valid: true };
    }
}

import { ref } from 'vue';

export class PolygonManager {
    private activePolygon: google.maps.Polygon | null = null;
    private roomPolygons: Record<number, google.maps.Polygon> = {};
    private map: google.maps.Map | null = null;
    private drawingManager: google.maps.drawing.DrawingManager | null = null;
    private googleRef: any = null;

    constructor(
        map: google.maps.Map,
        drawingManager: google.maps.drawing.DrawingManager,
        googleRef: any,
    ) {
        this.map = map;
        this.drawingManager = drawingManager;
        this.googleRef = googleRef;
    }

    getActivePolygon(): google.maps.Polygon | null {
        return this.activePolygon;
    }

    setActivePolygon(polygon: google.maps.Polygon | null) {
        this.activePolygon = polygon;
    }

    clearActivePolygon() {
        if (this.activePolygon) {
            this.activePolygon.setMap(null);
            this.activePolygon = null;
        }
    }

    getActivePolygonPath() {
        if (!this.activePolygon) return null;

        return this.activePolygon
            .getPath()
            .getArray()
            .map((p, idx) => ({
                latitude: p.lat(),
                longitude: p.lng(),
                point_order: idx + 1,
            }));
    }

    enableDrawMode() {
        if (!this.drawingManager) return;
        this.drawingManager.setDrawingMode(this.googleRef.maps.drawing.OverlayType.POLYGON);
    }

    disableDrawMode() {
        if (!this.drawingManager) return;
        this.drawingManager.setDrawingMode(null);
    }

    drawRoomPolygon(room: any, onHover?: (roomId: number, roomName: string) => void, onHoverEnd?: () => void, onClickRoom?: (room: any) => void) {
        if (!this.map || !this.googleRef) return;
        if (!room.polygon || room.polygon.length < 3) return;

        const path = room.polygon.map((p: any) => ({
            lat: Number(p.latitude),
            lng: Number(p.longitude),
        }));

        const color = room.color && String(room.color).startsWith('#')
            ? room.color
            : this.getRandomColor();

        // Remove old polygon if exists
        if (this.roomPolygons[room.room_id]) {
            this.roomPolygons[room.room_id].setMap(null);
        }

        const poly = new this.googleRef.maps.Polygon({
            paths: path,
            fillColor: color,
            fillOpacity: 0.35,
            strokeColor: color,
            strokeWeight: 2,
            clickable: true,
        });

        poly.setMap(this.map);
        this.roomPolygons[room.room_id] = poly;

        // Add event listeners
        poly.addListener('mouseover', () => {
            onHover?.(room.room_id, room.room_name);
            poly.setOptions({ fillOpacity: 0.6, strokeWeight: 3 });
        });

        poly.addListener('mouseout', () => {
            onHoverEnd?.();
            poly.setOptions({ fillOpacity: 0.35, strokeWeight: 2 });
        });

        poly.addListener('click', () => {
            onClickRoom?.(room);
        });
    }

    updatePolygonColor(roomId: number, color: string) {
        const poly = this.roomPolygons[roomId];
        if (poly) {
            poly.setOptions({
                fillColor: color,
                strokeColor: color,
            });
        }
    }

    removePolygon(roomId: number) {
        const poly = this.roomPolygons[roomId];
        if (poly) {
            poly.setMap(null);
            delete this.roomPolygons[roomId];
        }
    }

    private getRandomColor(): string {
        const colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b', '#ef4444', '#06b6d4'];
        return colors[Math.floor(Math.random() * colors.length)];
    }
}

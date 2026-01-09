import { loadGoogleMaps } from '@/services/googleMaps';
import { PolygonManager } from './PolygonManager';

export class MapInitializer {
    private map: google.maps.Map | null = null;
    private drawingManager: google.maps.drawing.DrawingManager | null = null;
    private googleRef: any = null;
    private polygonManager: PolygonManager | null = null;

    async initialize(
        mapElement: HTMLDivElement,
        polygonColor: string,
    ): Promise<{
        map: google.maps.Map;
        drawingManager: google.maps.drawing.DrawingManager;
        polygonManager: PolygonManager;
        googleRef: any;
    }> {
        // Load Google Maps
        this.googleRef = await loadGoogleMaps();

        // Create map instance
        this.map = new this.googleRef.maps.Map(mapElement, {
            center: { lat: 7.457697110755575, lng: 125.7923471118688 },
            zoom: 18,
            mapTypeId: 'satellite',
            disableDefaultUI: false,
        });

        // Create drawing manager
        this.drawingManager = new this.googleRef.maps.drawing.DrawingManager({
            drawingMode: null,
            drawingControl: false,
            polygonOptions: {
                fillColor: polygonColor,
                fillOpacity: 0.35,
                strokeColor: polygonColor,
                strokeWeight: 2,
                editable: true,
                draggable: false,
            },
        });

        if (this.drawingManager && this.map) {
            this.drawingManager.setMap(this.map);
        }

        // Create polygon manager
        this.polygonManager = new PolygonManager(
            this.map!,
            this.drawingManager!,
            this.googleRef,
        );

        return {
            map: this.map!,
            drawingManager: this.drawingManager!,
            polygonManager: this.polygonManager,
            googleRef: this.googleRef,
        };
    }

    getMap(): google.maps.Map | null {
        return this.map;
    }

    getPolygonManager(): PolygonManager | null {
        return this.polygonManager;
    }
}

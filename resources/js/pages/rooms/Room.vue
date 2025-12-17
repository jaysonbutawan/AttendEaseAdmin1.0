<script setup lang="ts">
import { ref,onMounted } from 'vue';
import { loadGoogleMaps } from '@/services/googleMaps';
// Types for our Area Management
interface Room {
  id: number;
  name: string;
  type: string;
  capacity: string | number;
  color: string;
}

interface Area {
  name: string;
  polygonColor: string;
  rooms: Room[];
}

// State
const selectedArea = ref<Area>({
  name: 'East Wing',
  polygonColor: '#3b82f6', // Blue
  rooms: [
    { id: 1, name: 'Classroom A', type: 'Class', capacity: 30, color: 'green' },
    { id: 2, name: 'Conference Room', type: 'Meeting', capacity: 30, color: 'green' },
    { id: 3, name: 'Conference Room', type: 'Meeting', capacity: 12, color: 'blue' },
    { id: 4, name: 'Storage', type: 'Utility', capacity: 'N/A', color: 'orange' },
  ]
});

const editingRoom = ref<Room>({ ...selectedArea.value.rooms[2] });

const saveChanges = () => {
  console.log('Saving room details:', editingRoom.value);
};
const mapEl = ref<HTMLDivElement | null>(null);
let map: google.maps.Map | null = null;
let drawingManager: google.maps.drawing.DrawingManager | null = null;
let activePolygon: google.maps.Polygon | null = null;

const enableDrawPolygon = () => {
  if (!drawingManager) return;
  drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
};

const enableMoveMap = () => {
  if (!drawingManager) return;
  drawingManager.setDrawingMode(null);
};

const saveAll = () => {
  if (!activePolygon) return;

  const path = activePolygon.getPath().getArray().map(p => ({
    lat: p.lat(),
    lng: p.lng(),
  }));

  console.log("Polygon coords to save:", path);
  // TODO: send `path` to Laravel via axios/fetch
};

onMounted(async () => {
  const google = await loadGoogleMaps();

  map = new google.maps.Map(mapEl.value!, {
    center: { lat: 14.5995, lng: 120.9842 }, // change to your location
    zoom: 18,
    mapTypeId: "satellite",
    disableDefaultUI: false,
  });

  drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: null,
    drawingControl: false, // we use your custom buttons
    polygonOptions: {
      fillColor: selectedArea.value.polygonColor,
      fillOpacity: 0.35,
      strokeColor: selectedArea.value.polygonColor,
      strokeWeight: 2,
      editable: true,
      draggable: false,
    },
  });

  drawingManager.setMap(map);

  google.maps.event.addListener(drawingManager, "overlaycomplete", (e: any) => {
    if (e.type !== google.maps.drawing.OverlayType.POLYGON) return;

    // Remove previous polygon if you only want one active area
    if (activePolygon) activePolygon.setMap(null);

    activePolygon = e.overlay as google.maps.Polygon;

    // Stop drawing after 1 polygon
    drawingManager?.setDrawingMode(null);
  });
});
</script>

<template>
    <div class="flex flex-col h-screen bg-gray-100 font-sans text-gray-700">
      <div class="flex items-center gap-4 bg-white p-2 border-b shadow-sm">
        <button class="flex flex-col items-center px-3 py-1 hover:bg-gray-100 rounded">
          <span class="text-xs">Draw Polygon</span>
        </button>
        <button class="flex flex-col items-center px-3 py-1 hover:bg-gray-100 rounded">
          <span class="text-xs">Move Map</span>
        </button>
        <div class="h-8 w-px bg-gray-200 mx-2"></div>
        <button class="text-xs font-semibold text-blue-600 border border-blue-600 px-4 py-1 rounded">Save All</button>
      </div>

     <div class="flex flex-1 overflow-hidden">
      <div class="flex-1 relative bg-white p-8 overflow-hidden">
        <div
          class="absolute inset-0 opacity-10 pointer-events-none"
          style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"
        ></div>

        <div class="relative w-full h-full border-2 border-dashed border-gray-200 rounded-lg overflow-hidden">
          <div ref="mapEl" class="w-full h-full"></div>
        </div>
      </div>

        <div class="w-80 bg-gray-50 border-l flex flex-col gap-4 p-4 overflow-y-auto">
          
          <div class="bg-white p-4 rounded-xl shadow-sm border">
            <h3 class="text-lg font-bold mb-3">Area & Room Management</h3>
            <div class="flex items-center bg-gray-100 p-2 rounded border">
              <span class="mr-2">🔒</span>
              <span class="text-sm font-medium">{{ selectedArea.name }} (Blue Polygon)</span>
            </div>
            <button class="text-red-500 text-xs mt-2 font-semibold">Delete Area</button>
          </div>

          <div class="bg-white p-4 rounded-xl shadow-sm border flex-1">
            <h4 class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-wider">Rooms in {{ selectedArea.name }}</h4>
            
            <div class="space-y-3">
              <div v-for="room in selectedArea.rooms" :key="room.id" class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg border border-transparent hover:border-gray-200">
                <div class="flex items-center gap-3">
                  <div :class="`w-8 h-8 rounded-full flex items-center justify-center text-white bg-${room.color}-500 text-xs` ">
  {{ room.name[0] }}
</div>
                  <div>
                    <p class="text-sm font-bold leading-tight">{{ room.name }}</p>
                    <p class="text-xs text-gray-400">Type: {{ room.type }}</p>
                  </div>
                </div>
                <button class="text-xs bg-gray-100 px-2 py-1 rounded font-semibold border hover:bg-white">Edit</button>
              </div>
            </div>

            <button class="w-full mt-6 py-2 border-2 border-dashed border-blue-300 text-blue-500 rounded-lg text-sm font-bold hover:bg-blue-50">
              + Add New Room
            </button>
          </div>

          <div class="bg-white p-4 rounded-xl shadow-sm border">
            <h4 class="text-sm font-bold mb-4">Edit Room Details</h4>
            <div class="space-y-3">
              <div>
                <label class="text-xs font-semibold text-gray-500">Room Name</label>
                <input v-model="editingRoom.name" type="text" class="w-full border rounded p-2 text-sm mt-1" />
              </div>
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="text-xs font-semibold text-gray-500">Room Type</label>
                  <select class="w-full border rounded p-2 text-sm mt-1">
                    <option>Meeting</option>
                    <option>Class</option>
                  </select>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-500">Capacity</label>
                  <input v-model="editingRoom.capacity" type="text" class="w-full border rounded p-2 text-sm mt-1" />
                </div>
              </div>
            </div>
            <div class="flex gap-2 mt-6">
              <button @click="saveChanges" class="flex-1 bg-blue-500 text-white py-2 rounded font-bold text-sm shadow-md">Save Changes</button>
              <button class="px-4 py-2 bg-gray-100 rounded text-sm font-semibold">Cancel</button>
            </div>
          </div>

        </div>
      </div>
    </div>
</template>
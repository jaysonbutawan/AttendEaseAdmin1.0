<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { loadGoogleMaps } from '@/services/googleMaps';
import axios from 'axios';
import { onMounted, ref } from 'vue';

interface Room {
    id: number;
    name: string;
    capacity: string | number;
    color: string;
}

interface Area {
    name: string;
    polygonColor: string;
    rooms: Room[];
}

const selectedArea = ref<Area>({
    name: 'East Wing',
    polygonColor: '#3b82f6',
    rooms: [
    ],
});

const isEditMode = ref(false);
const editingRoomId = ref<number | null>(null);

const editingRoom = ref<Room>({ ...selectedArea.value.rooms[0] });
const saveChanges = () =>
    console.log('Saving room details:', editingRoom.value);

const showRoomPrompt = ref(false);
const isSaving = ref(false);

const newRoom = ref({
    name: '',
    capacity: 0 as number | string,
    color: 'blue',
});


const editRoom = (room: Room) => {
  // open modal in edit mode
  isEditMode.value = true;
  editingRoomId.value = room.id;

  newRoom.value.name = room.name;
  newRoom.value.capacity = room.capacity;
  newRoom.value.color = room.color;

  // IMPORTANT:
  // Editing should not depend on the activePolygon you draw.
  // You can optionally allow polygon editing later by making stored polygons editable.
  showRoomPrompt.value = true;
};

const updateRoom = async () => {
  if (!editingRoomId.value) return;
  if (!newRoom.value.name.trim()) {
    alert("Enter room name first.");
    return;
  }

  isSaving.value = true;
  try {
    const payload = {
      room_name: newRoom.value.name,
      capacity: newRoom.value.capacity,
      color: newRoom.value.color,
      // polygon optional (only send if you support polygon editing)
    };

    const res = await axios.put(`/room_polygon/${editingRoomId.value}`, payload);

    // update list
    const idx = selectedArea.value.rooms.findIndex(r => r.id === editingRoomId.value);
    if (idx !== -1) {
      selectedArea.value.rooms[idx] = {
        ...selectedArea.value.rooms[idx],
        name: res.data.room.room_name,
        capacity: res.data.room.capacity,
        color: res.data.room.color,
      };
    }

    // update polygon color/name on map (no need to redraw points)
    const poly = roomPolygons[editingRoomId.value];
    if (poly) {
      poly.setOptions({
        fillColor: res.data.room.color,
        strokeColor: res.data.room.color,
      });
    }

    showRoomPrompt.value = false;
    isEditMode.value = false;
    editingRoomId.value = null;
  } catch (err) {
    console.error(err);
    alert("Failed to update room.");
  } finally {
    isSaving.value = false;
  }
};

const deleteRoom = async (roomId: number) => {
  const ok = confirm("Delete this room and its polygon?");
  if (!ok) return;

  try {
    await axios.delete(`/room_polygon/${roomId}`);

    selectedArea.value.rooms = selectedArea.value.rooms.filter(r => r.id !== roomId);

    if (roomPolygons[roomId]) {
      roomPolygons[roomId].setMap(null);
      delete roomPolygons[roomId];
    }

    if (editingRoomId.value === roomId) {
      showRoomPrompt.value = false;
      isEditMode.value = false;
      editingRoomId.value = null;
    }
  } catch (err) {
    console.error(err);
    alert("Failed to delete room.");
  }
};


const mapEl = ref<HTMLDivElement | null>(null);
let map: google.maps.Map | null = null;
let drawingManager: google.maps.drawing.DrawingManager | null = null;
let activePolygon: google.maps.Polygon | null = null;

let googleRef: any = null;
const roomPolygons: Record<number, google.maps.Polygon> = {};

const enableDrawPolygon = () => {
    if (!drawingManager) return;
    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
};

const enableMoveMap = () => {
    if (!drawingManager) return;
    drawingManager.setDrawingMode(null);
};

const clearPolygon = () => {
    if (activePolygon) {
        activePolygon.setMap(null);
        activePolygon = null;
    }
};

const getActivePolygonPath = () => {
    if (!activePolygon) return null;

    return activePolygon
        .getPath()
        .getArray()
        .map((p, idx) => ({
            latitude: p.lat(),
            longitude: p.lng(),
            point_order: idx + 1,
        }));
};

const openRoomPrompt = () => {
    newRoom.value.name = '';
    showRoomPrompt.value = true;
};

const cancelRoomPrompt = (removePolygon = true) => {
    showRoomPrompt.value = false;
    if (removePolygon) clearPolygon();
};

const saveRoomWithPolygon = async () => {
    const polygon = getActivePolygonPath();
    if (!polygon || polygon.length < 3) {
        alert('Draw a valid polygon first (at least 3 points).');
        return;
    }
    if (!newRoom.value.name.trim()) {
        alert('Enter room name first.');
        return;
    }

    isSaving.value = true;
    try {
        const payload = {
            room_name: newRoom.value.name,
            capacity: newRoom.value.capacity,
            color: newRoom.value.color,
            polygon,
        };

        const res = await axios.post('/room_polygon', payload);

        selectedArea.value.rooms.push({
            id: res.data.room.room_id,
            name: res.data.room.room_name,
            capacity: res.data.room.capacity ?? newRoom.value.capacity,
            color: res.data.room.color ?? newRoom.value.color,
        });

        drawRoomPolygon({
            room_id: res.data.room.room_id,
            room_name: res.data.room.room_name,
            color: res.data.room.color || newRoom.value.color,
            polygon, 
        });

        showRoomPrompt.value = false;
        clearPolygon();
        newRoom.value.name = '';
    } catch (err) {
        console.error(err);
        alert('Failed to save. Check API / validation.');
    } finally {
        isSaving.value = false;
    }
};

const randomHexColor = () => {
    const letters = '0123456789ABCDEF';
    let c = '#';
    for (let i = 0; i < 6; i++) c += letters[Math.floor(Math.random() * 16)];
    return c;
};

const loadRoomsAndDraw = async () => {
  try {
    const res = await axios.get('/room_polygon');
    const rooms = res.data.rooms ?? [];

    selectedArea.value.rooms = rooms.map((r: any) => ({
      id: r.room_id,
      name: r.room_name,
      type: 'Room',
      capacity: 'N/A',
      color: r.color ?? 'blue',
    }));

    rooms.forEach((r: any) => drawRoomPolygon(r));
  } catch (err) {
    console.error('loadRoomsAndDraw failed:', err);
  }
};



const drawRoomPolygon = (room: any) => {
    if (!map || !googleRef) return;
    if (!room.polygon || room.polygon.length < 3) return;

    const path = room.polygon.map((p: any) => ({
        lat: Number(p.latitude),
        lng: Number(p.longitude),
    }));

    const color =
        room.color && String(room.color).startsWith('#')
            ? room.color
            : randomHexColor();

    if (roomPolygons[room.room_id]) {
        roomPolygons[room.room_id].setMap(null);
    }

    const poly = new googleRef.maps.Polygon({
        paths: path,
        fillColor: color,
        fillOpacity: 0.35,
        strokeColor: color,
        strokeWeight: 2,
        clickable: true,
    });

    poly.setMap(map);
    roomPolygons[room.room_id] = poly;

    poly.addListener('click', () => alert(room.room_name));
};

onMounted(async () => {
    const google = await loadGoogleMaps();
    googleRef = google;

    map = new google.maps.Map(mapEl.value!, {
        center: { lat: 14.5995, lng: 120.9842 },
        zoom: 18,
        mapTypeId: 'satellite',
        disableDefaultUI: false,
    });

    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: false,
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

    await loadRoomsAndDraw();

    google.maps.event.addListener(
        drawingManager,
        'overlaycomplete',
        (e: any) => {
            if (e.type !== google.maps.drawing.OverlayType.POLYGON) return;
            if (activePolygon) activePolygon.setMap(null);

            activePolygon = e.overlay as google.maps.Polygon;
            drawingManager?.setDrawingMode(null);
            openRoomPrompt();
        },
    );
});
</script>

<template>
    <AppLayout>
        <div class="flex h-screen flex-col bg-gray-100 font-sans text-gray-700">
            <div
                class="flex items-center gap-4 border-b bg-white p-2 shadow-sm"
            >
                <button
                    @click="enableDrawPolygon"
                    class="flex flex-col items-center rounded px-3 py-1 hover:bg-gray-100"
                >
                    <span class="text-xs">Draw Polygon</span>
                </button>

                <button
                    @click="enableMoveMap"
                    class="flex flex-col items-center rounded px-3 py-1 hover:bg-gray-100"
                >
                    <span class="text-xs">Move Map</span>
                </button>

                <button
                    @click="saveRoomWithPolygon"
                    class="rounded border border-blue-600 px-4 py-1 text-xs font-semibold text-blue-600"
                >
                    Save All
                </button>
            </div>

            <div class="flex flex-1 overflow-hidden">
                <div class="relative flex-1 overflow-hidden bg-white p-8">
                    <div
                        class="pointer-events-none absolute inset-0 opacity-10"
                        style="
                            background-image: radial-gradient(
                                #000 1px,
                                transparent 1px
                            );
                            background-size: 20px 20px;
                        "
                    ></div>

                    <div
                        class="relative h-full w-full overflow-hidden rounded-lg border-2 border-dashed border-gray-200"
                    >
                        <div ref="mapEl" class="h-full w-full"></div>
                    </div>
                </div>

                <div
                    class="flex w-80 flex-col gap-4 overflow-y-auto border-l bg-gray-50 p-4"
                >

                 <div class="flex-1 rounded-xl border bg-white p-4 shadow-sm">
    <div class="space-y-3">
        <div
            v-for="room in selectedArea.rooms"
            :key="room.id"
            class="flex items-center justify-between rounded-lg border border-transparent p-2 hover:border-gray-200 hover:bg-gray-50"
        >
            <div class="flex items-center gap-3">
                <div
                    :class="`flex h-8 w-8 items-center justify-center rounded-full text-white bg-${room.color}-500 text-xs`"
                >
                    {{ room.name[0] }}
                </div>
                <div>
                    <p class="text-sm leading-tight font-bold">
                        {{ room.name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <button
                    @click="editRoom(room)"
                    class="rounded p-1.5 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                    title="Edit Room"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </button>

                <button
                    @click="deleteRoom(room.id)"
                    class="rounded p-1.5 text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"
                    title="Delete Room"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                </button>
            </div>
        </div>
    </div>

    <button
        class="mt-6 w-full rounded-lg border-2 border-dashed border-blue-300 py-2 text-sm font-bold text-blue-500 hover:bg-blue-50"
    >
        + Add New Room
    </button>
</div>
                </div>
            </div>
        </div>

        <Transition name="fadeScale">
            <div
                v-if="showRoomPrompt"
                class="fixed inset-0 z-50 flex items-center justify-center"
            >
                <div
                    class="absolute inset-0 bg-black/40"
                    @click="cancelRoomPrompt(true)"
                ></div>

                <div
                    class="relative w-[420px] max-w-[90%] rounded-2xl border bg-white p-5 shadow-xl"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold">New Room</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Enter room name then click save.
                            </p>
                        </div>

                        <button
                            class="text-gray-400 hover:text-gray-700"
                            @click="cancelRoomPrompt(true)"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-500"
                                >Room Name</label
                            >
                            <input
                                v-model="newRoom.name"
                                type="text"
                                class="mt-1 w-full rounded border p-2 text-sm"
                                placeholder="e.g. Room 101"
                                autofocus
                            />
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button
                            @click="saveRoomWithPolygon"
                            :disabled="isSaving"
                            class="flex-1 rounded bg-blue-500 py-2 text-sm font-bold text-white shadow-md disabled:opacity-60"
                        >
                            {{ isSaving ? 'Saving...' : 'Save' }}
                        </button>

                        <button
                            @click="cancelRoomPrompt(true)"
                            class="rounded bg-gray-100 px-4 py-2 text-sm font-semibold"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
.fadeScale-enter-active,
.fadeScale-leave-active {
    transition:
        opacity 180ms ease,
        transform 180ms ease;
}
.fadeScale-enter-from,
.fadeScale-leave-to {
    opacity: 0;
    transform: scale(0.96) translateY(6px);
}
.fadeScale-enter-to,
.fadeScale-leave-from {
    opacity: 1;
    transform: scale(1) translateY(0);
}
</style>

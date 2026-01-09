<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import { useRoomState } from '@/composables/useRoomState';
import { RoomManager } from '@/services/RoomManager';
import { MapInitializer } from '@/services/MapInitializer';

// State Management
const roomState = useRoomState();
const {
    selectedArea,
    hoveredRoomId,
    hoveredRoomName,
    isEditMode,
    editingRoomId,
    showRoomPrompt,
    isSaving,
    newRoom,
    addRoom,
    updateRoomInList,
    removeRoomFromList,
    setHoveredRoom,
    setEditMode,
    setNewRoom,
    resetNewRoom,
} = roomState;

// Service Instances
const mapInitializer = new MapInitializer();
let roomManager: RoomManager;
let polygonManager: any = null;

const mapEl = ref<HTMLDivElement | null>(null);


const editRoom = (room: any) => {
    setEditMode(room.id, true);
    setNewRoom(room.name, room.capacity, room.color);
    showRoomPrompt.value = true;
};

const updateRoom = async () => {
    if (!editingRoomId.value) return;
    if (!newRoom.value.name.trim()) {
        alert('Enter room name first.');
        return;
    }

    isSaving.value = true;
    try {
        const updatedRoom = await roomManager.updateRoom(editingRoomId.value, {
            name: newRoom.value.name,
            capacity: newRoom.value.capacity,
            color: newRoom.value.color,
        });

        updateRoomInList(editingRoomId.value, {
            name: updatedRoom.name,
            capacity: updatedRoom.capacity,
            color: updatedRoom.color,
        });

        showRoomPrompt.value = false;
        setEditMode(null, false);
    } catch (err) {
        console.error(err);
        alert('Failed to update room.');
    } finally {
        isSaving.value = false;
    }
};

const deleteRoom = async (roomId: number) => {
    const ok = confirm('Delete this room and its polygon?');
    if (!ok) return;

    try {
        await roomManager.deleteRoom(roomId);
        removeRoomFromList(roomId);

        if (editingRoomId.value === roomId) {
            showRoomPrompt.value = false;
            setEditMode(null, false);
        }
    } catch (err) {
        console.error(err);
        alert('Failed to delete room.');
    }
};

// ============ Polygon Management Functions ============

const enableDrawPolygon = () => {
    polygonManager?.enableDrawMode();
};

const enableMoveMap = () => {
    polygonManager?.disableDrawMode();
};

const clearPolygon = () => {
    polygonManager?.clearActivePolygon();
};

const openRoomPrompt = () => {
    setNewRoom('', 0, '#3b82f6');
    showRoomPrompt.value = true;
};

const cancelRoomPrompt = (removePolygon = true) => {
    showRoomPrompt.value = false;
    setEditMode(null, false);
    if (removePolygon) clearPolygon();
};

const saveRoomWithPolygon = async () => {
    if (isEditMode.value) {
        await updateRoom();
        return;
    }

    const polygon = polygonManager?.getActivePolygonPath();
    const validation = roomManager.validateRoomData(newRoom.value.name, polygon);

    if (!validation.valid) {
        alert(validation.error);
        return;
    }

    isSaving.value = true;
    try {
        const result = await roomManager.createRoom(
            {
                name: newRoom.value.name,
                capacity: newRoom.value.capacity,
                color: newRoom.value.color,
            },
            polygon,
        );

        addRoom(result.room);

        // Draw the new room on the map
        polygonManager?.drawRoomPolygon(result.apiRoom, handleRoomHover, handleRoomHoverEnd, handleRoomClick);

        showRoomPrompt.value = false;
        clearPolygon();
        resetNewRoom();
    } catch (err) {
        console.error(err);
        alert('Failed to save. Check API / validation.');
    } finally {
        isSaving.value = false;
    }
};

// ============ Event Handlers ============

const handleRoomHover = (roomId: number, roomName: string) => {
    setHoveredRoom(roomId, roomName);
};

const handleRoomHoverEnd = () => {
    setHoveredRoom(null, '');
};

const handleRoomClick = (room: any) => {
    const foundRoom = selectedArea.value.rooms.find((r) => r.id === room.room_id);
    if (foundRoom) editRoom(foundRoom);
};

// ============ Initialization ============

onMounted(async () => {
    try {
        // Initialize Map and Polygon Manager
        const { polygonManager: pm, googleRef } = await mapInitializer.initialize(
            mapEl.value!,
            selectedArea.value.polygonColor,
        );

        polygonManager = pm;
        roomManager = new RoomManager(polygonManager);

        // Load rooms from API and draw them
        await roomManager.loadAndDrawRooms(
            (rooms) => {
                selectedArea.value.rooms = rooms;
            },
            (room) => {
                polygonManager.drawRoomPolygon(
                    room,
                    handleRoomHover,
                    handleRoomHoverEnd,
                    handleRoomClick,
                );
            },
        );

        // Listen for polygon drawing completion
        googleRef.maps.event.addListener(
            mapInitializer.getPolygonManager()?.['drawingManager'],
            'overlaycomplete',
            (e: any) => {
                if (e.type !== googleRef.maps.drawing.OverlayType.POLYGON) return;
                polygonManager?.setActivePolygon(e.overlay);
                polygonManager?.disableDrawMode();
                openRoomPrompt();
            },
        );
    } catch (err) {
        console.error('Failed to initialize map:', err);
    }
});
</script>

<template>
    <AppLayout>
        <div class="flex h-screen flex-col bg-gradient-to-br from-gray-50 to-gray-100 font-sans text-gray-700">
            <!-- Modern Toolbar -->
            <div class="flex items-center justify-between gap-4 border-b bg-white/80 backdrop-blur-lg p-4 shadow-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Room Mapper</h1>
                        <p class="text-xs text-gray-500">Interactive floor planning</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="enableDrawPolygon"
                        class="group relative flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5 text-white shadow-lg shadow-blue-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/40 hover:scale-105"
                    >
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        <span class="text-sm font-semibold">Draw Room</span>
                    </button>

                    <button
                        @click="enableMoveMap"
                        class="group relative flex items-center gap-2 rounded-xl bg-white border-2 border-gray-200 px-4 py-2.5 text-gray-700 transition-all duration-300 hover:border-indigo-300 hover:bg-indigo-50 hover:scale-105"
                    >
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                        </svg>
                        <span class="text-sm font-semibold">Move Map</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-1 overflow-hidden">
                <!-- Map Container -->
                <div class="relative flex-1 overflow-hidden p-6">
                    <div class="h-full w-full overflow-hidden rounded-2xl shadow-2xl border-2 border-white/50 backdrop-blur-sm">
                        <div ref="mapEl" class="h-full w-full"></div>
                        
                        <!-- Hover Indicator -->
                        <Transition name="slideIn">
                            <div v-if="hoveredRoomName" class="absolute top-4 left-4 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200 px-6 py-4 flex items-center gap-3 animate-bounce-subtle">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Currently Hovering</p>
                                    <p class="text-lg font-bold text-gray-900">{{ hoveredRoomName }}</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-96 overflow-hidden border-l bg-white/50 backdrop-blur-sm">
                    <div class="h-full overflow-y-auto p-6">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Room List</h2>
                            <p class="text-sm text-gray-500">Manage and organize your rooms</p>
                        </div>

                        <div class="space-y-3">
                            <TransitionGroup name="list">
                                <div
                                    v-for="room in selectedArea.rooms"
                                    :key="room.id"
                                    :class="[
                                        'group relative rounded-2xl border-2 p-4 transition-all duration-300 cursor-pointer overflow-hidden',
                                        hoveredRoomId === room.id 
                                            ? 'border-blue-500 bg-blue-50 shadow-lg shadow-blue-500/20 scale-105' 
                                            : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md'
                                    ]"
                                >
                                    <!-- Gradient Background -->
                                    <div 
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                        :style="{ background: `linear-gradient(135deg, ${room.color}15, ${room.color}05)` }"
                                    ></div>

                                    <div class="relative flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div
                                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl text-white shadow-lg font-bold text-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                                                :style="{ background: `linear-gradient(135deg, ${room.color}, ${room.color}dd)` }"
                                            >
                                                {{ room.name[0] }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-base font-bold text-gray-900 truncate">
                                                    {{ room.name }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    Capacity: {{ room.capacity }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1">
                                            <button
                                                @click.stop="editRoom(room)"
                                                class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-blue-100 hover:text-blue-600 hover:scale-110"
                                                title="Edit Room"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>

                                            <button
                                                @click.stop="deleteRoom(room.id)"
                                                class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-red-100 hover:text-red-600 hover:scale-110"
                                                title="Delete Room"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </TransitionGroup>
                        </div>

                        <button
                            @click="enableDrawPolygon"
                            class="mt-6 w-full group rounded-2xl border-3 border-dashed border-blue-300 bg-blue-50/50 py-4 text-sm font-bold text-blue-600 transition-all duration-300 hover:bg-blue-100 hover:border-blue-500 hover:shadow-lg hover:scale-105"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add New Room</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Transition name="fadeScale">
            <div
                v-if="showRoomPrompt"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @click="cancelRoomPrompt(true)"
                ></div>

                <div class="relative w-full max-w-md rounded-3xl border-2 border-white/50 bg-white p-8 shadow-2xl">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ isEditMode ? 'Edit Room' : 'New Room' }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ isEditMode ? 'Update room information' : 'Enter room details to save' }}
                            </p>
                        </div>

                        <button
                            class="rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700"
                            @click="cancelRoomPrompt(true)"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Room Name
                            </label>
                            <input
                                v-model="newRoom.name"
                                type="text"
                                class="w-full rounded-xl border-2 border-gray-200 p-3 text-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                placeholder="e.g. Room 101"
                                autofocus
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Capacity
                            </label>
                            <input
                                v-model="newRoom.capacity"
                                type="number"
                                class="w-full rounded-xl border-2 border-gray-200 p-3 text-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                placeholder="e.g. 30"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Room Color
                            </label>
                            <div class="flex gap-2">
                                <input
                                    v-model="newRoom.color"
                                    type="color"
                                    class="h-12 w-20 rounded-xl border-2 border-gray-200 cursor-pointer"
                                />
                                <input
                                    v-model="newRoom.color"
                                    type="text"
                                    class="flex-1 rounded-xl border-2 border-gray-200 p-3 text-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    placeholder="#3b82f6"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button
                            @click="saveRoomWithPolygon"
                            :disabled="isSaving"
                            class="flex-1 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/40 disabled:opacity-60 disabled:cursor-not-allowed hover:scale-105"
                        >
                            {{ isSaving ? 'Saving...' : (isEditMode ? 'Update' : 'Save') }}
                        </button>

                        <button
                            @click="cancelRoomPrompt(true)"
                            class="rounded-xl bg-gray-100 px-6 py-3 text-sm font-semibold text-gray-700 transition-all duration-300 hover:bg-gray-200 hover:scale-105"
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
/* Fade Scale Animation */
.fadeScale-enter-active,
.fadeScale-leave-active {
    transition: opacity 300ms ease, transform 300ms ease;
}
.fadeScale-enter-from,
.fadeScale-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
}

/* Slide In Animation */
.slideIn-enter-active,
.slideIn-leave-active {
    transition: all 300ms ease;
}
.slideIn-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}
.slideIn-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

/* List Animation */
.list-enter-active,
.list-leave-active {
    transition: all 300ms ease;
}
.list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.list-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
.list-move {
    transition: transform 300ms ease;
}

/* Subtle Bounce */
@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-4px);
    }
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #3b82f6, #6366f1);
    border-radius: 20px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #2563eb, #4f46e5);
}
</style>
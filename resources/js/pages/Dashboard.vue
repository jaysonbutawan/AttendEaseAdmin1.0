<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';
import {
    Save, // For "Save Changes" button
    Pencil, // For "Edit" tool and "Save" in modal
    Trash2, // For "Delete" tool
    ZoomIn, // For "Zoom In"
    ZoomOut, // For "Zoom Out"
    X, // For "Cancel" in modal
    Grid, // For "New Room" tool (more appropriate than generic Plus/Pencil/Trash2)
    Move, // Potentially for dragging/moving rooms (visual hint)
    Check, // For confirming input in modal
} from 'lucide-vue-next';

// --- State Management ---

// To control the visibility of the room naming modal
const isRoomNamingModalOpen = ref(false);
// To store the current room name being edited/created
const currentRoomName = ref('');
// To simulate a currently active/selected room for naming
const activeRoomId = ref<string | null>(null);

// Mock data for rooms on the map (initially empty or pre-filled)
interface Room {
    id: string;
    name: string;
    x: number; // x position as a percentage or pixel value
    y: number; // y position as a percentage or pixel value
    width: number;
    height: number;
    color: 'blue' | 'green';
}

const rooms = ref<Room[]>([
    { id: 'room-1', name: 'Library Section A', x: 25, y: 30, width: 40, height: 30, color: 'blue' },
    { id: 'room-2', name: 'Server Room', x: 60, y: 15, width: 20, height: 20, color: 'green' },
]);

// --- Methods ---

const openRoomNamingModal = (roomId: string | null = null, existingName: string = '') => {
    activeRoomId.value = roomId;
    currentRoomName.value = existingName;
    isRoomNamingModalOpen.value = true;
};

const saveRoomName = () => {
    if (currentRoomName.value.trim() === '') {
        alert('Room name cannot be empty!');
        return;
    }

    if (activeRoomId.value) {
        // Edit existing room
        const room = rooms.value.find(r => r.id === activeRoomId.value);
        if (room) {
            room.name = currentRoomName.value.trim();
        }
    } else {
        // Create new room (for demonstration, we'll just add a new mock room)
        const newRoom: Room = {
            id: `room-${Date.now()}`,
            name: currentRoomName.value.trim(),
            x: Math.random() * 50 + 10, // Random position for new room
            y: Math.random() * 50 + 10,
            width: 30,
            height: 20,
            color: Math.random() > 0.5 ? 'blue' : 'green', // Random color
        };
        rooms.value.push(newRoom);
    }

    closeRoomNamingModal();
};

const closeRoomNamingModal = () => {
    isRoomNamingModalOpen.value = false;
    currentRoomName.value = '';
    activeRoomId.value = null;
};

const handleSaveChanges = () => {
    // In a real application, this would send the 'rooms' data to a backend
    console.log('Saving all changes:', rooms.value);
    alert('Changes saved successfully!');
};

const handleNewRoomClick = () => {
    // For now, this just opens the naming modal for a new room
    openRoomNamingModal(null, '');
};

const handleEditRoomClick = (roomId: string, currentName: string) => {
    openRoomNamingModal(roomId, currentName);
};

const handleDeleteRoom = (roomId: string) => {
    if (confirm('Are you sure you want to delete this room?')) {
        rooms.value = rooms.value.filter(room => room.id !== roomId);
    }
};

// Placeholder for other tool actions
const handleToolClick = (tool: string) => {
    alert(`Tool clicked: ${tool}`);
};
</script>

<template>
    <AppLayout>
    <div class="min-h-screen bg-gray-100 font-sans flex flex-col">
        <header class="flex items-center justify-between p-4 bg-white shadow-sm z-10">
            <h1 class="text-xl font-bold text-gray-800">Room Map Management</h1>
            <button
                @click="handleSaveChanges"
                class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200"
            >
                <Save class="w-4 h-4 mr-2" />
                Save Changes
            </button>
        </header>

        <main class="flex-grow p-6 relative overflow-hidden">
            <div
                class="relative w-full h-full bg-white rounded-lg shadow-xl overflow-hidden"
                style="background-image: linear-gradient(to right, #f5f5f5 1px, transparent 1px), linear-gradient(to bottom, #f5f5f5 1px, transparent 1px); background-size: 20px 20px;"
            >
                <div
                    v-for="room in rooms"
                    :key="room.id"
                    @click="handleEditRoomClick(room.id, room.name)"
                    :style="{
                        position: 'absolute',
                        left: `${room.x}%`,
                        top: `${room.y}%`,
                        width: `${room.width}%`,
                        height: `${room.height}%`,
                    }"
                    :class="{
                        'bg-blue-200/50 border-blue-400': room.color === 'blue',
                        'bg-green-200/50 border-green-400': room.color === 'green',
                        'border-2 border-dashed rounded-lg flex items-center justify-center text-sm font-semibold text-gray-700 cursor-pointer hover:shadow-lg transition-shadow duration-200': true
                    }"
                    :title="room.name"
                >
                    {{ room.name }}
                    <button
                        @click.stop="handleDeleteRoom(room.id)"
                        class="absolute top-1 right-1 p-1 rounded-full bg-red-500/80 text-white hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100"
                        title="Delete Room"
                    >
                        <X class="w-3 h-3" />
                    </button>
                </div>


                <Transition name="fade">
                    <div
                        v-if="isRoomNamingModalOpen"
                        class="absolute inset-0 bg-gray-800 bg-opacity-40 flex items-center justify-center z-20"
                    >
                        <div class="bg-white p-6 rounded-lg shadow-2xl w-full max-w-sm">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Name this room</h3>
                            <div class="relative flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition duration-200">
                                <input
                                    type="text"
                                    v-model="currentRoomName"
                                    placeholder="e.g., Library Section A"
                                    class="flex-grow py-2 px-3 text-gray-800 outline-none border-none"
                                    @keyup.enter="saveRoomName"
                                />
                                <button
                                    @click="saveRoomName"
                                    class="p-2 bg-blue-600 text-white hover:bg-blue-700 transition duration-200"
                                    title="Save Name"
                                >
                                    <Check class="w-5 h-5" />
                                </button>
                                <button
                                    @click="closeRoomNamingModal"
                                    class="p-2 bg-gray-200 text-gray-700 hover:bg-gray-300 transition duration-200"
                                    title="Cancel"
                                >
                                    <X class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>


                <div class="absolute bottom-6 right-6 flex items-center space-x-2 bg-white p-3 rounded-xl shadow-lg border border-gray-200 z-10">
                    <button @click="handleNewRoomClick" class="tool-button group" title="Add New Room">
                        <Grid class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition-colors duration-200" />
                    </button>
                    <button @click="handleToolClick('Delete')" class="tool-button group" title="Delete Selected">
                        <Trash2 class="w-5 h-5 text-gray-600 group-hover:text-red-600 transition-colors duration-200" />
                    </button>
                    <div class="w-px h-6 bg-gray-200 mx-1"></div> <button @click="handleToolClick('Zoom In')" class="tool-button group" title="Zoom In">
                        <ZoomIn class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors duration-200" />
                    </button>
                    <button @click="handleToolClick('Zoom Out')" class="tool-button group" title="Zoom Out">
                        <ZoomOut class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors duration-200" />
                    </button>
                    <button @click="handleToolClick('Move')" class="tool-button group" title="Move Map">
                        <Move class="w-5 h-5 text-gray-600 group-hover:text-gray-900 transition-colors duration-200" />
                    </button>
                </div>
            </div>
        </main>
    </div>
    </AppLayout>
</template>
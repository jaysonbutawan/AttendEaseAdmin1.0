<script setup lang="ts">
import type { Room } from '@/composables/useRoomState';
import RoomCard from './RoomCard.vue';

defineProps<{
    rooms: Room[];
    hoveredRoomId: number | null;
}>();

const emit = defineEmits<{
    (e: 'edit', room: Room): void;
    (e: 'delete', roomId: number): void;
    (e: 'addNew'): void;
}>();

const handleEdit = (room: Room) => {
    emit('edit', room);
};

const handleDelete = (roomId: number) => {
    emit('delete', roomId);
};

const handleAddNew = () => {
    emit('addNew');
};
</script>

<template>
    <div class="w-96 overflow-hidden border-l bg-white/50 backdrop-blur-sm">
        <div class="h-full overflow-y-auto p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Room List</h2>
                <p class="text-sm text-gray-500">Manage and organize your rooms</p>
            </div>

            <div class="space-y-3">
                <TransitionGroup name="list">
                    <RoomCard
                        v-for="room in rooms"
                        :key="room.id"
                        :room="room"
                        :is-hovered="hoveredRoomId === room.id"
                        @edit="handleEdit"
                        @delete="handleDelete"
                    />
                </TransitionGroup>
            </div>

            <button
                @click="handleAddNew"
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
</template>

<style scoped>
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

<script setup lang="ts">
import type { Room } from '@/composables/useRoomState';

defineProps<{
    room: Room;
    isHovered: boolean;
}>();

const emit = defineEmits<{
    (e: 'edit', room: Room): void;
    (e: 'delete', roomId: number): void;
}>();

const handleEdit = (room: Room) => {
    emit('edit', room);
};

const handleDelete = (roomId: number) => {
    emit('delete', roomId);
};
</script>

<template>
    <div
        :class="[
            'group relative rounded-2xl border-2 p-4 transition-all duration-300 cursor-pointer overflow-hidden',
            isHovered
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
                    @click.stop="handleEdit(room)"
                    class="rounded-xl p-2 text-gray-400 transition-all duration-200 hover:bg-blue-100 hover:text-blue-600 hover:scale-110"
                    title="Edit Room"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>

                <button
                    @click.stop="handleDelete(room.id)"
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
</template>

<style scoped>
/* Component-specific styles if needed */
</style>

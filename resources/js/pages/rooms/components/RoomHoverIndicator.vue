<script setup lang="ts">
defineProps<{
    roomName: string;
}>();
</script>

<template>
    <Transition name="slideIn">
        <div v-if="roomName" class="absolute top-4 left-1/2 -translate-x-1/2 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200 px-6 py-4 flex items-center gap-3 animate-bounce-subtle">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Currently Hovering</p>
                <p class="text-lg font-bold text-gray-900">{{ roomName }}</p>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
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
</style>

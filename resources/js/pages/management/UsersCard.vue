<script setup lang="ts">
import { computed } from 'vue';

interface User {
  id: string | number;
  name: string;
  email: string;
  role: 'admin' | 'teacher' | 'student';
  assigned_to?: string | number | null;
  last_activity?: string;
  initials: string;
  avatar_color: string;
  contact_number?: string;
  status?: string;
}

const props = defineProps<{ 
  user: User; 
  isSelected?: boolean; 
}>();

const emit = defineEmits<{
  (e: 'toggle-selection', userId: string | number): void;
  (e: 'edit', userId: string | number): void;
  (e: 'view', userId: string | number): void;
  (e: 'delete', userId: string | number): void;
}>();

const roleBadgeClass = computed(() => {
  switch (props.user.role) {
    case 'admin':
      return 'bg-purple-100 text-purple-800';
    case 'teacher':
      return 'bg-blue-100 text-blue-800';
    case 'student':
      return 'bg-green-100 text-green-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
});

const formattedLastActivity = computed(() => {
  const lastActivity = props.user.last_activity;
  if (!lastActivity) return 'Never';
  const date = new Date(lastActivity);
  const now = new Date();
  const diff = now.getTime() - date.getTime();
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);

  if (minutes < 1) return 'Just now';
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days === 1) return 'Yesterday';
  return date.toLocaleDateString();
});
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div 
          class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold"
          :style="{ background: user.avatar_color }"
        >
          {{ user.initials }}
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
          <p class="text-xs text-gray-500">ID: #{{ user.id }}</p>
        </div>
      </div>
      <div>
        <input 
          type="checkbox" 
          :checked="isSelected"
          @change="emit('toggle-selection', user.id)"
          class="rounded border-gray-300"
        >
      </div>
    </div>

    <div class="mt-4 space-y-2">
      <div class="flex justify-between items-center">
        <span class="text-sm text-gray-700">{{ user.email }}</span>
        <span 
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
          :class="roleBadgeClass"
        >
          {{ user.role }}
        </span>
      </div>
      <div class="text-xs text-gray-600">
        <span class="font-medium">Assigned:</span>
        <span>{{ user.assigned_to ?? '—' }}</span>
      </div>
      <div class="text-xs text-gray-600">
        <span class="font-medium">Last Activity:</span>
        <span>{{ formattedLastActivity }}</span>
      </div>
    </div>

    <div class="mt-4 flex gap-2">
      <button 
        @click="emit('edit', user.id)"
        class="px-2.5 py-1.5 text-blue-600 hover:bg-blue-50 rounded transition text-xs"
        title="Edit user"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
      </button>
      <button 
        @click="emit('view', user.id)"
        class="px-2.5 py-1.5 text-amber-600 hover:bg-amber-50 rounded transition text-xs"
        title="View details"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
        </svg>
      </button>
      <button 
        @click="emit('delete', user.id)"
        class="px-2.5 py-1.5 text-red-600 hover:bg-red-50 rounded transition text-xs"
        title="Delete user"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
        </svg>
      </button>
    </div>
  </div>
</template>

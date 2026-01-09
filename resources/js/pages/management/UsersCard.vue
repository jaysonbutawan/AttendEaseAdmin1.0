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
  approval_status?: 'pending' | 'approved' | 'rejected';
  approved_at?: string;
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
  (e: 'approve', userId: string | number): void;
  (e: 'reject', userId: string | number): void;
}>();

const roleBadgeClass = computed(() => {
  switch (props.user.role) {
    case 'admin':
      return 'bg-gradient-to-r from-purple-500 to-pink-500 text-white';
    case 'teacher':
      return 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white';
    case 'student':
      return 'bg-gradient-to-r from-green-500 to-emerald-500 text-white';
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

const statusBadgeClass = computed(() => {
  switch (props.user.approval_status) {
    case 'approved':
      return 'bg-green-500/10 text-green-600 border-green-200';
    case 'rejected':
      return 'bg-red-500/10 text-red-600 border-red-200';
    case 'pending':
    default:
      return 'bg-amber-500/10 text-amber-600 border-amber-200';
  }
});

const approvedDateText = computed(() => {
  if (!props.user.approved_at) return '—';
  try {
    const d = new Date(props.user.approved_at);
    return d.toLocaleDateString();
  } catch {
    return String(props.user.approved_at);
  }
});

const isOnline = computed(() => {
  if (!props.user.last_activity) return false;
  const diff = new Date().getTime() - new Date(props.user.last_activity).getTime();
  return diff < 5 * 60 * 1000; // 5 minutes
});
</script>

<template>
  <div 
    class="group relative bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] hover:-translate-y-1"
    :class="isSelected ? 'ring-2 ring-blue-500 shadow-lg' : ''"
  >
    <!-- Gradient Background Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-purple-50/30 to-pink-50/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    
    <!-- Animated Border Gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="padding: 1px; -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude;"></div>

    <div class="relative p-6">
      <!-- Header Section -->
      <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-4 flex-1">
          <!-- Avatar with Status -->
          <div class="relative">
            <div 
              class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-lg font-bold shadow-lg transform transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
              :style="{ background: `linear-gradient(135deg, ${user.avatar_color}, ${user.avatar_color}dd)` }"
            >
              {{ user.initials }}
            </div>
            <!-- Online Status Indicator -->
            <div 
              v-if="isOnline"
              class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full animate-pulse"
            ></div>
          </div>
          
          <!-- Name and ID -->
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
              {{ user.name }}
            </h3>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                #{{ user.id }}
              </span>
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold shadow-sm transition-transform duration-300 hover:scale-105"
                :class="roleBadgeClass"
              >
                {{ user.role }}
              </span>
            </div>
          </div>
        </div>

        <!-- Checkbox with Animation -->
        <div class="relative">
          <input 
            type="checkbox" 
            :checked="isSelected"
            @change="emit('toggle-selection', user.id)"
            class="w-5 h-5 rounded-lg border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer hover:border-blue-400"
          >
        </div>
      </div>

      <!-- Email Section -->
      <div class="mb-4 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-2 text-sm text-gray-600 group/email">
          <svg class="w-4 h-4 text-gray-400 group-hover/email:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
          <span class="truncate hover:text-blue-600 transition-colors cursor-pointer">{{ user.email }}</span>
        </div>
      </div>

      <!-- Info Grid -->
      <div class="grid grid-cols-2 gap-3 mb-4">
        <!-- Approval Status -->
        <div class="bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition-colors">
          <p class="text-xs text-gray-500 mb-1 font-medium">Approval Status</p>
          <span 
            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold border transition-transform duration-200 hover:scale-105"
            :class="statusBadgeClass"
          >
            <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
              :class="{
                'bg-green-600 animate-pulse': user.approval_status === 'approved',
                'bg-red-600': user.approval_status === 'rejected',
                'bg-amber-600 animate-pulse': user.approval_status === 'pending'
              }"
            ></span>
            {{ user.approval_status ?? 'pending' }}
          </span>
        </div>

        <!-- Approved Date -->
        <div class="bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition-colors">
          <p class="text-xs text-gray-500 mb-1 font-medium">Approved Date</p>
          <p class="text-sm font-semibold text-gray-900">{{ approvedDateText }}</p>
        </div>

        <!-- Assigned To -->
        <div class="bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition-colors">
          <p class="text-xs text-gray-500 mb-1 font-medium">Assigned To</p>
          <p class="text-sm font-semibold text-gray-900">{{ user.assigned_to ?? '—' }}</p>
        </div>

        <!-- Last Activity -->
        <div class="bg-gray-50 rounded-xl p-3 hover:bg-gray-100 transition-colors">
          <p class="text-xs text-gray-500 mb-1 font-medium">Last Activity</p>
          <p class="text-sm font-semibold" :class="isOnline ? 'text-green-600' : 'text-gray-900'">
            {{ formattedLastActivity }}
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2 pt-4 border-t border-gray-100">
        <button 
          @click="emit('edit', user.id)"
          class="flex-1 px-3 py-2.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all duration-200 text-xs font-semibold flex items-center justify-center gap-2 hover:shadow-lg hover:scale-105 group/btn"
          title="Edit user"
        >
          <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          <span>Edit</span>
        </button>
        
        <button 
          v-if="user.role !== 'admin'"
          @click="emit('approve', user.id)"
          class="px-3 py-2.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition-all duration-200 text-xs font-semibold hover:shadow-lg hover:scale-105 group/btn"
          title="Approve user"
        >
          <svg class="w-4 h-4 group-hover/btn:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>
        
        <button 
          v-if="user.role !== 'admin'"
          @click="emit('reject', user.id)"
          class="px-3 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all duration-200 text-xs font-semibold hover:shadow-lg hover:scale-105 group/btn"
          title="Reject user"
        >
          <svg class="w-4 h-4 group-hover/btn:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        
        <button 
          @click="emit('view', user.id)"
          class="px-3 py-2.5 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-xl transition-all duration-200 text-xs font-semibold hover:shadow-lg hover:scale-105 group/btn"
          title="View details"
        >
          <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
        </button>
        
        <button 
          @click="emit('delete', user.id)"
          class="px-3 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all duration-200 text-xs font-semibold hover:shadow-lg hover:scale-105 group/btn"
          title="Delete user"
        >
          <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
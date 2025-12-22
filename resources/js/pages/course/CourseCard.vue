<script setup lang="ts" generic="T extends { id: number; name: string; code: string; status: string; teacher: string; students: number; subjects: number; gradient: string }">
interface Props {
  course: T;
  onEdit?: () => void;
  onViewDetails?: () => void;
}

defineProps<Props>();
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
    <div :class="`h-2 bg-gradient-to-r ${course.gradient}`"></div>
    <div class="p-6">
      <!-- Course Header -->
      <div class="flex items-start justify-between mb-4">
        <div>
          <h3 class="text-lg font-bold text-gray-900">{{ course.name }}</h3>
          <p class="text-xs text-gray-500 font-mono mt-1">{{ course.code }}</p>
        </div>
        <span
          v-if="course.status"
          :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            course.status === 'Active'
              ? 'bg-green-100 text-green-800'
              : 'bg-yellow-100 text-yellow-800',
          ]"
        >
          {{ course.status }}
        </span>
      </div>

      <!-- Course Info -->
      <div class="space-y-3 mb-4">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"
            ></path>
          </svg>
          <p class="text-sm text-gray-600">{{ course.teacher }}</p>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 20h5v-2a3 3 0 0 0-5.856-1.487M15 6h3a1 1 0 0 1 1 1v3h-4V7a1 1 0 0 1 0-1z"
            ></path>
          </svg>
          <p class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900">{{ course.students }}</span> Students Enrolled
          </p>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"
            ></path>
          </svg>
          <p class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900">{{ course.subjects }}</span> Subjects
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-2 pt-4 border-t border-gray-200">
        <button
          @click="onEdit?.()"
          class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
        >
          Edit
        </button>
        <button
          @click="onViewDetails?.()"
          class="flex-1 px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm font-medium"
        >
          View Details
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped></style>

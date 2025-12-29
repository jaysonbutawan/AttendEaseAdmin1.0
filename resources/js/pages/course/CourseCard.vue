<script setup lang="ts">
import { Clock, GraduationCap, MapPin, Pencil, ArrowRight } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import axios from 'axios'

type ClassSession = {
  session_id: number
  subject_name: string
  teacher_name: string
  room_name: string
  session_days: string[]
  start_time: string
  end_time: string
  session_status: string
}

const sessions = ref<ClassSession[]>([])

onMounted(async () => {
  const { data } = await axios.get('/class_sessions')
  sessions.value = data.sessions
})

const formatDays = (days: string[]) => {
  return days.join(', ')
}

const formatTime = (startTime: string, endTime: string) => {
  return `${startTime} - ${endTime}`
}
</script>

<template>
  <div class="flex flex-col items-center justify-center bg-transparent gap-6">
    <div 
      v-for="session in sessions" 
      :key="session.session_id"
      class="bg-white rounded-[2rem] shadow-xl shadow-blue-100/50 w-full max-w-2xl p-8 md:p-10 relative overflow-hidden"
    >
      
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2 bg-indigo-50 px-4 py-1.5 rounded-full">
          <div class="w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
          <span class="text-indigo-600 text-xs font-bold tracking-wider uppercase">
            {{ session.session_status }}
          </span>
        </div>

        <span class="text-gray-400 text-sm font-medium">
          {{ formatDays(session.session_days) }}
        </span>
      </div>

      <div class="mb-8">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">
          {{ session.subject_name }}
        </h1>
        <div class="flex items-center gap-2 text-gray-400 font-semibold">
          <Clock class="w-5 h-5" />
          <span>{{ formatTime(session.start_time, session.end_time) }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-3xl">
          <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
            <GraduationCap class="w-6 h-6" />
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Teacher</p>
            <p class="text-slate-900 font-bold text-lg">{{ session.teacher_name }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-3xl">
          <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
            <MapPin class="w-6 h-6" />
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Location</p>
            <p class="text-slate-900 font-bold text-lg">{{ session.room_name }}</p>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-6">
        <div class="flex items-center gap-3 w-full md:w-auto">
          <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3.5 border border-gray-100 rounded-full font-bold text-slate-800 hover:bg-gray-50 transition-colors">
            <Pencil class="w-4 h-4" />
            Edit
          </button>
          <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-8 py-3.5 bg-indigo-600 text-white rounded-full font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
            View Details
            <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
    
    <!-- Show message if no sessions -->
    <div v-if="sessions.length === 0" class="text-gray-400 text-center py-8">
      No class sessions available
    </div>
  </div>
</template>

<style scoped>
button {
  -webkit-tap-highlight-color: transparent;
}
</style>
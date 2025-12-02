<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';


interface Teacher {
    name: string;
    subject: string;
    daysAgo: number;
    isSelected: boolean;
}

interface Student {
    name: string;
    type: string; 
    detail: string; 
    isSelected: boolean;
}

const teacherList = ref<Teacher[]>([
    { name: 'Dr. Helena Vance', subject: 'Physics', daysAgo: 2, isSelected: true },
    { name: 'Mr. Kevin Brooks', subject: 'History', daysAgo: 1, isSelected: true },
    { name: 'Ms. Sarah Lee', subject: 'Math', daysAgo: 4, isSelected: true },
]);

const studentList = ref<Student[]>([
    { name: 'Lia Torres', type: 'Grade 10', detail: 'New Transfer', isSelected: true },
    { name: 'Jacob Chen', type: 'Grade 7', detail: 'Re-enrollment', isSelected: false },
    { name: 'Olivia R.', type: 'Grade 11', detail: 'New Transfer', isSelected: false },
]);


const areAllTeachersSelected = computed(() => teacherList.value.every(t => t.isSelected));
const areAllStudentsSelected = computed(() => studentList.value.every(s => s.isSelected));

const selectedTeachersCount = computed(() => teacherList.value.filter(t => t.isSelected).length);
const selectedStudentsCount = computed(() => studentList.value.filter(s => s.isSelected).length);

const toggleAllTeachers = (event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    teacherList.value.forEach(t => t.isSelected = checked);
};

const toggleAllStudents = (event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    studentList.value.forEach(s => s.isSelected = checked);
};

</script>

<template>
<AppLayout>
  <!-- Main content wrapper to mimic the off-white background and padding of the design -->
  <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    
    <!-- Title -->
    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-8">
      User Management Overview
    </h1>

    <!-- 1. Stats Cards Section -->
    <!-- Responsive grid for the 4 stats cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
      
      <!-- Stat Card 1: Active Teachers -->
      <div class="flex flex-col p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="flex items-start justify-between mb-4">
          <span class="text-gray-500 font-semibold text-sm">Active Teachers</span>
          <!-- Icon Container (Blue/Purple Accent) -->
          <div class="p-2 bg-indigo-100 rounded-full text-indigo-600 flex-shrink-0">
            <!-- Icon: Person (Using a simple path for vector clarity) -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <span class="text-3xl sm:text-4xl font-bold text-indigo-600 mr-2">85</span>
        </div>
      </div>

      <!-- Stat Card 2: Total Students -->
      <div class="flex flex-col p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="flex items-start justify-between mb-4">
          <span class="text-gray-500 font-semibold text-sm">Total Students</span>
          <!-- Icon Container (Green Accent) -->
          <div class="p-2 bg-green-100 rounded-full text-green-600 flex-shrink-0">
            <!-- Icon: People -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <span class="text-3xl sm:text-4xl font-bold text-green-600 mr-2">1,450</span>
        </div>
      </div>

      <!-- Stat Card 3: Pending Approvals -->
      <div class="flex flex-col p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="flex items-start justify-between mb-4">
          <span class="text-gray-500 font-semibold text-sm">Pending Approvals</span>
          <!-- Icon Container (Red/Clock Accent) -->
          <div class="p-2 bg-red-100 rounded-full text-red-600 flex-shrink-0">
            <!-- Icon: Clock -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <span class="text-3xl sm:text-4xl font-bold text-red-600 mr-2">15</span>
        </div>
      </div>

      <!-- Stat Card 4: Classes in Session -->
      <div class="flex flex-col p-4 sm:p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="flex items-start justify-between mb-4">
          <span class="text-gray-500 font-semibold text-sm">Classes in Session</span>
          <!-- Icon Container (Teal/School Accent) -->
          <div class="p-2 bg-teal-100 rounded-full text-teal-600 flex-shrink-0">
            <!-- Icon: Building/School -->
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><path d="M12 22v-4"/><path d="M9 18h6"/></svg>
          </div>
        </div>
        <div class="flex items-baseline">
          <span class="text-3xl sm:text-4xl font-bold text-teal-600 mr-2">42</span>
        </div>
      </div>
    </div>

    <!-- 2. Action Required Section -->
    <h2 class="text-xl font-bold text-gray-800 mb-6">
      Action Required: Pending Approvals (15 Total)
    </h2>

    <!-- Action Cards Container -->
    <!-- Responsive grid for the two action lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Action Card 1: New Teacher Registrations -->
      <div class="flex flex-col p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        
        <!-- Card Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 pb-4 border-b border-gray-100">
          <h3 class="flex items-center text-lg font-semibold text-gray-800">
            <!-- Icon: Teacher Hat (Yellow Accent) -->
            <svg class="w-5 h-5 mr-2 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            New Teacher Registrations (3)
          </h3>
          <button 
            :disabled="selectedTeachersCount === 0"
            :class="[selectedTeachersCount === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700', 'px-4 py-2 text-sm font-medium text-white rounded-lg transition duration-150 shadow-md flex-shrink-0']"
          >
            Approve Selected ({{ selectedTeachersCount }})
          </button>
        </div>

        <!-- Select All Checkbox -->
        <div class="flex justify-start items-center mb-4">
          <label class="flex items-center text-sm font-medium text-gray-600 cursor-pointer">
            <input 
              type="checkbox" 
              :checked="areAllTeachersSelected" 
              @change="toggleAllTeachers" 
              class="form-checkbox text-indigo-600 rounded-md border-gray-300 w-4 h-4 mr-2 focus:ring-indigo-500"
            >
            Select All
          </label>
        </div>

        <!-- Teacher List -->
        <div v-for="(teacher, index) in teacherList" :key="teacher.name" 
             class="flex items-start justify-between py-3 border-t border-gray-100"
             :class="{ 'border-t-0': index === 0 }">
          <div class="flex items-start">
            <input type="checkbox" v-model="teacher.isSelected" class="form-checkbox text-indigo-600 rounded-md border-gray-300 w-5 h-5 mt-1 mr-3 focus:ring-indigo-500">
            <div>
              <p class="text-base font-semibold text-gray-900">{{ teacher.name }} ({{ teacher.subject }})</p>
              <p class="text-xs text-gray-500">Application submitted {{ teacher.daysAgo }} {{ teacher.daysAgo > 1 ? 'days' : 'day' }} ago.</p>
            </div>
          </div>
          <!-- PENDING Badge -->
          <span class="px-3 py-1 text-xs font-semibold text-orange-600 bg-orange-100 rounded-full tracking-wider flex-shrink-0">
            PENDING
          </span>
        </div>

        <!-- Footer Link -->
        <a href="#" class="mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 self-start">
          View all 3 teacher applications →
        </a>
      </div>

      <!-- Action Card 2: New Student Requests -->
      <div class="flex flex-col p-6 bg-white rounded-xl shadow-lg border border-gray-100">
        
        <!-- Card Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4 pb-4 border-b border-gray-100">
          <h3 class="flex items-center text-lg font-semibold text-gray-800">
            <!-- Icon: Student (Blue/Teal Accent) -->
            <svg class="w-5 h-5 mr-2 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 13a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg>
            New Student Requests (12)
          </h3>
          <button 
            :disabled="selectedStudentsCount === 0"
            :class="[selectedStudentsCount === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700', 'px-4 py-2 text-sm font-medium text-white rounded-lg transition duration-150 shadow-md flex-shrink-0']"
          >
            Approve Selected ({{ selectedStudentsCount }})
          </button>
        </div>

        <!-- Select All Checkbox -->
        <div class="flex justify-start items-center mb-4">
          <label class="flex items-center text-sm font-medium text-gray-600 cursor-pointer">
            <input 
              type="checkbox" 
              :checked="areAllStudentsSelected" 
              @change="toggleAllStudents" 
              class="form-checkbox text-indigo-600 rounded-md border-gray-300 w-4 h-4 mr-2 focus:ring-indigo-500"
            >
            Select All
          </label>
        </div>

        <!-- Student List -->
        <div v-for="(student, index) in studentList" :key="student.name" 
             class="flex items-start justify-between py-3 border-t border-gray-100"
             :class="{ 'border-t-0': index === 0 }">
          <div class="flex items-start">
            <input type="checkbox" v-model="student.isSelected" class="form-checkbox text-indigo-600 rounded-md border-gray-300 w-5 h-5 mt-1 mr-3 focus:ring-indigo-500">
            <div>
              <p class="text-base font-semibold text-gray-900">{{ student.name }} ({{ student.type }})</p>
              <p class="text-xs text-gray-500">Request type: {{ student.detail }}.</p>
            </div>
          </div>
          <!-- PENDING Badge -->
          <span class="px-3 py-1 text-xs font-semibold text-orange-600 bg-orange-100 rounded-full tracking-wider flex-shrink-0">
            PENDING
          </span>
        </div>

        <!-- Footer Link -->
        <a href="#" class="mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 self-start">
          View all 12 student applications →
        </a>
      </div>
    </div>
  </div>
</AppLayout>
</template>
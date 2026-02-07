<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { 
    Calendar, 
    Clock, 
    Users, 
    CheckCircle2, 
    XCircle, 
    AlertCircle,
    Download,
    Filter,
    Search,
    RefreshCw
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

type AttendanceRecord = {
    id: number;
    student_id: string;
    student_name: string;
    student_email: string;
    session_id: number;
    subject_name: string;
    room_name: string;
    status: 'present' | 'absent' | 'late';
    time_in: string | null;
    date: string;
};

type Course = {
    id: number;
    course_name: string;
};

type Subject = {
    subject_id: number;
    subject_name: string;
};

type ClassSession = {
    session_id: number;
    subject_name: string;
    room_name: string;
    start_time: string;
    end_time: string;
};

// Statistics
const totalPresent = ref(0);
const totalAbsent = ref(0);
const totalLate = ref(0);
const attendanceRate = computed(() => {
    const total = totalPresent.value + totalAbsent.value + totalLate.value;
    return total > 0 ? Math.round((totalPresent.value / total) * 100) : 0;
});

// Filters
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const selectedCourse = ref<number | 'all'>('all');
const selectedSubject = ref<number | 'all'>('all');
const selectedSession = ref<number | 'all'>('all');
const selectedStatus = ref<string | 'all'>('all');
const searchQuery = ref('');

// Data
const courses = ref<Course[]>([]);
const subjects = ref<Subject[]>([]);
const sessions = ref<ClassSession[]>([]);
const attendanceRecords = ref<AttendanceRecord[]>([]);
const loading = ref(false);

// Filtered records
const filteredRecords = computed(() => {
    let filtered = attendanceRecords.value;

    if (selectedCourse.value !== 'all') {
        // Filter by course (would need to join with student data)
        // For now, we'll skip this filter
    }

    if (selectedSubject.value !== 'all') {
        const subject = subjects.value.find(s => s.subject_id === selectedSubject.value);
        if (subject) {
            filtered = filtered.filter(r => r.subject_name === subject.subject_name);
        }
    }

    if (selectedSession.value !== 'all') {
        filtered = filtered.filter(r => r.session_id === selectedSession.value);
    }

    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(r => r.status === selectedStatus.value);
    }

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(r =>
            r.student_name.toLowerCase().includes(query) ||
            r.student_id.toLowerCase().includes(query) ||
            r.subject_name.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Load data functions
const loadCourses = async () => {
    try {
        const { data } = await axios.get('/api/courses');
        courses.value = data?.courses ?? [];
    } catch (error) {
        console.error('Failed to load courses:', error);
    }
};

const loadSubjects = async () => {
    try {
        const { data } = await axios.get('/api/subjects');
        subjects.value = data?.subjects ?? [];
    } catch (error) {
        console.error('Failed to load subjects:', error);
    }
};

const loadSessions = async () => {
    try {
        const { data } = await axios.get('/class_sessions');
        sessions.value = data?.sessions ?? [];
    } catch (error) {
        console.error('Failed to load sessions:', error);
    }
};

const loadAttendance = async () => {
    loading.value = true;
    try {
        // Mock data for now - replace with actual API call
        const { data } = await axios.get(`/api/attendance?date=${selectedDate.value}`);
        attendanceRecords.value = data?.records ?? [];
        
        // Calculate statistics
        totalPresent.value = attendanceRecords.value.filter(r => r.status === 'present').length;
        totalAbsent.value = attendanceRecords.value.filter(r => r.status === 'absent').length;
        totalLate.value = attendanceRecords.value.filter(r => r.status === 'late').length;
    } catch (error) {
        console.error('Failed to load attendance:', error);
        // Use mock data if API fails
        attendanceRecords.value = [];
        totalPresent.value = 0;
        totalAbsent.value = 0;
        totalLate.value = 0;
    } finally {
        loading.value = false;
    }
};

const refreshData = () => {
    loadAttendance();
};

const exportAttendance = () => {
    alert('Export functionality coming soon!');
};

const getStatusClass = (status: string) => {
    switch (status) {
        case 'present':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'absent':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'late':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'present':
            return CheckCircle2;
        case 'absent':
            return XCircle;
        case 'late':
            return AlertCircle;
        default:
            return Clock;
    }
};

const formatTime = (time: string | null) => {
    if (!time) return 'N/A';
    const date = new Date(`2000-01-01 ${time}`);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
    loadCourses();
    loadSubjects();
    loadSessions();
    loadAttendance();
});
</script>

<template>
    <AppLayout>
        <Head title="Attendance Monitoring - AttendEase" />

        <div class="space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Attendance Monitoring
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Real-time attendance tracking and monitoring across all sessions
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Present -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Present Today
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">
                                {{ totalPresent }}
                            </h3>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                Students marked present
                            </p>
                        </div>
                        <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/30">
                            <CheckCircle2 class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>

                <!-- Total Absent -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Absent Today
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">
                                {{ totalAbsent }}
                            </h3>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                Students marked absent
                            </p>
                        </div>
                        <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/30">
                            <XCircle class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                </div>

                <!-- Total Late -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Late Today
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ totalLate }}
                            </h3>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                Students marked late
                            </p>
                        </div>
                        <div class="rounded-lg bg-yellow-100 p-3 dark:bg-yellow-900/30">
                            <AlertCircle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                    </div>
                </div>

                <!-- Attendance Rate -->
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Attendance Rate
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-blue-600 dark:text-blue-400">
                                {{ attendanceRate }}%
                            </h3>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                Overall attendance
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/30">
                            <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Date Picker -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date
                        </label>
                        <div class="relative">
                            <Calendar class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="selectedDate"
                                type="date"
                                @change="loadAttendance"
                                class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                    </div>

                    <!-- Course Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Course
                        </label>
                        <select
                            v-model="selectedCourse"
                            class="w-full rounded-lg border border-gray-300 py-2 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="all">All Courses</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                {{ course.course_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Subject
                        </label>
                        <select
                            v-model="selectedSubject"
                            class="w-full rounded-lg border border-gray-300 py-2 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="all">All Subjects</option>
                            <option v-for="subject in subjects" :key="subject.subject_id" :value="subject.subject_id">
                                {{ subject.subject_name }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status
                        </label>
                        <select
                            v-model="selectedStatus"
                            class="w-full rounded-lg border border-gray-300 py-2 px-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="all">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                    </div>
                </div>

                <!-- Search and Actions -->
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by student name or ID..."
                                class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-4 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400"
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button
                            @click="refreshData"
                            class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700"
                        >
                            <RefreshCw class="h-4 w-4" />
                            Refresh
                        </button>
                        <button
                            @click="exportAttendance"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <Download class="h-4 w-4" />
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Student
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Room
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Time In
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-if="loading">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex items-center justify-center">
                                        <RefreshCw class="h-8 w-8 animate-spin text-blue-600" />
                                    </div>
                                </td>
                            </tr>
                            <tr v-else-if="filteredRecords.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <Users class="mx-auto mb-3 h-12 w-12 text-gray-400" />
                                        <p class="text-sm font-medium">No attendance records found</p>
                                        <p class="mt-1 text-xs">
                                            Try adjusting your filters or select a different date
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-else
                                v-for="record in filteredRecords"
                                :key="record.id"
                                class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ record.student_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ record.student_id }}
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ record.subject_name }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ record.room_name }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                        <Clock class="h-4 w-4" />
                                        {{ formatTime(record.time_in) }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        :class="getStatusClass(record.status)"
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
                                    >
                                        <component :is="getStatusIcon(record.status)" class="h-4 w-4" />
                                        {{ record.status.charAt(0).toUpperCase() + record.status.slice(1) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing <span class="font-medium">{{ filteredRecords.length }}</span> records
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Last updated: {{ new Date().toLocaleTimeString() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

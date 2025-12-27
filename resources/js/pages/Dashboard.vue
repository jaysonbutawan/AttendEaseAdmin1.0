<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface DashboardProps {
    totalStudents?: number;
    activeSessions?: number;
    roomsInUse?: {
        inUse: number;
        total: number;
    };
    liveAttendance?: Array<{
        id: number;
        subject: string;
        course: string;
        teacher: string;
        room: string;
        present: number;
        late: number;
        absent: number;
        isGeoFenced: boolean;
        status: string;
    }>;
    roomActivity?: Array<{
        id: number;
        name: string;
        status: 'occupied' | 'scheduled' | 'idle';
        currentSession?: string;
        nextSession?: string;
        nextSessionTime?: string;
    }>;
    weeklyAttendance?: Array<{
        day: string;
        percentage: number;
    }>;
    subjectPerformance?: Array<{
        subject: string;
        percentage: number;
    }>;
}

const props = defineProps<DashboardProps>();

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Administrator');

const formattedDate = computed(() => {
    const now = new Date();
    return new Intl.DateTimeFormat(undefined, {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    }).format(now);
});

// Compute total enrolled students
const totalEnrolledStudents = computed(() => {
    return props.totalStudents ?? 0;
});

// Compute active class sessions
const activeClassSessions = computed(() => {
    return props.activeSessions ?? 0;
});

// Compute rooms in use
const roomsInUse = computed(() => {
    const inUse = props.roomsInUse?.inUse ?? 0;
    const total = props.roomsInUse?.total ?? 0;
    return {
        inUse,
        total,
        idle: total - inUse,
        display: `${inUse}/${total}`
    };
});

// Live attendance sessions
const liveAttendanceSessions = computed(() => {
    return props.liveAttendance ?? [];
});

// Room activity list
const roomActivityList = computed(() => {
    return props.roomActivity ?? [];
});

// Weekly attendance data
const weeklyAttendanceData = computed(() => {
    return props.weeklyAttendance ?? [];
});

// Today's attendance rate
const todayAttendanceRate = computed(() => {
    const data = weeklyAttendanceData.value;
    if (!data.length) return 0;
    return data[data.length - 1]?.percentage ?? 0;
});

// Subject performance data
const subjectPerformanceData = computed(() => {
    return props.subjectPerformance ?? [];
});

// Helper function to get status badge color
const getStatusBadgeClass = (status: string) => {
    switch (status.toLowerCase()) {
        case 'occupied':
            return 'bg-green-100 text-green-800';
        case 'scheduled':
            return 'bg-blue-100 text-blue-800';
        case 'idle':
            return 'bg-gray-100 text-gray-700';
        default:
            return 'bg-gray-100 text-gray-700';
    }
};

// Helper function to get border color
const getBorderColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'occupied':
            return 'border-green-500';
        case 'scheduled':
            return 'border-blue-300';
        case 'idle':
            return 'border-gray-300';
        default:
            return 'border-gray-300';
    }
};

// Helper function to get performance color
const getPerformanceColor = (percentage: number) => {
    if (percentage >= 90) return { bar: 'bg-green-500', text: 'text-green-600' };
    if (percentage >= 80) return { bar: 'bg-amber-500', text: 'text-amber-600' };
    return { bar: 'bg-red-500', text: 'text-red-600' };
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
            <Head title="Dashboard - AttendEase" />

            <!-- Header Section -->
            <div class="mb-8 pb-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Good Morning, {{ userName }}</h1>
                        <p class="text-gray-500 text-sm mt-2">Today is {{ formattedDate }} | Current Semester</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <svg class="w-6 h-6 text-gray-600 cursor-pointer hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-700">Active</p>
                            <p class="text-xs text-green-600">Online</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Enrolled Students -->
                <Link href="/courses" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition cursor-pointer block">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Enrolled Students</p>
                            <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ totalEnrolledStudents.toLocaleString() }}</h3>
                            <p class="text-xs text-gray-500 mt-2">Students currently registered</p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"></path>
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- Active Class Sessions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Active Class Sessions</p>
                            <h3 class="text-3xl font-bold text-green-600 mt-2">{{ activeClassSessions }}</h3>
                            <p class="text-xs text-gray-500 mt-2">Currently running</p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rooms in Use -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Rooms in Use</p>
                            <h3 class="text-3xl font-bold text-indigo-600 mt-2">{{ roomsInUse.display }}</h3>
                            <p class="text-xs text-gray-500 mt-2">{{ roomsInUse.idle }} rooms idle</p>
                        </div>
                        <div class="bg-indigo-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5.581m0 0H9.11m3.409 0H15.5m-3.409 0H7m8-6.5h-5"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance Rate -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Today's Attendance Rate</p>
                            <h3 class="text-3xl font-bold text-amber-600 mt-2">{{ todayAttendanceRate }}%</h3>
                            <p class="text-xs text-gray-500 mt-2">Present percentage today</p>
                        </div>
                        <div class="bg-amber-100 rounded-lg p-3">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Attendance Overview and Room Activity Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Live Attendance Overview -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Live Attendance Overview</h2>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <div
                            v-for="session in liveAttendanceSessions"
                            :key="session.id"
                            class="border border-gray-200 rounded-lg p-4 hover:border-green-300 hover:bg-green-50 transition"
                        >
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ session.subject }} - {{ session.course }}</h3>
                                    <p class="text-sm text-gray-600">{{ session.teacher }} | {{ session.room }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 mr-1 bg-green-600 rounded-full animate-pulse"></span> {{ session.status }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold text-green-600">{{ session.present }}</span> Present |
                                    <span class="font-semibold text-amber-600">{{ session.late }}</span> Late |
                                    <span class="font-semibold text-red-600">{{ session.absent }}</span> Absent
                                </div>
                                <div class="flex items-center gap-2" v-if="session.isGeoFenced">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs text-blue-600 font-medium">Geo-Fenced</span>
                                </div>
                                <div class="flex items-center gap-2" v-else>
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-xs text-gray-500 font-medium">No Validation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Activity Panel -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Room Activity</h2>
                    <div class="space-y-3">
                        <div
                            v-for="room in roomActivityList"
                            :key="room.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            :class="getBorderColor(room.status) + ' border-l-4'"
                        >
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ room.name }}</p>
                                <p class="text-xs text-gray-600" v-if="room.status === 'occupied'">{{ room.currentSession }} (Active)</p>
                                <p class="text-xs text-gray-600" v-else-if="room.status === 'scheduled'">Next: {{ room.nextSession }} ({{ room.nextSessionTime }})</p>
                                <p class="text-xs text-gray-600" v-else>No sessions today</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getStatusBadgeClass(room.status)">{{ room.status.charAt(0).toUpperCase() + room.status.slice(1) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Preview (only, logs removed) -->
            <div class="grid grid-cols-1 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Analytics Preview</h2>
                    <!-- Weekly Attendance Trend -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Weekly Attendance Trend</h3>
                        <div class="flex items-end gap-2 h-24">
                            <div
                                v-for="item in weeklyAttendanceData"
                                :key="item.day"
                                class="flex-1 bg-blue-500/70 rounded-t-lg"
                                :style="{ height: Math.max(item.percentage, 5) + '%' }"
                            ></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span v-for="item in weeklyAttendanceData" :key="item.day">{{ item.day }}</span>
                        </div>
                    </div>

                    <!-- Subject Performance -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Subject Performance</h3>
                        <div class="space-y-4">
                            <div v-for="item in subjectPerformanceData" :key="item.subject">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-600">{{ item.subject }}</span>
                                    <span :class="getPerformanceColor(item.percentage).text" class="text-xs font-semibold">{{ item.percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div :class="getPerformanceColor(item.percentage).bar" class="h-2 rounded-full" :style="{ width: item.percentage + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

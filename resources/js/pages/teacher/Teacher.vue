<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type Teacher } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Filter, Plus, Search, Users } from 'lucide-vue-next';
import { ref } from 'vue';
import SelectSubject from './SelectSubject.vue';
const PRIMARY_COLOR_RGB = '79, 57, 246';
const PRIMARY_COLOR_HEX = '#4F39F6';

const teachersData: Teacher[] = [
    {
        id: 1,
        name: 'Dr. Helena Vance',
        email: 'helena.vance@schooldomain.edu',
        initials: 'HV',
        status: 'Active',
        department: 'Science',
        assignedSubjects: [
            { id: 101, name: 'Physics' },
            { id: 102, name: 'Chemistry' },
            { id: 103, name: 'Advanced Science' },
        ],
    },
    {
        id: 2,
        name: 'Lia Torres',
        email: 'lia.torres@schooldomain.edu',
        initials: 'LT',
        status: 'Active',
        department: 'Mathematics',
        assignedSubjects: [
            { id: 201, name: 'Algebra' },
            { id: 202, name: 'Geometry' },
            { id: 203, name: 'Calculus' },
        ],
    },
    {
        id: 3,
        name: 'John Doe',
        email: 'john.doe@schooldomain.edu',
        initials: 'JD',
        status: 'On Leave',
        department: 'English',
        assignedSubjects: [
            { id: 301, name: 'Literatural' },
            { id: 302, name: 'Writing' },
            { id: 303, name: 'Creative Writing' },
        ],
    },
    {
        id: 4,
        name: 'Sarah Johnson',
        email: 'sarah.johnson@schooldomain.edu',
        initials: 'SJ',
        status: 'Active',
        department: 'Science',
        assignedSubjects: [
            { id: 401, name: 'Biology' },
            { id: 402, name: 'Environmental Science' },
        ],
    },
];

const pendingApprovals = ref([
    { id: 1, name: 'Jane Doe', requestedOn: '2024-07-28', initials: 'JD' },
    { id: 2, name: 'Mike Ross', requestedOn: '2024-07-27', initials: 'MR' },
    {
        id: 3,
        name: 'Harvey Specter',
        requestedOn: '2024-07-26',
        initials: 'HS',
    },
]);

const pendingApprovalsStudents = ref([
    { id: 1, name: 'John Doe', requestedTime: '2 hours ago', initials: 'JD' },
    { id: 2, name: 'Jane Smith', requestedTime: '5 hours ago', initials: 'JS' },
    { id: 3, name: 'Mike...', requestedTime: '1 day ago', initials: 'M' },
    { id: 3, name: 'Mike...', requestedTime: '1 day ago', initials: 'M' },
    { id: 3, name: 'Mike...', requestedTime: '1 day ago', initials: 'M' },
    { id: 3, name: 'Mike...', requestedTime: '1 day ago', initials: 'M' },
]);

const pendingApprovalsTeachers = ref([
    { id: 10, name: 'Jane Doe', requestedTime: '2024-07-28', initials: 'JD' },
    {
        id: 20,
        name: 'Alex Johnson',
        requestedTime: '2024-07-27',
        initials: 'AJ',
    },
]);

const activeApprovalTab = ref<'Students' | 'Teachers'>('Students');

const departments = ['Mathematics', 'Science', 'English', 'History'];
const availableTeachers = [
    'Select Teacher',
    'Dr. Helena Vance',
    'Lia Torres',
    'Sarah Johnson',
];
const rooms = ['Room 101', 'Room 102', 'Room 201', 'Room 202'];
const courses = [
    'Computer Science',
    'Business Administration',
    'Engineering',
    'Mathematics',
];
const days = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
];

const assignForm = ref({
    subjectName: '',
    teacher: '',
    department: '',
    course: '',
    dayOfWeek: 'Monday',
    startTime: '',
    endTime: '',
    roomNumber: '',
});

const handleAssignTeacher = () => {
    console.log('Assigning teacher:', assignForm.value);
    alert('Teacher assigned successfully!');
    assignForm.value = {
        subjectName: '',
        teacher: '',
        department: '',
        course: '',
        dayOfWeek: 'Monday',
        startTime: '',
        endTime: '',
        roomNumber: '',
    };
};

const search = ref('');

const handleApprove = (id: number) => {
    console.log('Approving teacher ID:', id);
    alert(`Teacher ${id} approved (mock action)!`);
    pendingApprovals.value = pendingApprovals.value.filter(
        (item) => item.id !== id,
    );
};

const handleReject = (id: number) => {
    console.log('Rejecting teacher ID:', id);
    alert(`Teacher ${id} rejected (mock action)!`);
    pendingApprovals.value = pendingApprovals.value.filter(
        (item) => item.id !== id,
    );
};

const handleAssignSubjects = () => {
    alert('Subjects assigned (mock action)!');
};
</script>

<template>
    <Head title="Teacher Management" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-6 p-6 md:p-8">
            <header class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Subject Assignment
                    </h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">
                        Assign subjects to one or more students using the panels
                        below.
                    </p>
                </div>
                <button
                    @click="handleAssignSubjects"
                    class="flex items-center space-x-2 rounded-xl px-6 py-3 text-base font-semibold text-white shadow-md transition duration-150 ease-in-out"
                    :style="{ backgroundColor: PRIMARY_COLOR_HEX }"
                    :class="`hover:bg-[rgba(${PRIMARY_COLOR_RGB},0.8)]`"
                >
                    <Plus class="h-5 w-5" />
                    <span>Assign Subject(s)</span>
                </button>
            </header>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <div
                    class="h-fit rounded-xl border border-gray-200 bg-white shadow-xl lg:col-span-1 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="p-6">
                        <h2
                            class="mb-4 text-xl font-semibold text-gray-900 dark:text-white"
                        >
                            Pending Approvals
                        </h2>

                        <div
                            class="mb-4 flex border-b border-gray-200 dark:border-gray-700"
                        >
                            <button
                                @click="activeApprovalTab = 'Students'"
                                class="px-4 py-2 text-sm font-medium transition duration-150"
                                :class="
                                    activeApprovalTab === 'Students'
                                        ? `text-[${PRIMARY_COLOR_HEX}] border-b-2 border-[${PRIMARY_COLOR_HEX}] dark:text-white`
                                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                                "
                            >
                                Students ({{ pendingApprovalsStudents.length }})
                            </button>
                            <button
                                @click="activeApprovalTab = 'Teachers'"
                                class="px-4 py-2 text-sm font-medium transition duration-150"
                                :class="
                                    activeApprovalTab === 'Teachers'
                                        ? `text-[${PRIMARY_COLOR_HEX}] border-b-2 border-[${PRIMARY_COLOR_HEX}] dark:text-white`
                                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                                "
                            >
                                Teachers ({{ pendingApprovalsTeachers.length }})
                            </button>
                        </div>

                        <div class="max-h-96 space-y-3 overflow-y-auto pr-2">
                            <div
                                v-if="
                                    (activeApprovalTab === 'Students' &&
                                        pendingApprovalsStudents.length ===
                                            0) ||
                                    (activeApprovalTab === 'Teachers' &&
                                        pendingApprovalsTeachers.length === 0)
                                "
                                class="p-4 text-center text-gray-500 dark:text-gray-400"
                            >
                                No pending
                                {{ activeApprovalTab.toLowerCase() }} approvals.
                            </div>

                            <div
                                v-for="approval in pendingApprovalsStudents"
                                :key="approval.id"
                                v-if="activeApprovalTab === 'Students'"
                                class="flex items-center justify-between rounded-lg p-3 transition duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-white dark:bg-gray-600"
                                    >
                                        {{ approval.initials }}
                                    </div>

                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ approval.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ approval.requestedTime }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <!-- APPROVE BUTTON -->
                                    <button
                                        @click="handleApprove(approval.id)"
                                        class="flex items-center gap-1 rounded-md border border-green-600 px-3 py-1 text-green-600 transition hover:bg-green-600 hover:text-white"
                                    >
                                        <Check class="h-4 w-4" />
                                        Approve
                                    </button>

                                    <!-- REJECT BUTTON -->
                                    <button
                                        @click="handleReject(approval.id)"
                                        class="flex items-center gap-1 rounded-md border border-red-600 px-3 py-1 text-red-600 transition hover:bg-red-600 hover:text-white"
                                    >
                                        <X class="h-4 w-4" />
                                        Reject
                                    </button>
                                </div>
                            </div>

                            <div
                                v-for="approval in pendingApprovalsTeachers"
                                :key="approval.id"
                                v-if="activeApprovalTab === 'Teachers'"
                                class="flex items-center justify-between rounded-lg p-2 transition duration-150 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-white dark:bg-gray-600"
                                    >
                                        {{ approval.initials }}
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ approval.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            Requested on:
                                            {{ approval.requestedTime }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <!-- APPROVE BUTTON -->
                                    <button
                                        @click="handleApprove(approval.id)"
                                        class="flex items-center justify-center gap-1 rounded-md border border-green-600 px-3 py-1 text-green-600 transition hover:bg-green-600 hover:text-white"
                                    >
                                        <Check class="h-4 w-4" />
                                        Approve
                                    </button>

                                    <!-- REJECT BUTTON -->
                                    <button
                                        @click="handleReject(approval.id)"
                                        class="flex items-center justify-center gap-1 rounded-md border border-red-600 px-3 py-1 text-red-600 transition hover:bg-red-600 hover:text-white"
                                    >
                                        <X class="h-4 w-4" />
                                        <span class="ml-1">Reject</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <SelectSubject />
                </div>
            </div>
        </div>

        <!-- ASSIGN TEACHER FORM (WHITE CARD) -->
        <div class="mt-2 lg:mx-10">
            <h2
                class="mb-6 text-2xl font-semibold text-gray-900 dark:text-white"
            >
                Manage Subjects
            </h2>

            <div
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
                <form
                    @submit.prevent="handleAssignTeacher"
                    class="grid grid-cols-1 gap-6 md:grid-cols-5"
                >
                    <!-- Subject Name -->
                    <div class="md:col-span-1">
                        <label
                            for="subjectName"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            Subject Name
                        </label>
                        <input
                            id="subjectName"
                            v-model="assignForm.subjectName"
                            type="text"
                            placeholder="e.g., Advanced Mathematics"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Assigned Teacher -->
                    <div class="md:col-span-1">
                        <label
                            for="teacher"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            Assigned Teacher
                        </label>
                        <select
                            id="teacher"
                            v-model="assignForm.teacher"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="" disabled>Select a teacher</option>
                            <option
                                v-for="teacher in availableTeachers"
                                :key="teacher"
                                :value="teacher"
                            >
                                {{ teacher }}
                            </option>
                        </select>
                    </div>
                    <!-- Day of Week -->
                    <div class="md:col-span-1">
                        <label
                            for="dayOfWeek"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            Day of the Week
                        </label>
                        <select
                            id="dayOfWeek"
                            v-model="assignForm.dayOfWeek"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option v-for="day in days" :key="day" :value="day">
                                {{ day }}
                            </option>
                        </select>
                    </div>

                    <!-- Start Time -->
                    <div class="md:col-span-1">
                        <label
                            for="startTime"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            Start Time
                        </label>
                        <input
                            id="startTime"
                            v-model="assignForm.startTime"
                            type="time"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- End Time -->
                    <div class="md:col-span-1">
                        <label
                            for="endTime"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            End Time
                        </label>
                        <input
                            id="endTime"
                            v-model="assignForm.endTime"
                            type="time"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Room Number -->
                    <div class="md:col-span-1">
                        <label
                            for="roomNumber"
                            class="mb-2 block text-xl font-medium text-gray-700 dark:text-gray-300"
                        >
                            Room Number
                        </label>
                        <select
                            id="roomNumber"
                            v-model="assignForm.roomNumber"
                            class="w-full rounded-lg border-gray-300 text-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="" disabled>Select Room</option>
                            <option
                                v-for="room in rooms"
                                :key="room"
                                :value="room"
                            >
                                {{ room }}
                            </option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex justify-end space-x-3 md:col-span-4">
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-lg font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex items-center space-x-2 rounded-lg px-4 py-2 text-lg font-semibold text-white shadow-md transition duration-150 ease-in-out hover:opacity-80"
                            :style="{ backgroundColor: PRIMARY_COLOR_HEX }"
                        >
                            <Plus class="h-4 w-4" />
                            <span>Assign Teacher</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ALL TEACHERS TABLE (WHITE CARD) -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-6 shadow-xl md:p-8 dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="mb-6 flex items-center justify-between">
                <h2
                    class="flex items-center space-x-2 text-xl font-semibold text-gray-900 dark:text-white"
                >
                    <Users
                        class="h-5 w-5"
                        :style="{ color: `rgb(${PRIMARY_COLOR_RGB})` }"
                    />
                    <span>All Teachers</span>
                </h2>

                <div class="flex space-x-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search teachers..."
                            class="rounded-lg border-gray-300 py-2 pr-4 pl-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <!-- Filter Button -->
                    <button
                        class="flex items-center space-x-2 rounded-lg border px-4 py-2 text-sm font-medium shadow-sm transition duration-150"
                        :style="{
                            borderColor: `rgba(${PRIMARY_COLOR_RGB}, 0.6)`,
                            color: `rgb(${PRIMARY_COLOR_RGB})`,
                        }"
                    >
                        <Filter class="h-4 w-4" />
                        <span>Filter</span>
                    </button>
                </div>
            </div>

            <!-- Teacher List Table -->
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                TEACHER NAME
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                DEPARTMENT
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                ASSIGNED SUBJECTS
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                STATUS
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                ACTIONS
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"
                    >
                        <tr
                            v-for="teacher in teachersData"
                            :key="teacher.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <!-- Teacher Name -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex size-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                                        :style="{
                                            backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.8)`,
                                        }"
                                    >
                                        {{ teacher.initials }}
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ teacher.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ teacher.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Department -->
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    :class="{
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400':
                                            teacher.department === 'Science',
                                        'bg-indigo-100 text-indigo-800 dark:bg-indigo-800/20 dark:text-indigo-400':
                                            teacher.department ===
                                            'Mathematics',
                                        'bg-pink-100 text-pink-800 dark:bg-pink-800/20 dark:text-pink-400':
                                            teacher.department === 'English',
                                    }"
                                >
                                    {{ teacher.department }}
                                </span>
                            </td>
                            <!-- Assigned Subjects -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="subject in teacher.assignedSubjects"
                                        :key="subject.id"
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    >
                                        {{ subject.name }}
                                    </span>
                                </div>
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400':
                                            teacher.status === 'Active',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400':
                                            teacher.status === 'On Leave',
                                    }"
                                >
                                    {{ teacher.status }}
                                </span>
                            </td>
                            <!-- Actions -->
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <div class="flex justify-end space-x-3">
                                    <button
                                        class="flex items-center space-x-1 text-indigo-600 transition hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        class="flex items-center space-x-1 text-red-600 transition hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                class="mt-6 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400"
            >
                <div>Showing 1 to {{ teachersData.length }} of 24 teachers</div>
                <div class="flex space-x-2">
                    <button
                        class="rounded-lg border border-gray-300 px-3 py-1 transition hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        Previous
                    </button>
                    <button
                        class="rounded-lg px-3 py-1 font-bold text-white transition"
                        :style="{
                            backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 1)`,
                        }"
                    >
                        1
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 px-3 py-1 transition hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        2
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 px-3 py-1 transition hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        3
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 px-3 py-1 transition hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.focus\\:border-indigo-500:focus {
    border-color: rgb(79, 57, 246) !important;
}
.focus\\:ring-indigo-500:focus {
    --tw-ring-color: rgb(79, 57, 246) !important;
}
</style>

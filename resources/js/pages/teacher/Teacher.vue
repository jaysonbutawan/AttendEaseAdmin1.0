<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type Teacher } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Filter, Grid3x3, List, Search, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
const PRIMARY_COLOR_RGB = '79, 57, 246';

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

const search = ref('');
const viewMode = ref<'table' | 'cards'>('table');
const selectedTeachers = ref<number[]>([]);

const filteredTeachers = computed(() => {
    if (!search.value.trim()) return teachersData;
    const term = search.value.toLowerCase();
    return teachersData.filter(
        (teacher) =>
            teacher.name.toLowerCase().includes(term) ||
            teacher.email.toLowerCase().includes(term) ||
            teacher.department.toLowerCase().includes(term),
    );
});

const allTeachersSelected = computed(() => {
    return (
        filteredTeachers.value.length > 0 &&
        selectedTeachers.value.length === filteredTeachers.value.length
    );
});

const toggleViewMode = (mode: 'table' | 'cards') => {
    viewMode.value = mode;
};

const toggleTeacherSelection = (teacherId: number) => {
    const index = selectedTeachers.value.indexOf(teacherId);
    if (index > -1) {
        selectedTeachers.value.splice(index, 1);
    } else {
        selectedTeachers.value.push(teacherId);
    }
};

const toggleAllTeachers = () => {
    if (selectedTeachers.value.length === filteredTeachers.value.length) {
        selectedTeachers.value = [];
    } else {
        selectedTeachers.value = filteredTeachers.value.map((t) => t.id);
    }
};

const getDepartmentClass = (department: string) => {
    const classes = {
        Science:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400',
        Mathematics:
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-800/20 dark:text-indigo-400',
        English:
            'bg-pink-100 text-pink-800 dark:bg-pink-800/20 dark:text-pink-400',
    };
    return (
        classes[department as keyof typeof classes] ||
        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    );
};

const getStatusClass = (status: string) => {
    return status === 'Active'
        ? 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400'
        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400';
};
</script>

<template>
    <Head title="Teacher Management" />
    <AppLayout>
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

                    <!-- View Toggle -->
                    <div
                        class="flex gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-700"
                    >
                        <button
                            @click="toggleViewMode('cards')"
                            :class="
                                viewMode === 'cards'
                                    ? 'bg-white shadow-sm dark:bg-gray-600'
                                    : 'hover:bg-gray-200 dark:hover:bg-gray-600'
                            "
                            class="rounded-md px-3 py-2 transition"
                            title="Card View"
                        >
                            <Grid3x3
                                class="h-4 w-4 text-gray-700 dark:text-gray-300"
                            />
                        </button>
                        <button
                            @click="toggleViewMode('table')"
                            :class="
                                viewMode === 'table'
                                    ? 'bg-white shadow-sm dark:bg-gray-600'
                                    : 'hover:bg-gray-200 dark:hover:bg-gray-600'
                            "
                            class="rounded-md px-3 py-2 transition"
                            title="Table View"
                        >
                            <List
                                class="h-4 w-4 text-gray-700 dark:text-gray-300"
                            />
                        </button>
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

            <!-- Table View -->
            <div v-if="viewMode === 'table'" class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input
                                    type="checkbox"
                                    :checked="allTeachersSelected"
                                    @change="toggleAllTeachers"
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                />
                            </th>
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
                            v-for="teacher in filteredTeachers"
                            :key="teacher.id"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
                        >
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    :checked="
                                        selectedTeachers.includes(teacher.id)
                                    "
                                    @change="toggleTeacherSelection(teacher.id)"
                                    class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                />
                            </td>
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
                                    :class="
                                        getDepartmentClass(teacher.department)
                                    "
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
                                    :class="getStatusClass(teacher.status)"
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

                        <!-- Empty State -->
                        <tr v-if="filteredTeachers.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <Users
                                        class="mx-auto mb-3 h-12 w-12 text-gray-400"
                                    />
                                    <p class="text-sm font-medium">
                                        No teachers found
                                    </p>
                                    <p class="mt-1 text-xs">
                                        Try adjusting your search
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cards View -->
            <div
                v-if="viewMode === 'cards'"
                class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="teacher in filteredTeachers"
                    :key="teacher.id"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-lg dark:border-gray-600 dark:bg-gray-700"
                >
                    <!-- Card Header -->
                    <div class="mb-4 flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex size-12 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                                :style="{
                                    backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.8)`,
                                }"
                            >
                                {{ teacher.initials }}
                            </div>
                            <div>
                                <h3
                                    class="text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ teacher.name }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    ID: #{{ teacher.id }}
                                </p>
                            </div>
                        </div>
                        <input
                            type="checkbox"
                            :checked="selectedTeachers.includes(teacher.id)"
                            @change="toggleTeacherSelection(teacher.id)"
                            class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ teacher.email }}
                        </p>
                    </div>

                    <!-- Department & Status -->
                    <div class="mb-4 flex items-center justify-between">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="getDepartmentClass(teacher.department)"
                        >
                            {{ teacher.department }}
                        </span>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="getStatusClass(teacher.status)"
                        >
                            {{ teacher.status }}
                        </span>
                    </div>

                    <!-- Assigned Subjects -->
                    <div class="mb-4">
                        <p
                            class="mb-2 text-xs font-medium text-gray-700 dark:text-gray-300"
                        >
                            Assigned Subjects:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="subject in teacher.assignedSubjects"
                                :key="subject.id"
                                class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-600 dark:text-gray-200"
                            >
                                {{ subject.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex gap-2 border-t border-gray-200 pt-4 dark:border-gray-600"
                    >
                        <button
                            class="flex-1 rounded-lg border border-indigo-200 px-4 py-2 text-sm font-medium text-indigo-600 transition hover:bg-indigo-50 dark:border-indigo-800 dark:text-indigo-400 dark:hover:bg-indigo-900/20"
                        >
                            Edit
                        </button>
                        <button
                            class="flex-1 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Empty State for Cards -->
                <div v-if="filteredTeachers.length === 0" class="col-span-full">
                    <div
                        class="py-12 text-center text-gray-500 dark:text-gray-400"
                    >
                        <Users class="mx-auto mb-4 h-16 w-16 text-gray-400" />
                        <p class="text-lg font-medium">No teachers found</p>
                        <p class="mt-2 text-sm">Try adjusting your search</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                class="mt-6 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400"
            >
                <div>
                    Showing 1 to {{ filteredTeachers.length }} of
                    {{ teachersData.length }} teachers
                </div>
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

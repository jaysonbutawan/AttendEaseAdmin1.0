<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import TeacherCard from '@/components/TeacherCard.vue';
import { type Teacher } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Filter, Grid3x3, List, Search, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
const PRIMARY_COLOR_RGB = '79, 57, 246';

// Receive teachers data from backend
interface Props {
    teachers: Teacher[];
}

const props = defineProps<Props>();
const teachersData = ref<Teacher[]>(props.teachers);

const search = ref('');
const viewMode = ref<'table' | 'cards'>('table');
const selectedDepartment = ref<string>('all');
const showFilterDropdown = ref(false);

// Get unique departments
const departments = computed(() => {
    const depts = new Set(teachersData.value.map((t) => t.department));
    return ['all', ...Array.from(depts)];
});

const filteredTeachers = computed(() => {
    let result = teachersData.value;

    // Filter by search term
    if (search.value.trim()) {
        const term = search.value.toLowerCase();
        result = result.filter(
            (teacher) =>
                teacher.name.toLowerCase().includes(term) ||
                teacher.email.toLowerCase().includes(term) ||
                teacher.department.toLowerCase().includes(term) ||
                teacher.assignedSubjects.some((subject) =>
                    subject.name.toLowerCase().includes(term),
                ),
        );
    }

    // Filter by department
    if (selectedDepartment.value !== 'all') {
        result = result.filter(
            (teacher) => teacher.department === selectedDepartment.value,
        );
    }

    return result;
});

const toggleViewMode = (mode: 'table' | 'cards') => {
    viewMode.value = mode;
};

const selectDepartment = (dept: string) => {
    selectedDepartment.value = dept;
    showFilterDropdown.value = false;
};

const clearFilters = () => {
    selectedDepartment.value = 'all';
    search.value = '';
    showFilterDropdown.value = false;
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
                    <div class="relative">
                        <button
                            @click="showFilterDropdown = !showFilterDropdown"
                            class="flex items-center space-x-2 rounded-lg border px-4 py-2 text-sm font-medium shadow-sm transition duration-150"
                            :style="{
                                borderColor: `rgba(${PRIMARY_COLOR_RGB}, 0.6)`,
                                color: `rgb(${PRIMARY_COLOR_RGB})`,
                            }"
                        >
                            <Filter class="h-4 w-4" />
                            <span>Filter</span>
                            <span
                                v-if="selectedDepartment !== 'all'"
                                class="ml-1 rounded-full bg-indigo-100 px-2 py-0.5 text-xs dark:bg-indigo-900"
                            >
                                1
                            </span>
                        </button>

                        <!-- Filter Dropdown -->
                        <div
                            v-if="showFilterDropdown"
                            class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700"
                        >
                            <div class="p-2">
                                <div
                                    class="mb-2 px-3 py-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Department
                                </div>
                                <button
                                    v-for="dept in departments"
                                    :key="dept"
                                    @click="selectDepartment(dept)"
                                    class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm transition hover:bg-gray-100 dark:hover:bg-gray-600"
                                    :class="
                                        selectedDepartment === dept
                                            ? 'bg-gray-100 font-medium dark:bg-gray-600'
                                            : ''
                                    "
                                >
                                    <span class="capitalize">{{
                                        dept === 'all' ? 'All Departments' : dept
                                    }}</span>
                                    <span
                                        v-if="selectedDepartment === dept"
                                        class="text-indigo-600 dark:text-indigo-400"
                                    >
                                        ✓
                                    </span>
                                </button>
                            </div>
                            <div
                                class="border-t border-gray-200 p-2 dark:border-gray-600"
                            >
                                <button
                                    @click="clearFilters"
                                    class="w-full rounded-md px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div v-if="viewMode === 'table'" class="overflow-x-auto">
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
                                class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                            >
                                ACTIONS
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800"
                    >
                        <TeacherCard
                            v-for="teacher in filteredTeachers"
                            :key="teacher.id"
                            :teacher="teacher"
                            :view-mode="viewMode"
                        />

                        <!-- Empty State -->
                        <tr v-if="filteredTeachers.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <Users
                                        class="mx-auto mb-3 h-12 w-12 text-gray-400"
                                    />
                                    <p class="text-sm font-medium">
                                        No teachers found
                                    </p>
                                    <p class="mt-1 text-xs">
                                        Try adjusting your search or filters
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
                <TeacherCard
                    v-for="teacher in filteredTeachers"
                    :key="teacher.id"
                    :teacher="teacher"
                    :view-mode="viewMode"
                />

                <!-- Empty State for Cards -->
                <div v-if="filteredTeachers.length === 0" class="col-span-full">
                    <div
                        class="py-12 text-center text-gray-500 dark:text-gray-400"
                    >
                        <Users class="mx-auto mb-4 h-16 w-16 text-gray-400" />
                        <p class="text-lg font-medium">No teachers found</p>
                        <p class="mt-2 text-sm">
                            Try adjusting your search or filters
                        </p>
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

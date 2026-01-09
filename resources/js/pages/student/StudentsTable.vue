<script setup lang="ts">
import axios from 'axios';
import { Pencil, Search, Trash2 } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

type Course = {
    id: number;
    course_name: string;
};

type Student = {
    id: string;
    name: string;
    department: string;
    year_level?: string;
    email?: string;
    contact?: string;
    initials: string;
    selected: boolean;
};

const courses = ref<Course[]>([]);
const loadingCourses = ref(false);
const courseError = ref('');

const loadCourses = async () => {
    loadingCourses.value = true;
    courseError.value = '';

    try {
        const res = await fetch('/api/courses', { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Unable to load courses');

        const data = await res.json();
        courses.value = data?.courses ?? [];

        console.log('[Courses] Loaded', courses.value);
    } catch (err: any) {
        courseError.value = err?.message || 'Failed to load courses';
        console.error('[Courses] Error', err);
    } finally {
        loadingCourses.value = false;
    }
};

const studentsData = ref<Student[]>([]);
const studentSearchQuery = ref('');
const selectedDepartment = ref<number | 'all'>('all');
const selectedYearLevel = ref<string | 'all'>('all');

const filteredStudents = computed(() => {
    let filtered = studentsData.value;
    if (studentSearchQuery.value.trim()) {
        const query = studentSearchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (student) =>
                student.name.toLowerCase().includes(query) ||
                student.id.toLowerCase().includes(query) ||
                (student.email && student.email.toLowerCase().includes(query)),
        );
    }
    if (selectedDepartment.value !== 'all') {
        filtered = filtered.filter((student) => {
            const course = courses.value.find(
                (c) => c.id === selectedDepartment.value,
            );
            return course && student.department === course.course_name;
        });
    }
    if (selectedYearLevel.value !== 'all') {
        filtered = filtered.filter(
            (student) => student.year_level === selectedYearLevel.value,
        );
    }

    return filtered;
});

const allStudentsSelected = computed(() => {
    if (filteredStudents.value.length === 0) return false;
    return filteredStudents.value.every((student) => student.selected);
});

const resultsPerPage = 5;
const currentPage = ref(1);

const paginatedStudents = computed(() => {
    const start = (currentPage.value - 1) * resultsPerPage;
    const end = start + resultsPerPage;
    return filteredStudents.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / resultsPerPage));

const pageNumbers = computed(() => {
    const pages: (number | '...')[] = [];
    const maxVisiblePages = 5;

    if (totalPages.value <= maxVisiblePages) {
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        pages.push(1);
        if (currentPage.value > 2) pages.push('...');
        if (currentPage.value > 1 && currentPage.value < totalPages.value)
            pages.push(currentPage.value);
        if (currentPage.value < totalPages.value - 1) pages.push('...');
        pages.push(totalPages.value);

        const uniquePages: (number | '...')[] = [];
        let lastItem: number | '...' | undefined = undefined;
        for (const item of pages) {
            if (item === '...' && lastItem === '...') continue;
            if (typeof item === 'number' && uniquePages.includes(item))
                continue;
            uniquePages.push(item);
            lastItem = item;
        }
        return uniquePages;
    }

    return pages;
});

const selectAllTableStudents = computed({
    get: () => {
        if (filteredStudents.value.length === 0) return false;
        return filteredStudents.value.every((s) => s.selected);
    },
    set: (value) => {
        filteredStudents.value.forEach((s) => (s.selected = value));
    },
});

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * resultsPerPage + 1;
    const end = Math.min(currentPage.value * resultsPerPage, filteredStudents.value.length);
    const total = filteredStudents.value.length;
    return `Showing **${start} to ${end}** of **${total}** results`;
});

const goToPage = (page: number | '...') => {
    if (typeof page === 'number' && page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const editStudent = (id: string) => {
    console.log(`Editing student with ID: ${id}`);
    alert(`Editing student ${id} (Mock Action)`);
};

const deleteStudent = (id: string) => {
    console.log(`Deleting student with ID: ${id}`);
    if (confirm(`Are you sure you want to delete student ID ${id}?`)) {
    }
};
const fetchStudents = async () => {
    try {
        const { data } = await axios.get('/students_controller');
        studentsData.value = (data || []).map((student: any) => ({
            ...student,
            selected: false,
            initials: student.initials || getInitials(student.name),
        }));
        console.log('[Students] Loaded', studentsData.value);
    } catch (error) {
        console.error('[Students] Failed to load', error);
    }
};
const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};


onMounted(() => {
    loadCourses();
    fetchStudents();
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 px-6 pt-0 pb-6">
        <div class="mb-2 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Students</h1>
        </div>

        <p class="mb-6 text-gray-600">
            Manage student profiles, records, and information.
        </p>

        <div class="mb-8 flex space-x-4 rounded-lg bg-white p-4 shadow">
            <div class="relative flex-grow">
                <Search
                    class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 transform text-gray-400"
                />
                <input
                    type="text"
                    v-model="studentSearchQuery"
                    placeholder="Search by name, ID, or email..."
                    class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:border-blue-500 focus:ring-blue-500"
                />
            </div>

            <select
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                v-model="selectedDepartment"
            >
                <option value="all">Department: All</option>
                <option
                    v-for="course in courses"
                    :key="course.id"
                    :value="course.id"
                >
                    {{ course.course_name }}
                </option>
            </select>
            <select
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                v-model="selectedYearLevel"
            >
                <option value="all">Year Level: All</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-left">
                            <input
                                type="checkbox"
                                v-model="selectAllTableStudents"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Student ID
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Name
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Email
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Department
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Contact
                        </th>
                        <th
                            class="p-4 text-left text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Year Level
                        </th>
                        <th
                            class="p-4 text-center text-xs font-semibold tracking-wider text-gray-600 uppercase"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr
                        v-for="student in paginatedStudents"
                        :key="student.id"
                        class="hover:bg-blue-50"
                    >
                        <td class="p-4 whitespace-nowrap">
                            <input
                                type="checkbox"
                                v-model="student.selected"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                        </td>
                        <td
                            class="p-4 text-sm font-medium whitespace-nowrap text-gray-600"
                        >
                            {{ student.id }}
                        </td>
                        <td
                            class="p-4 text-sm font-semibold whitespace-nowrap text-gray-900"
                        >
                            {{ student.name }}
                        </td>
                        <td class="p-4 text-sm whitespace-nowrap text-blue-600">
                            {{ student.email || 'N/A' }}
                        </td>
                        <td class="p-4 text-sm whitespace-nowrap text-gray-700">
                            {{ student.department }}
                        </td>
                        <td class="p-4 text-sm whitespace-nowrap text-gray-700">
                            {{ student.contact || 'N/A' }}
                        </td>
                        <td class="p-4 text-sm whitespace-nowrap text-gray-700">
                            {{ student.year_level || 'N/A' }}
                        </td>
                        <td
                            class="space-x-2 p-4 text-center text-sm font-medium whitespace-nowrap"
                        >
                            <button
                                @click="editStudent(student.id)"
                                title="Edit"
                                class="rounded-full p-1 text-blue-500 transition duration-150 hover:bg-gray-100 hover:text-blue-700"
                            >
                                <Pencil class="h-5 w-5" />
                            </button>
                            <button
                                @click="deleteStudent(student.id)"
                                title="Delete"
                                class="rounded-full p-1 text-red-500 transition duration-150 hover:bg-gray-100 hover:text-red-700"
                            >
                                <Trash2 class="h-5 w-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                class="flex items-center justify-between border-t bg-white p-4"
            >
                <span
                    class="text-sm text-gray-700"
                    v-html="showingRange"
                ></span>
                <div class="flex space-x-1">
                    <button
                        @click="prevPage"
                        :disabled="currentPage === 1"
                        :class="[
                            'rounded-lg border border-gray-300 bg-white px-3 py-1 text-gray-500 transition duration-150 hover:bg-gray-100',
                            {
                                'cursor-not-allowed opacity-50':
                                    currentPage === 1,
                            },
                        ]"
                    >
                        &lt;
                    </button>

                    <button
                        v-for="(page, index) in pageNumbers"
                        :key="index"
                        @click="goToPage(page)"
                        :class="[
                            'rounded-lg px-4 py-1 transition duration-150',
                            page === currentPage
                                ? 'border border-blue-600 bg-blue-600 text-white'
                                : page === '...'
                                  ? 'cursor-default text-gray-500'
                                  : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        {{ page }}
                    </button>

                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        :class="[
                            'rounded-lg border border-gray-300 bg-white px-3 py-1 text-gray-500 transition duration-150 hover:bg-gray-100',
                            {
                                'cursor-not-allowed opacity-50':
                                    currentPage === totalPages,
                            },
                        ]"
                    >
                        &gt;
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
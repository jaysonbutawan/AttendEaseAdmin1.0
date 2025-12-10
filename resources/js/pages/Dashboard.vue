<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';

interface Teacher {
    name: string;
    daysAgo: number;
    isSelected: boolean;
}

const teacherList = ref<Teacher[]>([]);
const COLLAPSED_COUNT = 7;
const SCROLL_THRESHOLD = 12;

const showAll = ref(false);

const loadTeachers = async () => {
    try {
        const res = await axios.get('/teachers_controller');
        console.log('Teachers:', res.data); 
        teacherList.value = res.data.map((t: any) => ({
            name: t.name,
            daysAgo: t.daysAgo,
            isSelected: false,
        }));
    } catch (error) {
        console.error('Failed to load teachers:', error);
    }
};

const displayedTeachers = computed(() =>
    showAll.value ? teacherList.value : teacherList.value.slice(0, COLLAPSED_COUNT)
);

const areAllTeachersSelected = computed(() =>
    teacherList.value.every((t) => t.isSelected)
);

const selectedTeachersCount = computed(
    () => teacherList.value.filter((t) => t.isSelected).length
);

const toggleAllTeachers = (event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    teacherList.value.forEach((t) => (t.isSelected = checked));
};




interface Student {
    name: string;
    daysAgo: number;
    isSelected: boolean;
}

const studentList = ref<Student[]>([]);
const STUDENT_COUNT = 7;
const STUDENT_SCROLL_THRESHOLD = 12;

const showAllStudents = ref(false);

const loadStudents = async () => {
    try {
        const res = await axios.get('/students_controller');
        console.log('Students:', res.data); 
        studentList.value = res.data.map((s: any) => ({
            name: s.name,
            daysAgo: s.daysAgo,
            isSelected: false,
        }));
    } catch (error) {
        console.error('Failed to load students:', error);
    }
};

const displayedStudents = computed(() =>
    showAllStudents.value
        ? studentList.value
        : studentList.value.slice(0, STUDENT_COUNT)
);

const selectedStudentsCount = computed(
    () => studentList.value.filter((s) => s.isSelected).length
);

const areAllStudentsSelected = computed(() =>
    studentList.value.every((s) => s.isSelected)
);

const toggleAllStudents = (event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    studentList.value.forEach((s) => (s.isSelected = checked));
};

onMounted(() => {
    console.log('Component mounted');
    loadTeachers();
    loadStudents();
});
</script>



<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6">
            <h1 class="mb-8 text-2xl font-extrabold text-gray-800 sm:text-3xl">
                User Management Overview
            </h1>

            <div class="mb-10 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-lg sm:p-6"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <span class="text-sm font-semibold text-gray-500"
                            >Active Teachers</span
                        >
                        <div
                            class="flex-shrink-0 rounded-full bg-indigo-100 p-2 text-indigo-600"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span
                            class="mr-2 text-3xl font-bold text-indigo-600 sm:text-4xl"
                            >85</span
                        >
                    </div>
                </div>

                <!-- Stat Card 2: Total Students -->
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-lg sm:p-6"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <span class="text-sm font-semibold text-gray-500"
                            >Total Students</span
                        >
                        <!-- Icon Container (Green Accent) -->
                        <div
                            class="flex-shrink-0 rounded-full bg-green-100 p-2 text-green-600"
                        >
                            <!-- Icon: People -->
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="8.5" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span
                            class="mr-2 text-3xl font-bold text-green-600 sm:text-4xl"
                            >1,450</span
                        >
                    </div>
                </div>

                <!-- Stat Card 3: Pending Approvals -->
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-lg sm:p-6"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <span class="text-sm font-semibold text-gray-500"
                            >Pending Approvals</span
                        >
                        <!-- Icon Container (Red/Clock Accent) -->
                        <div
                            class="flex-shrink-0 rounded-full bg-red-100 p-2 text-red-600"
                        >
                            <!-- Icon: Clock -->
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span
                            class="mr-2 text-3xl font-bold text-red-600 sm:text-4xl"
                            >15</span
                        >
                    </div>
                </div>

                <!-- Stat Card 4: Classes in Session -->
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-lg sm:p-6"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <span class="text-sm font-semibold text-gray-500"
                            >Classes in Session</span
                        >
                        <!-- Icon Container (Teal/School Accent) -->
                        <div
                            class="flex-shrink-0 rounded-full bg-teal-100 p-2 text-teal-600"
                        >
                            <!-- Icon: Building/School -->
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"
                                />
                                <path d="M12 22v-4" />
                                <path d="M9 18h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline">
                        <span
                            class="mr-2 text-3xl font-bold text-teal-600 sm:text-4xl"
                            >42</span
                        >
                    </div>
                </div>
            </div>

            <!-- 2. Action Required Section -->
            <h2 class="mb-6 text-xl font-bold text-gray-800">
                Action Required: Pending Approvals (15 Total)
            </h2>

        
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-6 shadow-lg"
                >
                    <div
                        class="mb-4 flex flex-col items-start justify-between gap-4 border-b border-gray-100 pb-4 sm:flex-row sm:items-center"
                    >
                        <h3
                            class="flex items-center text-lg font-semibold text-gray-800"
                        >
                            <svg
                                class="mr-2 h-5 w-5 text-yellow-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                            New Teacher Registrations (3)
                        </h3>
                        <button
                            :disabled="selectedTeachersCount === 0"
                            :class="[
                                selectedTeachersCount === 0
                                    ? 'cursor-not-allowed bg-gray-400'
                                    : 'bg-indigo-600 hover:bg-indigo-700',
                                'flex-shrink-0 rounded-lg px-4 py-2 text-sm font-medium text-white shadow-md transition duration-150',
                            ]"
                        >
                            Approve Selected ({{ selectedTeachersCount }})
                        </button>
                    </div>

                    <!-- Select All Checkbox -->
                    <div class="mb-4 flex items-center justify-start">
                        <label
                            class="flex cursor-pointer items-center text-sm font-medium text-gray-600"
                        >
                            <input
                                type="checkbox"
                                :checked="areAllTeachersSelected"
                                @change="toggleAllTeachers"
                                class="form-checkbox mr-2 h-4 w-4 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            Select All
                        </label>
                    </div>

                    <div
                        :class="[
                            showAll && teacherList.length >= SCROLL_THRESHOLD
                                ? 'max-h-[600px] overflow-y-auto'
                                : '',
                        ]"
                    >
                        <div
                            v-for="(teacher, index) in displayedTeachers"
                            :key="index"
                            class="flex items-start justify-between border-t border-gray-100 py-3"
                            :class="{ 'border-t-0': index === 0 }"
                        >
                            <div class="flex items-start">
                                <input
                                    type="checkbox"
                                    v-model="teacher.isSelected"
                                    class="form-checkbox mt-1 mr-3 h-5 w-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <div>
                                    <p
                                        class="text-base font-semibold text-gray-900"
                                    >
                                        {{ teacher.name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Application submitted
                                        {{ teacher.daysAgo }}
                                        {{
                                            teacher.daysAgo > 1 ? 'days' : 'day'
                                        }}
                                        ago.
                                    </p>
                                </div>
                            </div>

                            <span
                                class="flex-shrink-0 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold tracking-wider text-orange-600"
                            >
                                PENDING
                            </span>
                        </div>

                        <a
                            href="#"
                            v-if="teacherList.length > COLLAPSED_COUNT"
                            @click.prevent="showAll = !showAll"
                            class="mt-4 self-start text-sm font-medium text-indigo-600 transition duration-150 hover:text-indigo-800"
                        >
                            {{
                                showAll
                                    ? 'Show less'
                                    : 'View all teacher applications →'
                            }}
                        </a>
                    </div>
                </div>

                <!-- Action Card 2: New Student Requests -->
                <div
                    class="flex flex-col rounded-xl border border-gray-100 bg-white p-6 shadow-lg"
                >
                    <!-- Card Header -->
                    <div
                        class="mb-4 flex flex-col items-start justify-between gap-4 border-b border-gray-100 pb-4 sm:flex-row sm:items-center"
                    >
                        <h3
                            class="flex items-center text-lg font-semibold text-gray-800"
                        >
                            <!-- Icon: Student (Blue/Teal Accent) -->
                            <svg
                                class="mr-2 h-5 w-5 text-teal-600"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="10" r="3" />
                                <path
                                    d="M12 13a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"
                                />
                            </svg>
                            New Student Requests (12)
                        </h3>
                        <button
                            :disabled="selectedStudentsCount === 0"
                            :class="[
                                selectedStudentsCount === 0
                                    ? 'cursor-not-allowed bg-gray-400'
                                    : 'bg-indigo-600 hover:bg-indigo-700',
                                'flex-shrink-0 rounded-lg px-4 py-2 text-sm font-medium text-white shadow-md transition duration-150',
                            ]"
                        >
                            Approve Selected ({{ selectedStudentsCount }})
                        </button>
                    </div>

                    <!-- Select All Checkbox -->
                    <div class="mb-4 flex items-center justify-start">
                        <label
                            class="flex cursor-pointer items-center text-sm font-medium text-gray-600"
                        >
                            <input
                                type="checkbox"
                                :checked="areAllStudentsSelected"
                                @change="toggleAllStudents"
                                class="form-checkbox mr-2 h-4 w-4 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            Select All
                        </label>
                    </div>

                   
                    <div
                        :class="[
                            showAllStudents && studentList.length >= STUDENT_SCROLL_THRESHOLD
                                ? 'max-h-[600px] overflow-y-auto'
                                : '',
                        ]"
                    >
                    <div
                        v-for="(student, index) in displayedStudents"
                        :key="student.name"
                        class="flex items-start justify-between border-t border-gray-100 py-3"
                        :class="{ 'border-t-0': index === 0 }"
                    >
                        <div class="flex items-start">
                            <input
                                type="checkbox"
                                v-model="student.isSelected"
                                class="form-checkbox mt-1 mr-3 h-5 w-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <div>
                                <p
                                    class="text-base font-semibold text-gray-900"
                                >
                                    {{ student.name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Request type: .
                                </p>
                            </div>
                        </div>
                        <!-- PENDING Badge -->
                        <span
                            class="flex-shrink-0 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold tracking-wider text-orange-600"
                        >
                            PENDING
                        </span>
                    </div>
<a
                            href="#"
                            v-if="studentList.length > STUDENT_COUNT"
                            @click.prevent="showAllStudents = !showAllStudents"
                            class="mt-4 self-start text-sm font-medium text-indigo-600 transition duration-150 hover:text-indigo-800"
                        >
                            {{
                                showAllStudents
                                    ? 'Show less'
                                    : 'View all student applications →'
                            }}
                        </a>
        
                </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

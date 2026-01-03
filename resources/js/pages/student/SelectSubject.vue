<script setup lang="ts">
import axios from 'axios';
import { Check, ChevronDown, Search } from 'lucide-vue-next';
import { computed, ref, onMounted} from 'vue';

interface SubjectRecord {
    id: number;
    code: string;
    name: string;
    units: number;
    department: string;
    isSelected: boolean;
    selectedTimeSlot?: string; 
}
interface Student {
    id: string;
    name: string;
    department: string;
    initials: string;
    selected: boolean;
}

const allSubjects = ref<SubjectRecord[]>([
    {
        id: 1,
        code: 'MATH101',
        name: 'Mathematics',
        units: 3,
        department: 'Mathematics',
        isSelected: true,
        selectedTimeSlot: '12:00 PM - 1:00 PM',
    },
    {
        id: 2,
        code: 'PHY202',
        name: 'Physics',
        units: 4,
        department: 'Physics',
        isSelected: false,
    },
    {
        id: 3,
        code: 'CS101',
        name: 'Computer Science',
        units: 3,
        department: 'Computer Science',
        isSelected: false,
    },
    {
        id: 4,
        code: 'ENGL210',
        name: 'English Literature',
        units: 3,
        department: 'English',
        isSelected: false,
    },
    {
        id: 5,
        code: 'ART101',
        name: 'Creative Arts',
        units: 2,
        department: 'Fine Arts',
        isSelected: false,
    },
]);

const searchQuery = ref('');

const subjectCategories = [
    'All Categories',
    'Mathematics',
    'Physics',
    'Computer Science',
    'English',
    'Fine Arts',
];
const creditValues = [
    'All Credits',
    '1 Credit',
    '2 Credits',
    '3 Credits',
    '4 Credits',
];

const selectedCategory = ref(subjectCategories[0]);
const selectedCreditValue = ref(creditValues[0]);

const timeSlots = [
    '9:00 AM - 10:00 AM',
    '10:00 AM - 11:00 AM',
    '11:00 AM - 12:00 PM',
    '12:00 PM - 1:00 PM', 
    '1:00 PM - 2:00 PM',
    '2:00 PM - 3:00 PM',
];

const filteredSubjects = computed(() => {
    return allSubjects.value.filter(
        (subject) =>
            subject.name
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            subject.code
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()),
    );
});

// Function to toggle subject selection
const toggleSubjectSelection = (subjectId: number) => {
    const subject = allSubjects.value.find((s) => s.id === subjectId);
    if (subject) {
        subject.isSelected = !subject.isSelected;
        // If selected, ensure a default time slot is set if none exists
        if (subject.isSelected && !subject.selectedTimeSlot) {
            subject.selectedTimeSlot = timeSlots[3]; // Default to 12:00 PM - 1:00 PM
        }
    }
};

const courses = [
    'All',
    'Computer Science',
    'Business Admin',
    'Engineering',
];
const studentYearLevels = [
    'All',
    '1st Year',
    '2nd Year',
    '3rd Year',
    '4th Year',
];
const toggleStudentSelection = (studentId: string) => {
    const student = studentsData.value.find((s) => s.id === studentId);
    if (student) {
        student.selected = !student.selected;
    }
};


const studentsData = ref<Student[]>([]);

const fetchStudents = async () => {
    try {
        console.log('[Students] Fetching students from backend...');

        const response = await axios.get('/students_controller');

        if (Array.isArray(response.data) && response.data.length > 0) {
            console.log(
                `[Students] Fetched ${response.data.length} students successfully`,
                response.data
            );
        } else {
            console.warn('[Students] Backend returned an empty list', response.data);
        }

        studentsData.value = response.data;
    } catch (error) {
        console.error('[Students] Failed to load students from backend', error);
    }
};


onMounted(fetchStudents);

const updateTimeSlot = (subject: SubjectRecord, newTime: string) => {
    subject.selectedTimeSlot = newTime;
};

const getSelectedSubjects = () =>
    allSubjects.value
        .filter((s) => s.isSelected)
        .map((s) => ({ id: s.id, selectedTimeSlot: s.selectedTimeSlot || '' }));

defineExpose({ getSelectedSubjects });
</script>

<template>
    <div class="font-sans">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="col-span-1">
                <div
                    class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-xl md:p-8 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h1 class="mb-6 text-2xl font-bold text-gray-900">
                        Select Subjects
                    </h1>

                    <div class="relative mb-4">
                        <Search
                            class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-gray-400"
                        />
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search by subject name or code..."
                            class="w-full rounded-lg border border-gray-200 py-3 pr-4 pl-10 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <div class="mb-6 flex space-x-3">
                        <div class="relative">
                            <select
                                v-model="selectedCategory"
                                class="cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white px-4 py-2 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                                <option
                                    v-for="category in subjectCategories"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ category }}
                                </option>
                            </select>
                            <ChevronDown
                                class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-500"
                            />
                        </div>

                        <div class="relative">
                            <select
                                v-model="selectedCreditValue"
                                class="cursor-pointer appearance-none rounded-lg border border-gray-200 bg-white px-4 py-2 pr-8 text-sm font-medium text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                                <option
                                    v-for="credit in creditValues"
                                    :key="credit"
                                    :value="credit"
                                >
                                    {{ credit }}
                                </option>
                            </select>
                            <ChevronDown
                                class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-500"
                            />
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto pr-2">
                        <div class="space-y-4">
                            <div
                                v-for="subject in filteredSubjects"
                                :key="subject.id"
                                class="rounded-xl border p-4 transition-all duration-200"
                                :class="{
                                    'border-blue-500 bg-blue-50 shadow-md':
                                        subject.isSelected,
                                    'border-gray-200 hover:border-gray-300':
                                        !subject.isSelected,
                                }"
                            >
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex w-full cursor-pointer items-center select-none"
                                        @click="
                                            toggleSubjectSelection(subject.id)
                                        "
                                    >
                                        <div
                                            class="mr-3 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded transition-colors"
                                            :class="
                                                subject.isSelected
                                                    ? 'border-blue-600 bg-blue-600'
                                                    : 'border border-gray-300 bg-white'
                                            "
                                        >
                                            <Check
                                                v-if="subject.isSelected"
                                                class="h-4 w-4 text-white"
                                            />
                                        </div>

                                        <div>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ subject.name }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ subject.code }} -
                                                {{ subject.units }} Credits
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="subject.isSelected"
                                    class="mt-4 border-t border-blue-200/70 pt-3"
                                >
                                    <p
                                        class="mb-2 text-sm font-medium text-gray-700"
                                    >
                                        Select Time Slot
                                    </p>
                                    <div class="relative">
                                        <select
                                            :value="subject.selectedTimeSlot"
                                            @change="
                                                (event) =>
                                                    updateTimeSlot(
                                                        subject,
                                                        (
                                                            event.target as HTMLSelectElement
                                                        ).value,
                                                    )
                                            "
                                            class="w-full cursor-pointer appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pr-10 pl-3 text-base text-gray-800 shadow-sm focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                        >
                                            <option
                                                v-for="slot in timeSlots"
                                                :key="slot"
                                                :value="slot"
                                            >
                                                {{ slot }}
                                            </option>
                                        </select>
                                        <ChevronDown
                                            class="pointer-events-none absolute top-1/2 right-3 h-5 w-5 -translate-y-1/2 text-gray-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-1">
                <div
                    class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-xl md:p-8 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        Manage Student Selection
                    </h2>

                    <div class="space-y-4">
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400"
                            />
                            <input
                                type="text"
                                placeholder="Search by student name or ID..."
                                class="w-full rounded-lg border-gray-300 py-2 pr-4 pl-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <div class="flex space-x-3">
                            <select
                                class="w-1/2 rounded-lg border-gray-300 py-2.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option disabled selected>
                                    Department: All
                                </option>
                                <option
                                    v-for="dept in courses"
                                    :key="dept"
                                    :value="dept"
                                >
                                    {{ dept }}
                                </option>
                            </select>
                            <select
                                class="w-1/2 rounded-lg border-gray-300 py-2.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option disabled selected>
                                    Year Level: All
                                </option>
                                <option
                                    v-for="year in studentYearLevels"
                                    :key="year"
                                    :value="year"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="mt-6 max-h-96 space-y-3 overflow-y-auto border-t border-gray-200 pt-4 pr-2 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between p-2">
                            <label
                                class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-indigo-500"
                                />
                                <span>Select All</span>
                            </label>
                        </div>

                        <div
                            v-for="student in studentsData"
                            :key="student.id"
                            class="flex items-center justify-between rounded-lg border border-gray-200 p-3 transition duration-150 dark:border-gray-700"
                            :class="{
                                'border-indigo-300 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/20':
                                    student.selected,
                                'hover:bg-gray-50 dark:hover:bg-gray-700/50':
                                    !student.selected,
                            }"
                        >
                            <div
                                class="flex items-center space-x-3"
                                @click="toggleStudentSelection(student.id)"
                            >
                                <input
                                    type="checkbox"
                                    :checked="student.selected"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-indigo-500"
                                />
                                <div
                                    class="flex size-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-white dark:bg-gray-600"
                                >
                                    {{ student.initials }}
                                </div>
                                <div>
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ student.name }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        ID: {{ student.id }} |
                                        {{ student.department }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>

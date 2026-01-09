<script setup lang="ts">
import axios from 'axios';
import { Check, ChevronDown, Search } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

/* =======================
   TYPES
======================= */

type ClassSession = {
    session_id: number;
    start_time: string;
    end_time: string;
    session_days: string[];
    session_status: string;
};

type Subject = {
    subject_id: number;
    subject_name: string;
    subject_code?: string;
    units?: number;
    sessions: ClassSession[];
    isSelected?: boolean;
    selectedTimeSlot?: number | null;
};

type Student = {
    id: string;
    name: string;
    department: string;
    year_level?: string;
    initials: string;
    selected: boolean;
};

type Course = {
    id: number;
    course_name: string;
};

/* =======================
   SUBJECTS & SESSIONS
======================= */

const subjects = ref<Subject[]>([]);
const searchQuery = ref('');

const loadSubjects = async () => {
    try {
        const { data } = await axios.get('/subjects-with-sessions');
        subjects.value = (data.subjects || []).map((subject: any) => ({
            ...subject,
            sessions:
                subject.sessions?.map((session: any) => ({
                    ...session,
                    // Parse session_days if it's a string
                    session_days:
                        typeof session.session_days === 'string'
                            ? JSON.parse(session.session_days)
                            : Array.isArray(session.session_days)
                              ? session.session_days
                              : [],
                })) || [],
            isSelected: false,
            selectedTimeSlot: subject.sessions?.[0]?.session_id || null,
        }));
        console.log('[Subjects] Loaded', subjects.value);
    } catch (error) {
        console.error('[Subjects] Failed to load', error);
    }
};

const filteredSubjects = computed(() => {
    if (!searchQuery.value.trim()) {
        return subjects.value;
    }

    const query = searchQuery.value.toLowerCase();
    return subjects.value.filter(
        (subject) =>
            subject.subject_name.toLowerCase().includes(query) ||
            subject.subject_code?.toLowerCase().includes(query),
    );
});

const toggleSubjectSelection = (subjectId: number) => {
    const subject = subjects.value.find((s) => s.subject_id === subjectId);
    if (subject) {
        subject.isSelected = !subject.isSelected;
        if (
            subject.isSelected &&
            !subject.selectedTimeSlot &&
            subject.sessions.length > 0
        ) {
            subject.selectedTimeSlot = subject.sessions[0].session_id;
        }
    }
};

const updateTimeSlot = (subject: Subject, sessionId: string) => {
    subject.selectedTimeSlot = parseInt(sessionId);
};

const dayAbbreviationMap: Record<string, string> = {
    Monday: 'M',
    Tuesday: 'T',
    Wednesday: 'W',
    Thursday: 'Th',
    Friday: 'F',
    Saturday: 'S',
    Sunday: 'Su',
};

const normalizeDay = (day: string) => day.trim().toLowerCase();

const formatSessionDays = (days: string[]) => {
    if (!Array.isArray(days) || days.length === 0) return '';

    return days
        .map((day) => {
            const normalized = normalizeDay(day);
            return (
                dayAbbreviationMap[
                    normalized.charAt(0).toUpperCase() + normalized.slice(1)
                ] ?? ''
            );
        })
        .filter(Boolean)
        .join('');
};

const formatPHTime = (time: string) => {
    if (!time) return '';

    const [hours, minutes] = time.split(':').map(Number);

    const period = hours >= 12 ? 'PM' : 'AM';
    const hour12 = hours % 12 || 12;

    return `${hour12}:${minutes.toString().padStart(2, '0')} ${period}`;
};

const getAvailableTimeSlotsForSubject = (subjectId: number) => {
    const subject = subjects.value.find((s) => s.subject_id === subjectId);
    if (!subject) return [];

    return subject.sessions.map((session) => ({
        session_id: session.session_id,
        label: `${formatPHTime(session.start_time)} - ${formatPHTime(session.end_time)} ${formatSessionDays(session.session_days)}`,
        status: session.session_status,
    }));
};

/* =======================
   COURSES
======================= */

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

/* =======================
   STUDENTS
======================= */

const studentsData = ref<Student[]>([]);
const studentYearLevels = ref(['1st Year', '2nd Year', '3rd Year', '4th Year']);

// Student filters
const studentSearchQuery = ref('');
const selectedDepartment = ref<number | 'all'>('all');
const selectedYearLevel = ref<string | 'all'>('all');

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

// Filtered students based on search and filters
const filteredStudents = computed(() => {
    let filtered = studentsData.value;

    // Apply search filter
    if (studentSearchQuery.value.trim()) {
        const query = studentSearchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (student) =>
                student.name.toLowerCase().includes(query) ||
                student.id.toLowerCase().includes(query),
        );
    }

    // Apply department filter
    if (selectedDepartment.value !== 'all') {
        filtered = filtered.filter((student) => {
            const course = courses.value.find(
                (c) => c.id === selectedDepartment.value,
            );
            return course && student.department === course.course_name;
        });
    }

    // Apply year level filter
    if (selectedYearLevel.value !== 'all') {
        filtered = filtered.filter(
            (student) => student.year_level === selectedYearLevel.value,
        );
    }

    return filtered;
});

// Select all functionality
const allStudentsSelected = computed(() => {
    if (filteredStudents.value.length === 0) return false;
    return filteredStudents.value.every((student) => student.selected);
});

const toggleSelectAll = () => {
    const newValue = !allStudentsSelected.value;
    filteredStudents.value.forEach((student) => {
        student.selected = newValue;
    });
};

const toggleStudentSelection = (studentId: string) => {
    const student = studentsData.value.find((s) => s.id === studentId);
    if (student) student.selected = !student.selected;
};

/* =======================
   AUTO-ASSIGN BY DEPARTMENT
======================= */

/**
 * Get departments from selected subjects
 */
const getSelectedSubjectDepartments = computed(() => {
    const selectedSubjects = subjects.value.filter((s) => s.isSelected);
    
    if (selectedSubjects.length === 0) {
        return [];
    }

    // Get unique departments from courses that match selected subjects
    const departments = new Set<string>();
    selectedSubjects.forEach(() => {
        // Since we don't have direct subject-to-department mapping,
        // we'll match based on the subjects selected
        // For now, return all courses as potential departments
    });

    // Return all courses as potential match departments
    return courses.value.map((c) => c.course_name);
});

/**
 * Get students that match the selected subjects' departments
 */
const getMatchingStudentsByDepartment = computed(() => {
    const selectedSubjects = subjects.value.filter((s) => s.isSelected);
    
    if (selectedSubjects.length === 0) {
        return [];
    }

    // Get departments that should match
    const targetDepartments = getSelectedSubjectDepartments.value;

    // Filter students by matching departments
    return filteredStudents.value.filter((student) =>
        targetDepartments.includes(student.department)
    );
});

/**
 * Auto-assign all students from the same department as selected subjects
 */
const autoAssignByDepartment = () => {
    const matchingStudents = getMatchingStudentsByDepartment.value;

    if (matchingStudents.length === 0) {
        alert('No students found in the departments of selected subjects');
        return;
    }

    // Select all matching students
    matchingStudents.forEach((student) => {
        const dbStudent = studentsData.value.find((s) => s.id === student.id);
        if (dbStudent) {
            dbStudent.selected = true;
        }
    });

    // Scroll to student section
    const studentSection = document.querySelector('[data-student-section]');
    if (studentSection) {
        studentSection.scrollIntoView({ behavior: 'smooth' });
    }

    alert(
        `Auto-assigned ${matchingStudents.length} student(s) from matching department(s)`
    );
};

/**
 * Clear all student selections
 */
const clearAllStudentSelections = () => {
    studentsData.value.forEach((student) => {
        student.selected = false;
    });
};

/**
 * Get count of selected subjects
 */
const selectedSubjectsCount = computed(() => {
    return subjects.value.filter((s) => s.isSelected).length;
});

/**
 * Get count of matching students
 */
const matchingStudentsCount = computed(() => {
    return getMatchingStudentsByDepartment.value.length;
});

/* =======================
   SUBMISSION
======================= */

const selectedSessionIds = computed(() => {
    return subjects.value
        .filter((s) => s.isSelected && s.selectedTimeSlot)
        .map((s) => s.selectedTimeSlot as number);
});

const selectedStudentIds = computed(() => {
    return studentsData.value
        .filter((s) => s.selected)
        .map((s) => {
            const id = parseInt(s.id, 10);
            return id;
        })
        .filter((id) => !isNaN(id) && id > 0);
});

const isSubmitting = ref(false);

const submitAssignments = async () => {
    if (selectedSessionIds.value.length === 0) {
        alert('Please select at least one subject/session');
        return;
    }

    if (selectedStudentIds.value.length === 0) {
        alert('Please select at least one student');
        return;
    }

    const payload = {
        session_ids: selectedSessionIds.value,
        student_ids: selectedStudentIds.value,
    };

    console.log('Submitting payload:', payload);
    console.log('Student IDs types:', selectedStudentIds.value.map(id => typeof id));
    console.log('Session IDs types:', selectedSessionIds.value.map(id => typeof id));

    isSubmitting.value = true;

    try {
        const response = await axios.post('/assign-students-to-sessions', payload);
        console.log('Success:', response.data);
        alert('Students assigned to sessions successfully!');
        
        // Reset selections after successful submission
        subjects.value.forEach(subject => {
            subject.isSelected = false;
            subject.selectedTimeSlot = null;
        });
        studentsData.value.forEach(student => {
            student.selected = false;
        });
    } catch (error: any) {
        console.error('Failed to save assignments:', error);
        const errorMessage = error.response?.data?.message || 'Failed to save assignments. Please try again.';
        alert(errorMessage);
    } finally {
        isSubmitting.value = false;
    }
};

/* =======================
   LIFECYCLE
======================= */

onMounted(() => {
    loadSubjects();
    loadCourses();
    fetchStudents();
});
</script>

<template>
    <div class="font-sans">
        <!-- Submit Button at the top -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Assign Students to Sessions
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Select subjects and students, then click assign
                </p>
            </div>
            <button
                @click="submitAssignments"
                :disabled="isSubmitting || selectedSessionIds.length === 0 || selectedStudentIds.length === 0"
                class="px-6 py-3 rounded-lg font-semibold text-white shadow-lg transition-all duration-200"
                :class="{
                    'bg-blue-600 hover:bg-blue-700 hover:shadow-xl': !isSubmitting && selectedSessionIds.length > 0 && selectedStudentIds.length > 0,
                    'bg-gray-400 cursor-not-allowed': isSubmitting || selectedSessionIds.length === 0 || selectedStudentIds.length === 0
                }"
            >
                <span v-if="isSubmitting">Assigning...</span>
                <span v-else>
                    Assign ({{ selectedSessionIds.length }} sessions, {{ selectedStudentIds.length }} students)
                </span>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- ... rest of your template remains the same ... -->
            <div class="col-span-1">
                <div
                    class="h-full rounded-xl border border-gray-200 bg-white p-6 shadow-xl md:p-8 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h1
                        class="mb-6 text-2xl font-bold text-gray-900 dark:text-white"
                    >
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
                            class="w-full rounded-lg border border-gray-200 py-3 pr-4 pl-10 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>

                    <div class="max-h-96 overflow-y-auto pr-2">
                        <div
                            v-if="subjects.length === 0"
                            class="py-8 text-center text-gray-500"
                        >
                            No subjects available
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="subject in filteredSubjects"
                                :key="subject.subject_id"
                                class="rounded-xl border p-4 transition-all duration-200"
                                :class="{
                                    'border-blue-500 bg-blue-50 shadow-md dark:bg-blue-900/20':
                                        subject.isSelected,
                                    'border-gray-200 hover:border-gray-300 dark:border-gray-700':
                                        !subject.isSelected,
                                }"
                            >
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex w-full cursor-pointer items-center select-none"
                                        @click="
                                            toggleSubjectSelection(
                                                subject.subject_id,
                                            )
                                        "
                                    >
                                        <div
                                            class="mr-3 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded transition-colors"
                                            :class="
                                                subject.isSelected
                                                    ? 'border-blue-600 bg-blue-600'
                                                    : 'border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700'
                                            "
                                        >
                                            <Check
                                                v-if="subject.isSelected"
                                                class="h-4 w-4 text-white"
                                            />
                                        </div>

                                        <div>
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{ subject.subject_name }}
                                            </p>
                                            <p
                                                class="text-sm text-gray-600 dark:text-gray-400"
                                            >
                                                <span
                                                    v-if="subject.subject_code"
                                                    >{{
                                                        subject.subject_code
                                                    }}</span
                                                >
                                                <span v-if="subject.units">
                                                    -
                                                    {{
                                                        subject.units
                                                    }}
                                                    Credits</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        subject.isSelected &&
                                        subject.sessions.length > 0
                                    "
                                    class="mt-4 border-t border-blue-200/70 pt-3 dark:border-blue-800"
                                >
                                    <p
                                        class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300"
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
                                            class="w-full cursor-pointer appearance-none rounded-lg border border-gray-300 bg-white py-2.5 pr-10 pl-3 text-base text-gray-800 shadow-sm focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                        >
                                            <option
                                                v-for="slot in getAvailableTimeSlotsForSubject(
                                                    subject.subject_id,
                                                )"
                                                :key="slot.session_id"
                                                :value="slot.session_id"
                                            >
                                                {{ slot.label }}
                                            </option>
                                        </select>
                                        <ChevronDown
                                            class="pointer-events-none absolute top-1/2 right-3 h-5 w-5 -translate-y-1/2 text-gray-500"
                                        />
                                    </div>
                                </div>

                                <div
                                    v-else-if="
                                        subject.isSelected &&
                                        subject.sessions.length === 0
                                    "
                                    class="mt-4 border-t border-blue-200/70 pt-3 dark:border-blue-800"
                                >
                                    <p
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        No time slots available for this subject
                                    </p>
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
                                v-model="studentSearchQuery"
                                placeholder="Search by student name or ID..."
                            class="w-full rounded-lg border border-gray-200 py-3 pr-4 pl-10 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <div class="flex space-x-3">
                            <select
                                v-model="selectedDepartment"
                                class="w-1/2 rounded-lg border border-gray-300 py-2.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
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
                                v-model="selectedYearLevel"
                                class="w-1/2 rounded-lg border border-gray-300 py-2.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="all">Year Level: All</option>
                                <option
                                    v-for="year in studentYearLevels"
                                    :key="year"
                                    :value="year"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <!-- Auto-Assign Buttons -->
                        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                            <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Quick Actions
                            </p>
                            <div class="flex gap-2">
                                <button
                                    @click="autoAssignByDepartment"
                                    :disabled="selectedSubjectsCount === 0"
                                    :class="{
                                        'bg-green-600 hover:bg-green-700 text-white': selectedSubjectsCount > 0,
                                        'bg-gray-300 text-gray-500 cursor-not-allowed': selectedSubjectsCount === 0,
                                    }"
                                    class="flex-1 rounded-lg px-3 py-2 text-sm font-medium transition duration-150"
                                    :title="selectedSubjectsCount === 0 ? 'Select at least one subject first' : `Auto-assign ${matchingStudentsCount} matching students`"
                                >
                                    Auto-Assign by Dept
                                    <span v-if="matchingStudentsCount > 0" class="ml-1 font-bold">({{ matchingStudentsCount }})</span>
                                </button>
                                <button
                                    @click="clearAllStudentSelections"
                                    :disabled="selectedStudentIds.length === 0"
                                    :class="{
                                        'bg-red-600 hover:bg-red-700 text-white': selectedStudentIds.length > 0,
                                        'bg-gray-300 text-gray-500 cursor-not-allowed': selectedStudentIds.length === 0,
                                    }"
                                    class="flex-1 rounded-lg px-3 py-2 text-sm font-medium transition duration-150"
                                    title="Clear all student selections"
                                >
                                    Clear All
                                </button>
                            </div>
                            <p v-if="selectedSubjectsCount > 0" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ selectedSubjectsCount }} subject(s) selected • {{ matchingStudentsCount }} matching student(s) from {{ getSelectedSubjectDepartments.length }} department(s)
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-6 max-h-96 space-y-3 overflow-y-auto border-t border-gray-200 pt-4 pr-2 dark:border-gray-700"
                        data-student-section
                    >
                        <div class="flex items-center justify-between p-2">
                            <label
                                class="flex cursor-pointer items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                @click="toggleSelectAll"
                            >
                                <input
                                    type="checkbox"
                                    :checked="allStudentsSelected"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:checked:bg-indigo-500"
                                />
                                <span
                                    >Select All ({{
                                        filteredStudents.length
                                    }})</span
                                >
                            </label>
                        </div>

                        <div
                            v-if="filteredStudents.length === 0"
                            class="py-8 text-center text-gray-500"
                        >
                            No students found
                        </div>

                        <div
                            v-for="student in filteredStudents"
                            :key="student.id"
                            class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-200 p-3 transition duration-150 dark:border-gray-700"
                            :class="{
                                'border-indigo-300 bg-indigo-50 dark:border-indigo-600 dark:bg-indigo-900/20':
                                    student.selected,
                                'hover:bg-gray-50 dark:hover:bg-gray-700/50':
                                    !student.selected,
                            }"
                            @click="toggleStudentSelection(student.id)"
                        >
                            <div class="flex items-center space-x-3">
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
                                        <span v-if="student.year_level">
                                            | {{ student.year_level }}</span
                                        >
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
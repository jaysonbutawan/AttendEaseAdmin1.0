<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {  type Teacher } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { LayoutGrid, Users, Plus,Search, Filter } from 'lucide-vue-next';
const PRIMARY_COLOR_RGB = '79, 57, 246'; // rgba(79, 57, 246)

// Dummy data structure for the table
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

// Dummy data for form selections
const departments = ['Mathematics', 'Science', 'English', 'History'];
const availableTeachers = ['Select Teacher', 'Dr. Helena Vance', 'Lia Torres', 'Sarah Johnson'];
const rooms = ['Room 101', 'Room 102', 'Room 201', 'Room 202'];

const assignForm = ref({
    subjectName: '',
    department: '',
    room: '',
    teacher: '',
});

const search = ref('');
const handleAssignTeacher = () => {
    console.log('Assigning teacher:', assignForm.value);
    alert('Teacher assigned successfully (mock action)!');
    assignForm.value = {
        subjectName: '',
        department: '',
        room: '',
        teacher: '',
    };
};
</script>

<template>
    <Head title="Teacher Management" />

    <AppLayout >
        <div class="flex h-full flex-1 flex-col gap-6 p-6 md:p-8">
            <!-- Header Section -->
            <header>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Teacher Management</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Manage teacher assignments and view teacher details.
                </p>
            </header>

            <!-- STATISTIC CARDS -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Card 1: Total Teachers -->
                <div 
                    class="rounded-xl p-6 shadow-lg transition duration-300 ease-in-out"
                    :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.9)`, color: 'white' }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium opacity-80">Total Teachers</p>
                            <p class="text-4xl font-extrabold mt-1">24</p>
                        </div>
                        <Users class="size-10 opacity-70" />
                    </div>
                </div>

                <!-- Card 2: Assigned Classes -->
                <div 
                    class="rounded-xl p-6 shadow-lg transition duration-300 ease-in-out"
                    :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.8)`, color: 'white' }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium opacity-80">Assigned Classes</p>
                            <p class="text-4xl font-extrabold mt-1">42</p>
                        </div>
                        <LayoutGrid class="size-10 opacity-70" />
                    </div>
                </div>

                <!-- Card 3: Available for Assignment -->
                <div 
                    class="rounded-xl p-6 shadow-lg transition duration-300 ease-in-out"
                    :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.7)`, color: 'white' }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium opacity-80">Available for Assignment</p>
                            <p class="text-4xl font-extrabold mt-1">5</p>
                        </div>
                        <Plus class="size-10 opacity-70" />
                    </div>
                </div>
            </div>

            <!-- ASSIGN TEACHER FORM (WHITE CARD) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 md:p-8 border border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-white">Assign Teacher to Class</h2>

                <form @submit.prevent="handleAssignTeacher" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Subject Name -->
                    <div class="md:col-span-1">
                        <label for="subjectName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject Name</label>
                        <input
                            id="subjectName"
                            v-model="assignForm.subjectName"
                            type="text"
                            placeholder="Enter subject name"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>

                    <!-- Department -->
                    <div class="md:col-span-1">
                        <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <select
                            id="department"
                            v-model="assignForm.department"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="" disabled>Select Department</option>
                            <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                        </select>
                    </div>

                    <!-- Room -->
                    <div class="md:col-span-1">
                        <label for="room" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Room</label>
                        <select
                            id="room"
                            v-model="assignForm.room"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="" disabled>Select Room</option>
                            <option v-for="room in rooms" :key="room" :value="room">{{ room }}</option>
                        </select>
                    </div>
                    
                    <!-- Teacher Selection -->
                    <div class="md:col-span-1">
                        <label for="teacher" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Teacher</label>
                        <select
                            id="teacher"
                            v-model="assignForm.teacher"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="" disabled>-- Select Teacher --</option>
                            <option v-for="teacher in availableTeachers" :key="teacher" :value="teacher">{{ teacher }}</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="md:col-span-4 flex justify-end space-x-3 mt-4">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            Cancel
                        </button>
                       <button 
                        type="submit"
                        class="flex items-center space-x-2 px-4 py-2 text-sm font-semibold rounded-lg text-white shadow-md transition duration-150 ease-in-out"
                        :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 1)` }"
                        :class="`hover:bg-[rgba(${PRIMARY_COLOR_RGB},0.8)]`"
                        >
                            <Plus class="h-4 w-4" />
                            <span>Assign Teacher</span>
                        </button>

                    </div>
                </form>
            </div>

            <!-- ALL TEACHERS TABLE (WHITE CARD) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 md:p-8 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold flex items-center space-x-2 text-gray-900 dark:text-white">
                        <Users class="h-5 w-5" :style="{ color: `rgb(${PRIMARY_COLOR_RGB})` }" />
                        <span>All Teachers</span>
                    </h2>

                    <div class="flex space-x-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search teachers..."
                                class="pl-10 pr-4 py-2 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>

                        <!-- Filter Button -->
                        <button 
                            class="flex items-center space-x-2 px-4 py-2 text-sm font-medium rounded-lg border shadow-sm transition duration-150"
                            :style="{ borderColor: `rgba(${PRIMARY_COLOR_RGB}, 0.6)`, color: `rgb(${PRIMARY_COLOR_RGB})` }"
                        >
                            <Filter class="h-4 w-4" />
                            <span>Filter</span>
                        </button>
                    </div>
                </div>

                <!-- Teacher List Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    TEACHER NAME
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    DEPARTMENT
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    ASSIGNED SUBJECTS
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    STATUS
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="teacher in teachersData" :key="teacher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <!-- Teacher Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div 
                                            class="flex-shrink-0 size-10 rounded-full flex items-center justify-center font-bold text-white text-sm"
                                            :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.8)` }"
                                        >
                                            {{ teacher.initials }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ teacher.name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ teacher.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Department -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium" 
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400': teacher.department === 'Science',
                                            'bg-indigo-100 text-indigo-800 dark:bg-indigo-800/20 dark:text-indigo-400': teacher.department === 'Mathematics',
                                            'bg-pink-100 text-pink-800 dark:bg-pink-800/20 dark:text-pink-400': teacher.department === 'English',
                                        }"
                                    >
                                        {{ teacher.department }}
                                    </span>
                                </td>
                                <!-- Assigned Subjects -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="subject in teacher.assignedSubjects" :key="subject.id"
                                            class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-200"
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
                                            'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-400': teacher.status === 'Active',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400': teacher.status === 'On Leave',
                                        }"
                                    >
                                        {{ teacher.status }}
                                    </span>
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition flex items-center space-x-1">
                                            <span>Edit</span>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition flex items-center space-x-1">
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-6 text-sm text-gray-600 dark:text-gray-400">
                    <div>Showing 1 to {{ teachersData.length }} of 24 teachers</div>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Previous</button>
                        <button 
                            class="px-3 py-1 rounded-lg font-bold text-white transition"
                            :style="{ backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 1)` }"
                        >1</button>
                        <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">2</button>
                        <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">3</button>
                        <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optional: Adding custom styles for input focus based on the primary color if Tailwind styles aren't enough */
.focus\\:border-indigo-500:focus {
    border-color: rgb(79, 57, 246) !important;
}
.focus\\:ring-indigo-500:focus {
    --tw-ring-color: rgb(79, 57, 246) !important;
}
</style>
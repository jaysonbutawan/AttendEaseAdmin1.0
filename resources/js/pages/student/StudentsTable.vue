<script setup lang="ts">
import { ref, computed } from 'vue';
import { Plus, Search, Pencil, Trash2 } from 'lucide-vue-next'; // Lucide icons

// --- DATA STRUCTURES (MOCK DATA) ---

interface StudentRecord {
    id: number;
    studentId: string;
    name: string;
    email: string;
    department: string;
    yearLevel: string;
    isSelected: boolean;
}

const allStudents = ref<StudentRecord[]>([
    { id: 1, studentId: 'STU-00123', name: 'Alex Johnson', email: 'alex.j@school.com', department: 'Computer Science', yearLevel: '3rd Year', isSelected: false },
    { id: 2, studentId: 'STU-00124', name: 'Maria Garcia', email: 'maria.g@school.com', department: 'Fine Arts', yearLevel: '2nd Year', isSelected: false },
    { id: 3, studentId: 'STU-00125', name: 'David Smith', email: 'david.s@school.com', department: 'Engineering', yearLevel: '4th Year', isSelected: false },
    { id: 4, studentId: 'STU-00126', name: 'Emily White', email: 'emily.w@school.com', department: 'Business', yearLevel: '1st Year', isSelected: false },
    { id: 5, studentId: 'STU-00127', name: 'Chris Lee', email: 'chris.l@school.com', department: 'Computer Science', yearLevel: '3rd Year', isSelected: false },
]);

const totalResults = 50;
const resultsPerPage = 5;
const currentPage = ref(1);

// --- COMPUTED PROPERTIES ---

const paginatedStudents = computed(() => {
    // In a real app, pagination and filtering would be handled by a backend API.
    // This is a simple front-end slicing for display purposes.
    const start = (currentPage.value - 1) * resultsPerPage;
    const end = start + resultsPerPage;
    return allStudents.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(totalResults / resultsPerPage));

const pageNumbers = computed(() => {
    // Logic for displaying limited pagination numbers (e.g., 1, 2, ..., 10)
    const pages: (number | '...')[] = [];
    const maxVisiblePages = 5;

    if (totalPages.value <= maxVisiblePages) {
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        // Simple logic for first, ellipses, and last pages
        pages.push(1);
        if (currentPage.value > 2) pages.push('...');
        if (currentPage.value > 1 && currentPage.value < totalPages.value) pages.push(currentPage.value);
        if (currentPage.value < totalPages.value - 1) pages.push('...');
        pages.push(totalPages.value);

        // Filter out duplicate consecutive '...' and ensure page numbers are unique
        const uniquePages: (number | '...')[] = [];
        let lastItem: number | '...' | undefined = undefined;
        for (const item of pages) {
            if (item === '...' && lastItem === '...') continue;
            if (typeof item === 'number' && uniquePages.includes(item)) continue;
            uniquePages.push(item);
            lastItem = item;
        }
        return uniquePages;
    }

    return pages;
});

const selectAllTableStudents = computed({
    get: () => allStudents.value.every(s => s.isSelected),
    set: (value) => {
        allStudents.value.forEach(s => s.isSelected = value);
    }
});

const showingRange = computed(() => {
    const start = (currentPage.value - 1) * resultsPerPage + 1;
    const end = Math.min(currentPage.value * resultsPerPage, totalResults);
    return `Showing **${start} to ${end}** of **${totalResults}** results`;
});

// --- METHODS ---

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

const addNewStudent = () => {
    alert('Opening modal to add a new student (Mock Action)');
};

const editStudent = (id: number) => {
    console.log(`Editing student with ID: ${id}`);
    alert(`Editing student ${id} (Mock Action)`);
};

const deleteStudent = (id: number) => {
    console.log(`Deleting student with ID: ${id}`);
    if (confirm(`Are you sure you want to delete student ID ${id}?`)) {
    }
};
</script>

<template>
    <div class="pt-0 px-6 pb-6 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center mb-2">
            <h1 class="text-3xl font-bold text-gray-900">Students</h1>
            <button @click="addNewStudent"
                class="flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                <Plus class="w-5 h-5 mr-2" />
                Add New Student
            </button>
        </div>

        <p class="text-gray-600 mb-6">Manage student profiles, records, and information.</p>

        <div class="flex space-x-4 mb-8 bg-white p-4 rounded-lg shadow">
            <div class="relative flex-grow">
                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input type="text" placeholder="Search by name, ID, or email..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500">
                <option>Department: All</option>
                <option>Computer Science</option>
                <option>Fine Arts</option>
                <option>Engineering</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500">
                <option>Year Level: All</option>
                <option>1st Year</option>
                <option>2nd Year</option>
                <option>3rd Year</option>
                <option>4th Year</option>
            </select>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4 text-left">
                            <input type="checkbox" v-model="selectAllTableStudents"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="p-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student ID</th>
                        <th class="p-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="p-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="p-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                        <th class="p-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Year Level</th>
                        <th class="p-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="student in paginatedStudents" :key="student.id" class="hover:bg-blue-50">
                        <td class="p-4 whitespace-nowrap">
                            <input type="checkbox" v-model="student.isSelected"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="p-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ student.studentId }}</td>
                        <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ student.name }}</td>
                        <td class="p-4 whitespace-nowrap text-sm text-blue-600">{{ student.email }}</td>
                        <td class="p-4 whitespace-nowrap text-sm text-gray-700">{{ student.department }}</td>
                        <td class="p-4 whitespace-nowrap text-sm text-gray-700">{{ student.yearLevel }}</td>
                        <td class="p-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                            <button @click="editStudent(student.id)" title="Edit"
                                class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-gray-100 transition duration-150">
                                <Pencil class="w-5 h-5" />
                            </button>
                            <button @click="deleteStudent(student.id)" title="Delete"
                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-gray-100 transition duration-150">
                                <Trash2 class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between items-center p-4 border-t bg-white">
                <span class="text-sm text-gray-700" v-html="showingRange"></span>
                <div class="flex space-x-1">
                    <button @click="prevPage" :disabled="currentPage === 1"
                        :class="['px-3 py-1 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition duration-150', { 'cursor-not-allowed opacity-50': currentPage === 1 }]">
                        &lt;
                    </button>

                    <button v-for="(page, index) in pageNumbers" :key="index" @click="goToPage(page)"
                        :class="[
                            'px-4 py-1 rounded-lg transition duration-150',
                            page === currentPage
                                ? 'text-white bg-blue-600 border border-blue-600'
                                : page === '...'
                                    ? 'text-gray-500 cursor-default'
                                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-100'
                        ]">
                        {{ page }}
                    </button>

                    <button @click="nextPage" :disabled="currentPage === totalPages"
                        :class="['px-3 py-1 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition duration-150', { 'cursor-not-allowed opacity-50': currentPage === totalPages }]">
                        &gt;
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
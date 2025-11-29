<script setup lang="ts">
import { ref, computed } from 'vue';
import { Check, X, Search, ChevronDown, Plus, LayoutGrid, Users, BookOpen } from 'lucide-vue-next'; // Lucide icons

// --- DATA STRUCTURES (MOCK DATA) ---

interface PendingItem {
    id: number;
    name: string;
    avatarUrl: string;
    requestedTime: string;
    type: 'Student' | 'Teacher';
}

interface Student {
    id: number;
    name: string;
    avatarUrl: string;
    major: string;
    studentId: string;
    isSelected: boolean;
}

interface Subject {
    id: number;
    name: string;
    code: string;
    credits: number;
}

const pendingApprovals = ref<PendingItem[]>([
    { id: 1, name: 'John Doe', avatarUrl: 'https://i.pravatar.cc/40?img=1', requestedTime: '2 hours ago', type: 'Student' },
    { id: 2, name: 'Jane Smith', avatarUrl: 'https://i.pravatar.cc/40?img=2', requestedTime: '5 hours ago', type: 'Student' },
    { id: 3, name: 'Mike Johnson', avatarUrl: 'https://i.pravatar.cc/40?img=3', requestedTime: '1 day ago', type: 'Student' },
    { id: 4, name: 'Teacher A', avatarUrl: 'https://i.pravatar.cc/40?img=8', requestedTime: '1 day ago', type: 'Teacher' },
    { id: 5, name: 'Teacher B', avatarUrl: 'https://i.pravatar.cc/40?img=9', requestedTime: '2 days ago', type: 'Teacher' },
]);

const students = ref<Student[]>([
    { id: 101, name: 'Alex Johnson', avatarUrl: 'https://i.pravatar.cc/40?img=4', major: 'CS', studentId: '12345', isSelected: true },
    { id: 102, name: 'Maria Garcia', avatarUrl: 'https://i.pravatar.cc/40?img=5', major: 'BA', studentId: '12346', isSelected: false },
    { id: 103, name: 'David Smith', avatarUrl: 'https://i.pravatar.cc/40?img=6', major: 'Eng', studentId: '12347', isSelected: false },
    { id: 104, name: 'Emily White', avatarUrl: 'https://i.pravatar.cc/40?img=7', major: 'Bus', studentId: '12348', isSelected: false },
]);

const availableSubjects = ref<Subject[]>([
    { id: 201, name: 'Introduction to Algorithms', code: 'CS101', credits: 3 },
    { id: 202, name: 'Data Structures', code: 'CS202', credits: 4 },
    { id: 203, name: 'Calculus II', code: 'MATH150', credits: 3 },
    { id: 204, name: 'Marketing Principles', code: 'BUS210', credits: 3 },
]);

const studentSearchTerm = ref('');
const subjectSearchTerm = ref('');
const selectedPendingType = ref<'Student' | 'Teacher'>('Student');
const selectAllStudents = computed({
    get: () => students.value.every(s => s.isSelected),
    set: (value) => {
        students.value.forEach(s => s.isSelected = value);
    }
});

// --- COMPUTED PROPERTIES ---

const filteredPendingApprovals = computed(() => {
    return pendingApprovals.value.filter(item => item.type === selectedPendingType.value);
});

const studentCount = computed(() => pendingApprovals.value.filter(item => item.type === 'Student').length);
const teacherCount = computed(() => pendingApprovals.value.filter(item => item.type === 'Teacher').length);
const selectedStudentCount = computed(() => students.value.filter(s => s.isSelected).length);

// --- METHODS ---

const handleApprove = (id: number) => {
    console.log(`Approving item with ID: ${id}`);
    // Logic to remove item from pendingApprovals list in a real app
};

const handleReject = (id: number) => {
    console.log(`Rejecting item with ID: ${id}`);
    // Logic to remove item from pendingApprovals list in a real app
};

const assignSubjects = () => {
    const selectedStudents = students.value.filter(s => s.isSelected).map(s => s.id);
    console.log('Attempting to assign subjects to student IDs:', selectedStudents);
    alert(`Attempting to assign subjects to ${selectedStudentCount.value} student(s) (Mock Action)`);
};

const selectSubject = (subjectId: number) => {
    console.log(`Subject ID ${subjectId} selected. Ready to assign.`);
    // In a real app, this might open a modal or add the subject to a list of subjects to be assigned.
};
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Subject Assignment</h1>
            <button @click="assignSubjects"
                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                Assign Subject(s) ({{ selectedStudentCount }})
            </button>
        </div>
        <p class="text-gray-600 mb-6">Assign subjects to one or more students using the panels below.</p>

        <div class="grid grid-cols-12 gap-6">

            <div class="col-span-12 md:col-span-3">
                <div class="bg-white p-4 rounded-lg shadow-lg h-full">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">🔔 Pending Approvals</h2>
                    <div class="flex border-b mb-3 text-sm text-gray-600">
                        <button @click="selectedPendingType = 'Student'"
                            :class="[
                                'pb-2 mr-4 font-medium transition duration-150',
                                selectedPendingType === 'Student' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
                            ]">
                            Students ({{ studentCount }})
                        </button>
                        <button @click="selectedPendingType = 'Teacher'"
                            :class="[
                                'pb-2 font-medium transition duration-150',
                                selectedPendingType === 'Teacher' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'
                            ]">
                            Teachers ({{ teacherCount }})
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="item in filteredPendingApprovals" :key="item.id"
                            class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <img :src="item.avatarUrl" :alt="item.name" class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500">Requested {{ item.requestedTime }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="handleApprove(item.id)" title="Approve"
                                    class="text-green-500 hover:text-green-600 p-1">
                                    <Check class="w-5 h-5" />
                                </button>
                                <button @click="handleReject(item.id)" title="Reject"
                                    class="text-red-500 hover:text-red-600 p-1">
                                    <X class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-4">
                <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Select Students</h2>

                    <div class="flex space-x-2 mb-4">
                        <input type="text" v-model="studentSearchTerm" placeholder="Search by student name or ID..."
                            class="flex-grow px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option>Department</option>
                            <option>CS</option>
                            <option>BA</option>
                            </select>
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option>Year Level</option>
                            <option>1st</option>
                            <option>2nd</option>
                            </select>
                    </div>

                    <label class="flex items-center space-x-2 mb-4">
                        <input type="checkbox" v-model="selectAllStudents"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Select All ({{ students.length }})</span>
                    </label>

                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        <div v-for="student in students" :key="student.id"
                            :class="[
                                'flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition duration-150',
                                student.isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200'
                            ]">
                            <div class="flex items-center w-full">
                                <input type="checkbox" v-model="student.isSelected"
                                    :class="[
                                        'mr-3 rounded text-blue-600 focus:ring-blue-500',
                                        student.isSelected ? 'border-blue-500' : 'border-gray-300'
                                    ]">
                                <img :src="student.avatarUrl" :alt="student.name" class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ student.name }}</p>
                                    <p :class="['text-xs font-medium', student.isSelected ? 'text-blue-600' : 'text-gray-500']">
                                        {{ student.major }} | ID: {{ student.studentId }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-5">
                <div class="bg-white p-6 rounded-lg shadow-lg h-full">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Available Subjects</h2>

                    <div class="flex space-x-2 mb-4">
                        <input type="text" v-model="subjectSearchTerm" placeholder="Search by subject name or code..."
                            class="flex-grow px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option>Subject Category</option>
                            </select>
                        <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                            <option>Credit Value</option>
                            </select>
                    </div>

                    <div class="space-y-3 max-h-[calc(100vh-250px)] overflow-y-auto pr-2">
                        <div v-for="subject in availableSubjects" :key="subject.id"
                            @click="selectSubject(subject.id)"
                            class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition duration-150">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ subject.name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ subject.code }} - **{{ subject.credits }} Credits**
                                    </p>
                                </div>
                                <Plus class="w-6 h-6 text-gray-400 hover:text-blue-600" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
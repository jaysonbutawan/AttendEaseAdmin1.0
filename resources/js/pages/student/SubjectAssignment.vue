<script setup lang="ts">
import { ref, computed } from 'vue';
import SelectSubject from './SelectSubject.vue';
interface Student {
    id: number;
    name: string;
    avatarUrl: string;
    major: string;
    studentId: string;
    isSelected: boolean;
}

const students = ref<Student[]>([
    { id: 101, name: 'Alex Johnson', avatarUrl: 'https://i.pravatar.cc/40?img=4', major: 'CS', studentId: '12345', isSelected: true },
    { id: 102, name: 'Maria Garcia', avatarUrl: 'https://i.pravatar.cc/40?img=5', major: 'BA', studentId: '12346', isSelected: false },
    { id: 103, name: 'David Smith', avatarUrl: 'https://i.pravatar.cc/40?img=6', major: 'Eng', studentId: '12347', isSelected: false },
    { id: 104, name: 'Emily White', avatarUrl: 'https://i.pravatar.cc/40?img=7', major: 'Bus', studentId: '12348', isSelected: false },
]);



const selectedStudentCount = computed(() => students.value.filter(s => s.isSelected).length);



const assignSubjects = () => {
    const selectedStudents = students.value.filter(s => s.isSelected).map(s => s.id);
    console.log('Attempting to assign subjects to student IDs:', selectedStudents);
    alert(`Attempting to assign subjects to ${selectedStudentCount.value} student(s) (Mock Action)`);
};

</script>

<template>
    <div class="p-6 bg-gray-50 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Subject Assignment</h1>
            <button @click="assignSubjects"
                class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                Assign Subject(s) ({{ selectedStudentCount }})
            </button>
        </div>
        <p class="text-gray-600 mb-6">Assign subjects to one or more students using the panels below.</p>

    
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <SelectSubject />
                </div>
            </div>
    </div>
</template>
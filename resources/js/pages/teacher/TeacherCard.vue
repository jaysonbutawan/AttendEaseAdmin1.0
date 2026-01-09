<script setup lang="ts">
import { Pencil, Trash2, X } from 'lucide-vue-next';
import { type Teacher } from '@/types';
import { ref } from 'vue';
import axios from 'axios';

const PRIMARY_COLOR_RGB = '79, 57, 246';

interface Props {
    teacher: Teacher;
    viewMode: 'table' | 'cards';
}

const props = defineProps<Props>();

const showEditModal = ref(false);
const saving = ref(false);
const editForm = ref({
    teacher_id: '',
    firstname: '',
    lastname: '',
    email: '',
    contact_number: '',
    status: '',
    approval_status: 'pending' as 'pending' | 'approved' | 'rejected'
});

const getInitials = (name: string) => {
    const nameParts = name.split(' ');
    if (nameParts.length >= 2) {
        return (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const getDepartmentClass = (department: string) => {
    const classes = {
        Science:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400',
        Mathematics:
            'bg-indigo-100 text-indigo-800 dark:bg-indigo-800/20 dark:text-indigo-400',
        English:
            'bg-pink-100 text-pink-800 dark:bg-pink-800/20 dark:text-pink-400',
        'Computer Science':
            'bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-400',
        History:
            'bg-purple-100 text-purple-800 dark:bg-purple-800/20 dark:text-purple-400',
        'Not Assigned':
            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    };
    return (
        classes[department as keyof typeof classes] ||
        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
    );
};

const editTeacher = (teacher: Teacher) => {
    // Populate form with teacher data
    editForm.value = {
        teacher_id: teacher.id,
        firstname: teacher.name.split(' ')[0] || '',
        lastname: teacher.name.split(' ').slice(1).join(' ') || '',
        email: teacher.email,
        contact_number: teacher.contact_number || '',
        status: teacher.status || '',
        approval_status: (teacher.approval_status || 'pending') as 'pending' | 'approved' | 'rejected'
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    saving.value = false;
};

const saveTeacher = async () => {
    saving.value = true;
    try {
        const response = await axios.put(`/api/teachers/${editForm.value.teacher_id}`, {
            firstname: editForm.value.firstname,
            lastname: editForm.value.lastname,
            email: editForm.value.email,
            contact_number: editForm.value.contact_number,
            status: editForm.value.status,
            approval_status: editForm.value.approval_status
        });

        if (response.data.success) {
            // Update local teacher data
            props.teacher.name = `${editForm.value.firstname} ${editForm.value.lastname}`;
            props.teacher.email = editForm.value.email;
            props.teacher.contact_number = editForm.value.contact_number;
            props.teacher.status = editForm.value.status;
            props.teacher.approval_status = editForm.value.approval_status;
            
            alert('Teacher updated successfully!');
            closeEditModal();
            window.location.reload(); // Reload to show updated data
        }
    } catch (error: any) {
        console.error('Failed to update teacher:', error);
        alert(error?.response?.data?.message || 'Failed to update teacher. Please try again.');
    } finally {
        saving.value = false;
    }
};

const deleteTeacher = async (teacher: Teacher) => {
    if (!confirm(`Are you sure you want to delete ${teacher.name}?`)) return;
    
    try {
        const response = await axios.delete(`/api/teachers/${teacher.id}`);
        
        if (response.data.success) {
            alert('Teacher deleted successfully!');
            window.location.reload(); // Reload to show updated list
        }
    } catch (error: any) {
        console.error('Failed to delete teacher:', error);
        alert(error?.response?.data?.message || 'Failed to delete teacher. Please try again.');
    }
};
</script>

<template>
    <!-- Table Row View -->
    <tr
        v-if="viewMode === 'table'"
        class="transition hover:bg-gray-50 dark:hover:bg-gray-700/50"
    >
        <!-- Teacher Name -->
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center space-x-3">
                <div
                    class="flex size-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                    :style="{
                        backgroundColor: `rgba(${PRIMARY_COLOR_RGB}, 0.8)`,
                    }"
                >
                    {{ getInitials(teacher.name) }}
                </div>
                <div>
                    <div
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ teacher.name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
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
                :class="getDepartmentClass(teacher.department)"
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
                <span
                    v-if="teacher.assignedSubjects.length === 0"
                    class="text-xs italic text-gray-400 dark:text-gray-500"
                >
                    No subjects assigned
                </span>
            </div>
        </td>
        <!-- Actions -->
        <td
            class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
        >
            <div class="flex justify-end space-x-2">
                <button
                    @click="editTeacher(teacher)"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 transition hover:bg-blue-100 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-400"
                    title="Edit teacher"
                >
                    <Pencil class="h-4 w-4" />
                </button>
                <button
                    @click="deleteTeacher(teacher)"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-gray-600 transition hover:bg-red-100 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-900/30 dark:hover:text-red-400"
                    title="Delete teacher"
                >
                    <Trash2 class="h-4 w-4" />
                </button>
            </div>
        </td>
    </tr>

    <!-- Card View -->
    <div
        v-if="viewMode === 'cards'"
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
                    {{ getInitials(teacher.name) }}
                </div>
                <div>
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-white"
                    >
                        {{ teacher.name }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        ID: #{{ teacher.id }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="mb-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ teacher.email }}
            </p>
        </div>

        <!-- Department -->
        <div class="mb-4">
            <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                :class="getDepartmentClass(teacher.department)"
            >
                {{ teacher.department }}
            </span>
        </div>

        <!-- Assigned Subjects -->
        <div class="mb-4">
            <p
                class="mb-2 text-xs font-medium text-gray-700 dark:text-gray-300"
            >
                Assigned Subjects:
            </p>
            <div
                v-if="teacher.assignedSubjects.length > 0"
                class="flex flex-wrap gap-2"
            >
                <span
                    v-for="subject in teacher.assignedSubjects"
                    :key="subject.id"
                    class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-600 dark:text-gray-200"
                >
                    {{ subject.name }}
                </span>
            </div>
            <p
                v-else
                class="text-xs italic text-gray-400 dark:text-gray-500"
            >
                No subjects assigned
            </p>
        </div>

        <!-- Actions -->
        <div
            class="flex gap-2 border-t border-gray-200 pt-4 dark:border-gray-600"
        >
            <button
                @click="editTeacher(teacher)"
                class="flex-1 flex items-center justify-center gap-2 rounded-lg py-2 px-3 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20"
                title="Edit teacher"
            >
                <Pencil class="h-4 w-4" />
                <span>Edit</span>
            </button>
            <button
                @click="deleteTeacher(teacher)"
                class="flex-1 flex items-center justify-center gap-2 rounded-lg py-2 px-3 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                title="Delete teacher"
            >
                <Trash2 class="h-4 w-4" />
                <span>Delete</span>
            </button>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <transition name="fade">
        <div 
            v-if="showEditModal"
            class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            @click="closeEditModal"
        >
            <transition name="slide-up">
                <div 
                    v-if="showEditModal"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col dark:bg-gray-800"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white">Edit Teacher</h2>
                            <p class="text-blue-100 text-sm mt-1">Update teacher information</p>
                        </div>
                        <button 
                            @click="closeEditModal"
                            class="p-2 hover:bg-blue-500 rounded-lg transition"
                        >
                            <X class="w-6 h-6 text-white" />
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="overflow-y-auto flex-1 p-8">
                        <form @submit.prevent="saveTeacher" class="space-y-6">
                            <!-- Name Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="editForm.firstname"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Enter first name"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="editForm.lastname"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Enter last name"
                                    />
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="editForm.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="teacher@example.com"
                                />
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Contact Number
                                </label>
                                <input
                                    v-model="editForm.contact_number"
                                    type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="+1 234 567 8900"
                                />
                            </div>

                            <!-- Approval Status -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    Approval Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="editForm.approval_status"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-gray-200 px-8 py-4 flex justify-end gap-3 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                        <button 
                            @click="closeEditModal"
                            type="button"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button 
                            @click="saveTeacher"
                            :disabled="saving"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <div v-if="saving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<style scoped>
/* Fade transition for overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide up transition for modal */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(30px);
  opacity: 0;
}
</style>

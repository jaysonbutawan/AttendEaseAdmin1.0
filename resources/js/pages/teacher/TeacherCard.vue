<script setup lang="ts">
import { type Teacher } from '@/types';

const PRIMARY_COLOR_RGB = '79, 57, 246';

interface Props {
    teacher: Teacher;
    viewMode: 'table' | 'cards';
}

defineProps<Props>();

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
        <!-- Actions Placeholder -->
        <td
            class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
        >
            <div class="flex justify-end space-x-3">
                <span class="text-xs text-gray-400">Actions coming soon</span>
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

        <!-- Actions Placeholder -->
        <div
            class="flex gap-2 border-t border-gray-200 pt-4 dark:border-gray-600"
        >
            <div class="flex-1 text-center text-xs text-gray-400">
                Actions coming soon
            </div>
        </div>
    </div>
</template>

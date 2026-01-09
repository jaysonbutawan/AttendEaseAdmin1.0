<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import SubjectCard from '@/pages/subject/SubjectCard.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

type ApiSubject = { subject_id: number; subject_name: string };

const showSubjectModal = ref(false);
const subjectModalTitle = ref('');
const subjects = ref<ApiSubject[]>([]);
const totalSubjects = ref(0);
const loading = ref(false);
const error = ref('');
const searchTerm = ref('');
const newSubjectName = ref('');
const saving = ref(false);
const editingSubjectId = ref<number | null>(null);
const viewMode = ref<'table' | 'cards'>('cards');
const selectedSubjects = ref<number[]>([]);

const gradients = [
    'from-blue-500 to-blue-600',
    'from-green-500 to-emerald-600',
    'from-amber-500 to-orange-600',
    'from-pink-500 to-rose-600',
    'from-cyan-500 to-blue-600',
    'from-violet-500 to-purple-600',
];

const filteredSubjects = computed(() => {
    const term = searchTerm.value.trim().toLowerCase();
    if (!term) return subjects.value;
    return subjects.value.filter((s) =>
        s.subject_name.toLowerCase().includes(term),
    );
});

const subjectCards = computed(() =>
    filteredSubjects.value.map((s, idx) => ({
        id: s.subject_id,
        name: s.subject_name,
        code: `SUB-${s.subject_id}`,
        course: '',
        teacher: null,
        status: '',
        gradient: gradients[idx % gradients.length],
    })),
);

const allSubjectsSelected = computed(() => {
    return (
        filteredSubjects.value.length > 0 &&
        selectedSubjects.value.length === filteredSubjects.value.length
    );
});

const toggleViewMode = (mode: 'table' | 'cards') => {
    viewMode.value = mode;
};

const toggleSubjectSelection = (subjectId: number) => {
    const index = selectedSubjects.value.indexOf(subjectId);
    if (index > -1) {
        selectedSubjects.value.splice(index, 1);
    } else {
        selectedSubjects.value.push(subjectId);
    }
};

const toggleAllSubjects = () => {
    if (selectedSubjects.value.length === filteredSubjects.value.length) {
        selectedSubjects.value = [];
    } else {
        selectedSubjects.value = filteredSubjects.value.map(
            (s) => s.subject_id,
        );
    }
};

const openSubjectModal = (mode: string, subjectId?: number) => {
    if (mode === 'edit' && subjectId) {
        const subject = subjects.value.find((s) => s.subject_id === subjectId);
        if (subject) {
            editingSubjectId.value = subjectId;
            newSubjectName.value = subject.subject_name;
        }
    }
    subjectModalTitle.value =
        mode === 'create' ? 'Add New Subject' : 'Edit Subject';
    showSubjectModal.value = true;
};

const closeSubjectModal = () => {
    showSubjectModal.value = false;
    newSubjectName.value = '';
    error.value = '';
    editingSubjectId.value = null;
};

const deleteSubject = async (subjectId: number) => {
    if (!confirm('Are you sure you want to delete this subject?')) return;

    try {
        const csrf =
            (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement
            )?.content || '';
        const res = await fetch(`/api/subjects/${subjectId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
        });

        if (!res.ok) throw new Error('Failed to delete subject');

        subjects.value = subjects.value.filter(
            (s) => s.subject_id !== subjectId,
        );
        totalSubjects.value -= 1;
    } catch (e: any) {
        error.value = e?.message ?? 'Unable to delete subject';
    }
};

async function loadSubjects() {
    loading.value = true;
    error.value = '';
    try {
        const res = await fetch('/api/subjects', {
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Failed to fetch subjects');
        const data = await res.json();
        subjects.value = (data?.subjects ?? []) as ApiSubject[];
    } catch (e: any) {
        error.value = e?.message ?? 'Unable to load subjects';
    } finally {
        loading.value = false;
    }
}

async function loadTotalSubjects() {
    try {
        const res = await fetch('/api/subjects/total', {
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Failed to fetch total subjects');
        const data = await res.json();
        totalSubjects.value = Number(data?.total_subjects ?? 0);
    } catch (e) {
        // silent in UI
    }
}

async function createSubject() {
    error.value = '';
    if (!newSubjectName.value.trim()) {
        error.value = 'Subject name is required';
        return;
    }

    saving.value = true;
    try {
        const csrf =
            (
                document.querySelector(
                    'meta[name="csrf-token"]',
                ) as HTMLMetaElement
            )?.content || '';
        const isEdit = editingSubjectId.value !== null;
        const url = isEdit
            ? `/api/subjects/${editingSubjectId.value}`
            : '/api/subjects';
        const method = isEdit ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ subject_name: newSubjectName.value.trim() }),
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            console.error('Server error:', err);
            throw new Error(
                err?.message ??
                    (isEdit
                        ? 'Failed to update subject'
                        : 'Failed to create subject'),
            );
        }

        const data = await res.json();
        console.log('Server response:', data);
        const savedSubject = data?.subject as ApiSubject | undefined;

        if (savedSubject?.subject_id) {
            if (isEdit) {
                const idx = subjects.value.findIndex(
                    (s) => s.subject_id === savedSubject.subject_id,
                );
                if (idx !== -1) subjects.value[idx] = savedSubject;
            } else {
                subjects.value.unshift(savedSubject);
                totalSubjects.value += 1;
            }
        }
        closeSubjectModal();
    } catch (e: any) {
        error.value = e?.message ?? 'Unable to save subject';
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    await Promise.all([loadSubjects(), loadTotalSubjects()]);
});
</script>

<template>
    <AppLayout>
        <Head title="Subjects - AttendEase" />

        <!-- Subjects Management Page -->
        <div class="space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Subjects</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Organize and manage all academic subjects across courses
                    with teacher assignments
                </p>
            </div>

            <!-- Subjects Overview Statistics -->
            <div
                class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <!-- Total Subjects -->
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Total Subjects
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-blue-600">
                                {{ totalSubjects }}
                            </h3>
                            <p class="mt-2 text-xs text-gray-500">
                                Across all courses
                            </p>
                        </div>
                        <div class="rounded-lg bg-blue-100 p-3">
                            <svg
                                class="h-6 w-6 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Listed Subjects -->
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">
                                Listed Subjects
                            </p>
                            <h3 class="mt-2 text-3xl font-bold text-purple-600">
                                {{ filteredSubjects.length }}
                            </h3>
                            <p class="mt-2 text-xs text-gray-500">
                                After applying search filters
                            </p>
                        </div>
                        <div class="rounded-lg bg-purple-100 p-3">
                            <svg
                                class="h-6 w-6 text-purple-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6v12m-6-6h12"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Panel -->
            <div
                class="mb-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
            >
                <div class="flex flex-col items-center gap-4 lg:flex-row">
                    <!-- Search by Subject -->
                    <div class="w-full flex-1">
                        <div class="relative">
                            <svg
                                class="absolute top-3 left-3 h-5 w-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"
                                ></path>
                            </svg>
                            <input
                                type="text"
                                v-model="searchTerm"
                                placeholder="Search by subject name..."
                                class="w-full rounded-lg border border-gray-300 py-2.5 pr-4 pl-10 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex gap-1 rounded-lg bg-gray-100 p-1">
                        <button
                            @click="toggleViewMode('cards')"
                            :class="
                                viewMode === 'cards'
                                    ? 'bg-white shadow-sm'
                                    : 'hover:bg-gray-200'
                            "
                            class="rounded-md px-3 py-2 transition"
                            title="Card View"
                        >
                            <svg
                                class="h-5 w-5 text-gray-700"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                ></path>
                            </svg>
                        </button>
                        <button
                            @click="toggleViewMode('table')"
                            :class="
                                viewMode === 'table'
                                    ? 'bg-white shadow-sm'
                                    : 'hover:bg-gray-200'
                            "
                            class="rounded-md px-3 py-2 transition"
                            title="Table View"
                        >
                            <svg
                                class="h-5 w-5 text-gray-700"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"
                                ></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Add Subject Button -->
                    <button
                        @click="openSubjectModal('create')"
                        class="flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 font-medium whitespace-nowrap text-white transition-all hover:bg-blue-700"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                        Add Subject
                    </button>
                </div>
            </div>

            <!-- Cards View -->
            <div
                v-if="viewMode === 'cards' && subjectCards.length > 0"
                class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm"
            >
                <div v-if="error" class="mb-4 text-sm text-red-600">
                    {{ error }}
                </div>
                <div v-if="loading" class="text-sm text-gray-600">
                    Loading subjects...
                </div>
                <div
                    v-else
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                >
                    <SubjectCard
                        v-for="subject in subjectCards"
                        :key="subject.id"
                        :subject="subject"
                        :onEdit="() => openSubjectModal('edit', subject.id)"
                    />
                </div>
            </div>

            <!-- Empty State for Cards -->
            <div
                v-if="
                    viewMode === 'cards' &&
                    !loading &&
                    subjectCards.length === 0
                "
                class="rounded-xl border border-gray-100 bg-white p-12 shadow-sm"
            >
                <div class="text-center text-gray-500">
                    <svg
                        class="mx-auto mb-4 h-16 w-16 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"
                        ></path>
                    </svg>
                    <p class="text-lg font-medium">No subjects found</p>
                    <p class="mt-2 text-sm">
                        Try adjusting your search or add a new subject
                    </p>
                </div>
            </div>

            <!-- Table View -->
            <div
                v-if="viewMode === 'table'"
                class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm"
            >
                <div v-if="error" class="p-4 text-sm text-red-600">
                    {{ error }}
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="allSubjectsSelected"
                                        @change="toggleAllSubjects"
                                        class="rounded border-gray-300"
                                    />
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700"
                                >
                                    Subject ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700"
                                >
                                    Subject Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700"
                                >
                                    Code
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-700"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="subject in filteredSubjects"
                                :key="subject.subject_id"
                                class="transition hover:bg-gray-50"
                            >
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedSubjects.includes(
                                                subject.subject_id,
                                            )
                                        "
                                        @change="
                                            toggleSubjectSelection(
                                                subject.subject_id,
                                            )
                                        "
                                        class="rounded border-gray-300"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <p
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        #{{ subject.subject_id }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        {{ subject.subject_name }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                    >
                                        SUB-{{ subject.subject_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button
                                            @click="
                                                openSubjectModal(
                                                    'edit',
                                                    subject.subject_id,
                                                )
                                            "
                                            class="rounded p-1.5 text-blue-600 transition hover:bg-blue-50"
                                            title="Edit subject"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                ></path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="
                                                deleteSubject(
                                                    subject.subject_id,
                                                )
                                            "
                                            class="rounded p-1.5 text-red-600 transition hover:bg-red-50"
                                            title="Delete subject"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr
                                v-if="!loading && filteredSubjects.length === 0"
                            >
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg
                                            class="mx-auto mb-3 h-12 w-12 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"
                                            ></path>
                                        </svg>
                                        <p class="text-sm font-medium">
                                            No subjects found
                                        </p>
                                        <p class="mt-1 text-xs">
                                            Try adjusting your search or add a
                                            new subject
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Subject Modal -->
        <div
            id="subjectModal"
            v-show="showSubjectModal"
            class="fixed inset-0 backdrop-blur-sm z-50 flex items-center justify-center px-4"
            @click.self="closeSubjectModal"
        >
            <div
                class="w-full max-w-md transform rounded-2xl bg-white shadow-2xl transition-all duration-300 ease-out"
                :class="
                    showSubjectModal
                        ? 'scale-100 opacity-100'
                        : 'scale-95 opacity-0'
                "
            >
                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between rounded-t-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20"
                        >
                            <svg
                                class="h-5 w-5 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6v12m-6-6h12"
                                    v-if="!editingSubjectId"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    v-else
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-white">
                            {{ subjectModalTitle }}
                        </h2>
                    </div>
                    <button
                        @click="closeSubjectModal"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-white transition hover:bg-white/20"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <form @submit.prevent="createSubject" class="space-y-6 p-6">
                    <!-- Subject Name -->
                    <div>
                        <label
                            class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-700"
                        >
                            <svg
                                class="h-4 w-4 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"
                                ></path>
                            </svg>
                            Subject Name
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="newSubjectName"
                            placeholder="e.g., Mathematics, Physics, English Literature"
                            class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 transition-all placeholder:text-gray-400 focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required
                            autofocus
                        />
                        <p
                            v-if="error"
                            class="mt-2 flex items-center gap-1 text-xs text-red-600"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"
                                ></path>
                            </svg>
                            {{ error }}
                        </p>
                        <p v-else class="mt-2 text-xs text-gray-500">
                            Enter a unique name for this subject
                        </p>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="closeSubjectModal"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-gray-200 px-4 py-3 font-medium text-gray-700 transition-all hover:border-gray-300 hover:bg-gray-50"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                ></path>
                            </svg>
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 font-medium text-white shadow-lg shadow-blue-500/30 transition-all hover:from-blue-700 hover:to-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg
                                v-if="!saving"
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                    v-if="!editingSubjectId"
                                ></path>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                    v-else
                                ></path>
                            </svg>
                            <svg
                                v-else
                                class="h-4 w-4 animate-spin"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{
                                saving
                                    ? 'Saving...'
                                    : editingSubjectId
                                      ? 'Update Subject'
                                      : 'Save Subject'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

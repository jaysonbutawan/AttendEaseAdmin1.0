<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import SubjectCard from '@/pages/subject/SubjectCard.vue';

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
  return subjects.value.filter((s) => s.subject_name.toLowerCase().includes(term));
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
  }))
);

const openSubjectModal = (mode: string, subjectId?: number) => {
  if (mode === 'edit' && subjectId) {
    const subject = subjects.value.find(s => s.subject_id === subjectId);
    if (subject) {
      editingSubjectId.value = subjectId;
      newSubjectName.value = subject.subject_name;
    }
  }
  subjectModalTitle.value = mode === 'create' ? 'Add New Subject' : 'Edit Subject';
  showSubjectModal.value = true;
};

const closeSubjectModal = () => {
  showSubjectModal.value = false;
  newSubjectName.value = '';
  error.value = '';
  editingSubjectId.value = null;
};

async function loadSubjects() {
  loading.value = true;
  error.value = '';
  try {
    const res = await fetch('/api/subjects', { credentials: 'same-origin' });
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
    const res = await fetch('/api/subjects/total', { credentials: 'same-origin' });
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
    const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
    const isEdit = editingSubjectId.value !== null;
    const url = isEdit ? `/api/subjects/${editingSubjectId.value}` : '/api/subjects';
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
      throw new Error(err?.message ?? (isEdit ? 'Failed to update subject' : 'Failed to create subject'));
    }

    const data = await res.json();
    console.log('Server response:', data);
    const savedSubject = data?.subject as ApiSubject | undefined;
    
    if (savedSubject?.subject_id) {
      if (isEdit) {
        const idx = subjects.value.findIndex(s => s.subject_id === savedSubject.subject_id);
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
        <p class="text-gray-600 text-sm mt-2">Organize and manage all academic subjects across courses with teacher assignments</p>
      </div>

      <!-- Subjects Overview Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Subjects</p>
              <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ totalSubjects }}</h3>
              <p class="text-xs text-gray-500 mt-2">Across all courses</p>
            </div>
            <div class="bg-blue-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Listed Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Listed Subjects</p>
              <h3 class="text-3xl font-bold text-purple-600 mt-2">{{ filteredSubjects.length }}</h3>
              <p class="text-xs text-gray-500 mt-2">After applying search filters</p>
            </div>
            <div class="bg-purple-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-6-6h12"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filter Panel -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-center">
          <!-- Search by Subject -->
          <div class="flex-1 w-full">
            <div class="relative">
              <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"></path>
              </svg>
              <input
                type="text"
                v-model="searchTerm"
                placeholder="Search by subject name..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
          </div>

          <!-- Add Subject Button -->
          <button @click="openSubjectModal('create')" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Subject
          </button>
        </div>
      </div>

      <!-- Subjects Grid -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div v-if="error" class="mb-4 text-sm text-red-600">{{ error }}</div>
        <div v-if="loading" class="text-sm text-gray-600">Loading subjects...</div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <SubjectCard
            v-for="subject in subjectCards"
            :key="subject.id"
            :subject="subject"
            :onEdit="() => openSubjectModal('edit', subject.id)"
          />
        </div>
        <div v-if="!loading && subjectCards.length === 0" class="text-sm text-gray-600">No subjects found.</div>
      </div>
    </div>

    <!-- Create/Edit Subject Modal -->
    <div id="subjectModal" v-show="showSubjectModal" class="fixed inset-0 flex items-center justify-center z-50 px-4" @click.self="closeSubjectModal">
      <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 ease-out" :class="showSubjectModal ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-2xl flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-6-6h12" v-if="!editingSubjectId"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z" v-else></path>
              </svg>
            </div>
            <h2 class="text-lg font-bold text-white">{{ subjectModalTitle }}</h2>
          </div>
          <button @click="closeSubjectModal" class="w-8 h-8 flex items-center justify-center text-white hover:bg-white/20 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Modal Content -->
        <form @submit.prevent="createSubject" class="p-6 space-y-6">
          <!-- Subject Name -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"></path>
              </svg>
              Subject Name
              <span class="text-red-500">*</span>
            </label>
            <input 
              type="text" 
              v-model="newSubjectName"
              placeholder="e.g., Mathematics, Physics, English Literature" 
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-gray-400"
              required
              autofocus
            >
            <p v-if="error" class="text-xs text-red-600 mt-2 flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
              </svg>
              {{ error }}
            </p>
            <p v-else class="text-xs text-gray-500 mt-2">Enter a unique name for this subject</p>
          </div>

          <!-- Modal Actions -->
          <div class="flex gap-3 pt-4">
            <button 
              type="button" 
              @click="closeSubjectModal" 
              class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="saving" 
              class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-medium disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30"
            >
              <svg v-if="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" v-if="!editingSubjectId"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" v-else></path>
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ saving ? 'Saving...' : (editingSubjectId ? 'Update Subject' : 'Save Subject') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { ChevronsUpDown, Check, PlusCircle } from 'lucide-vue-next';

const props = defineProps<{
  subjects: string[];
  currentSubject?: string;
}>();

const emit = defineEmits<{
  (e: 'subjectSelect', value: string): void;
  (e: 'subjectCreate', value: string): void;
}>();

const open = ref(false);
const inputValue = ref(props.currentSubject ?? '');
const containerRef = ref<HTMLElement | null>(null);

const currentSubject = computed(() => props.currentSubject ?? '');

// keep input in sync when parent changes currentSubject
watch(
  () => props.currentSubject,
  newVal => {
    if (typeof newVal === 'string') {
      inputValue.value = newVal;
    }
  }
);

const trimmedInput = computed(() => inputValue.value.trim());

const filteredSubjects = computed(() => {
  if (!trimmedInput.value) return props.subjects;
  const lower = trimmedInput.value.toLowerCase();
  return props.subjects.filter(subject =>
    subject.toLowerCase().includes(lower)
  );
});

const isNewSubjectCandidate = computed(() => {
  if (!trimmedInput.value) return false;
  const exists = props.subjects.some(
    subject => subject.toLowerCase() === trimmedInput.value.toLowerCase()
  );
  return !exists;
});

const handleSelect = (subjectName: string) => {
  emit('subjectSelect', subjectName);
  inputValue.value = subjectName;
  open.value = false;
};

const handleCreate = (newSubjectName: string) => {
  const value = newSubjectName.trim();
  if (!value) return;
  emit('subjectCreate', value);
  inputValue.value = value;
  open.value = false;
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Enter' && isNewSubjectCandidate.value) {
    e.preventDefault();
    handleCreate(trimmedInput.value);
  } else if (e.key === 'Escape') {
    open.value = false;
  }
};

const handleClickOutside = (event: MouseEvent) => {
  if (!open.value) return;
  if (!containerRef.value) return;
  if (!containerRef.value.contains(event.target as Node)) {
    open.value = false;
  }
};

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
  <div ref="containerRef" class="relative w-full">
    <div class="flex items-center">
      <input
        v-model="inputValue"
        @input="open = true"
        @focus="open = true"
        @keydown="handleKeyDown"
        type="text"
        :placeholder="
          subjects.length === 0
            ? 'e.g., Advanced Mathematics (Type to create)'
            : 'Select or type subject'
        "
        class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-50 dark:placeholder:text-gray-500 pr-10"
      />
      <button
        @click.stop="open = !open"
        type="button"
        class="inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 text-gray-500 hover:bg-transparent dark:text-gray-400 dark:hover:bg-transparent p-2 -ml-10 h-10 w-10 rounded-l-none"
      >
        <ChevronsUpDown class="h-4 w-4" />
      </button>
    </div>

    <div
      v-if="open"
      class="absolute z-50 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-xl max-h-60 overflow-y-auto dark:border-gray-700 dark:bg-gray-800"
    >
      <!-- Existing subjects -->
      <div class="p-1">
        <div
          class="sticky top-0 z-10 bg-white px-2 py-1.5 text-xs font-semibold text-gray-400 dark:bg-gray-800 dark:text-gray-500"
        >
          Existing Subjects
        </div>

        <template v-if="filteredSubjects.length > 0">
          <div
            v-for="subject in filteredSubjects"
            :key="subject"
            @click="handleSelect(subject)"
            :class="[
              'relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300',
              currentSubject === subject ? 'bg-blue-50 dark:bg-blue-900/30' : ''
            ]"
          >
            <Check
              v-if="currentSubject === subject"
              class="mr-2 h-4 w-4 text-blue-600 dark:text-blue-400"
            />
            <div v-else class="mr-2 h-4 w-4"></div>
            {{ subject }}
          </div>
        </template>

        <template v-else>
          <div class="p-2 text-sm text-gray-500 dark:text-gray-400">
            No existing subjects match the query.
          </div>
        </template>
      </div>

      <!-- Create new subject -->
      <div v-if="isNewSubjectCandidate" class="p-1">
        <div class="px-2 py-1.5 text-xs font-semibold text-gray-400 dark:text-gray-500">
          Create New
        </div>
        <div
          @click="handleCreate(trimmedInput)"
          class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/50"
        >
          <PlusCircle class="mr-2 h-4 w-4" />
          Create "{{ trimmedInput }}"
        </div>
      </div>
    </div>
  </div>
</template>

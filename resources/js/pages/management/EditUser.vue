<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface EditUserProps {
  role: 'teacher' | 'student';
  user: {
    id: string | number;
    firstname?: string;
    lastname?: string;
    email?: string;
    contact_number?: string;
    course_id?: number | null;
  };
}

const props = defineProps<EditUserProps>();

const firstname = ref(props.user.firstname ?? '');
const lastname = ref(props.user.lastname ?? '');
const email = ref(props.user.email ?? '');
const contact_number = ref(props.user.contact_number ?? '');
const course_id = ref<number | null>(props.user.course_id ?? null);

const title = computed(() => props.role === 'teacher' ? 'Edit Teacher' : 'Edit Student');

const submit = () => {
  const payload: Record<string, any> = {
    firstname: firstname.value || null,
    lastname: lastname.value || null,
    email: email.value || null,
    contact_number: contact_number.value || null,
  };
  if (props.role === 'student') {
    payload.course_id = course_id.value ?? null;
  }

  const endpoint = props.role === 'teacher'
    ? `/api/teachers/${props.user.id}`
    : `/api/students/${props.user.id}`;

  router.put(endpoint, payload, {
    preserveScroll: true,
    onSuccess: () => router.visit('/usermanagement'),
  });
};
</script>

<template>
  <AppLayout>
    <Head :title="title + ' - AttendEase'" />

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <h1 class="text-2xl font-bold text-gray-800">{{ title }}</h1>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <input v-model="firstname" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <input v-model="lastname" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
            <input v-model="contact_number" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
          <div v-if="props.role === 'student'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Course ID</label>
            <input v-model.number="course_id" type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
          </div>
        </div>
        <div class="mt-6 flex gap-3">
          <button @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save Changes</button>
          <button @click="router.visit('/usermanagement')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancel</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

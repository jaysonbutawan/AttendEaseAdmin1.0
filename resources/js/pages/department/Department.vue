<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
	AlertCircle,
	Building2,
	CheckCircle2,
	Loader2,
	Plus,
	Trash2,
} from 'lucide-vue-next';
import { computed, onMounted, reactive, ref } from 'vue';

type Course = {
	course_id: number;
	course_name: string;
	created_at?: string;
};

const courses = ref<Course[]>([]);
const loading = ref(false);
const saving = ref(false);
const deletingId = ref<number | null>(null);
const editingId = ref<number | null>(null);
const editName = ref('');
const search = ref('');
const form = reactive({ name: '', error: '', success: '' });

const csrfToken = () =>
	(document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';

const filteredCourses = computed(() => {
	const q = search.value.trim().toLowerCase();
	if (!q) return courses.value;
	return courses.value.filter((course) => course.course_name.toLowerCase().includes(q));
});

async function loadCourses() {
	loading.value = true;
	form.error = '';
	try {
		const res = await fetch('/api/courses', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Unable to load departments');
		const data = await res.json();
		courses.value = (data?.courses ?? []) as Course[];
	} catch (err: any) {
		form.error = err?.message || 'Failed to load departments';
	} finally {
		loading.value = false;
	}
}

async function createCourse() {
	form.error = '';
	form.success = '';
	const name = form.name.trim();
	if (!name) {
		form.error = 'Department name is required';
		return;
	}

	saving.value = true;
	try {
		const res = await fetch('/api/courses', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				Accept: 'application/json',
				'X-CSRF-TOKEN': csrfToken(),
			},
			credentials: 'same-origin',
			body: JSON.stringify({ course_name: name }),
		});

		if (res.status === 422) {
			const body = await res.json();
			const message = body?.errors?.course_name?.[0] || 'Validation failed';
			throw new Error(message);
		}

		if (!res.ok) throw new Error('Could not save department');

		const data = await res.json();
		const created = data?.course as Course | undefined;
		if (created) {
			courses.value.unshift(created);
			form.name = '';
			form.success = 'Department saved to the database.';
		}
	} catch (err: any) {
		form.error = err?.message || 'Something went wrong';
	} finally {
		saving.value = false;
	}
}

async function deleteCourse(id: number) {
	deletingId.value = id;
	form.error = '';
	form.success = '';
	try {
		const res = await fetch(`/api/courses/${id}`, {
			method: 'DELETE',
			headers: {
				Accept: 'application/json',
				'X-CSRF-TOKEN': csrfToken(),
			},
			credentials: 'same-origin',
		});
		if (!res.ok) throw new Error('Failed to delete department');
		courses.value = courses.value.filter((course) => course.course_id !== id);
		form.success = 'Department removed.';
	} catch (err: any) {
		form.error = err?.message || 'Unable to delete department';
	} finally {
		deletingId.value = null;
	}
}

function startEdit(course: Course) {
	editingId.value = course.course_id;
	editName.value = course.course_name;
	form.error = '';
	form.success = '';
}

function cancelEdit() {
	editingId.value = null;
	editName.value = '';
}

async function updateCourse(id: number) {
	if (!editName.value.trim()) {
		form.error = 'Department name is required';
		return;
	}

	form.error = '';
	form.success = '';
	saving.value = true;

	try {
		const res = await fetch(`/api/courses/${id}`, {
			method: 'PUT',
			headers: {
				'Content-Type': 'application/json',
				Accept: 'application/json',
				'X-CSRF-TOKEN': csrfToken(),
			},
			credentials: 'same-origin',
			body: JSON.stringify({ course_name: editName.value.trim() }),
		});

		if (res.status === 422) {
			const body = await res.json();
			const message = body?.errors?.course_name?.[0] || 'Validation failed';
			throw new Error(message);
		}

		if (!res.ok) throw new Error('Could not update department');

		const data = await res.json();
		const updated = data?.course as Course | undefined;
		if (updated?.course_id) {
			const idx = courses.value.findIndex((c) => c.course_id === updated.course_id);
			if (idx !== -1) courses.value[idx] = updated;
			form.success = 'Department updated.';
		}
		cancelEdit();
	} catch (err: any) {
		form.error = err?.message || 'Something went wrong while updating';
	} finally {
		saving.value = false;
	}
}

onMounted(loadCourses);
</script>

<template>
	<AppLayout>
		<Head title="Departments" />

		<div class="space-y-8 p-4 sm:p-6 lg:p-8">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div class="space-y-1">
					<p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Departments</p>
					<div class="flex items-center gap-2">
						<Building2 class="h-6 w-6 text-indigo-600" />
						<h1 class="text-3xl font-bold text-gray-900">Department & Course Catalog</h1>
					</div>
					<p class="text-sm text-gray-600">
						Add departments (courses) that students can select during enrollment.
					</p>
				</div>
				<div class="rounded-full bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">
					{{ courses.length }} total departments
				</div>
			</div>

			<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
				<div class="rounded-2xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
					<div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
						<div>
							<p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Database</p>
							<h2 class="text-lg font-semibold text-gray-900">Existing Departments</h2>
						</div>
						<div class="relative">
							<input
								v-model="search"
								type="text"
								placeholder="Search"
								class="w-48 rounded-lg border border-gray-200 bg-gray-50 py-2 pr-3 pl-3 text-sm text-gray-800 shadow-inner focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100"
							/>
						</div>
					</div>

					<div class="overflow-hidden">
						<div v-if="loading" class="flex items-center justify-center gap-2 px-6 py-8 text-sm text-gray-600">
							<Loader2 class="h-5 w-5 animate-spin text-indigo-600" />
							Loading departments...
						</div>

						<div v-else-if="!filteredCourses.length" class="flex flex-col items-center justify-center gap-3 px-6 py-10 text-center">
							<div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
								<Building2 class="h-5 w-5" />
							</div>
							<div class="space-y-1">
								<p class="text-lg font-semibold text-gray-900">No departments yet</p>
								<p class="text-sm text-gray-600">Create one to get started. Students will see these options during course selection.</p>
							</div>
						</div>

						<div v-else class="divide-y divide-gray-100">
							<div
								v-for="course in filteredCourses"
								:key="course.course_id"
								class="flex items-center justify-between px-6 py-4 transition hover:bg-gray-50"
							>
								<div class="flex items-center gap-3">
									<div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
										{{ course.course_name.slice(0, 2).toUpperCase() }}
									</div>
									<div class="space-y-1">
										<template v-if="editingId === course.course_id">
											<input
												v-model="editName"
												type="text"
												class="w-64 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
											/>
											<p class="text-xs text-gray-500">Editing department name</p>
										</template>
										<template v-else>
											<p class="text-sm font-semibold text-gray-900">{{ course.course_name }}</p>
											<p class="text-xs text-gray-500">
												Added {{ course.created_at ? new Date(course.created_at).toLocaleDateString() : 'recently' }}
											</p>
										</template>
									</div>
								</div>

								<div class="flex items-center gap-2">
									<template v-if="editingId === course.course_id">
										<button
											type="button"
											:disabled="saving"
											@click="updateCourse(course.course_id)"
											class="flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70"
										>
											<Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
											<span v-else>Save</span>
										</button>
										<button
											type="button"
											:disabled="saving"
											@click="cancelEdit"
											class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-70"
										>
											Cancel
										</button>
									</template>
									<template v-else>
										<button
											type="button"
											@click="startEdit(course)"
											class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
										>
											Edit
										</button>
										<button
											type="button"
											:disabled="deletingId === course.course_id"
											@click="deleteCourse(course.course_id)"
											class="flex items-center gap-2 rounded-lg border border-red-100 px-3 py-2 text-sm font-medium text-red-600 transition hover:border-red-200 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60"
										>
											<Trash2 class="h-4 w-4" />
											<span v-if="deletingId === course.course_id" class="flex items-center gap-1">
												<Loader2 class="h-4 w-4 animate-spin" /> Removing
											</span>
											<span v-else>Remove</span>
										</button>
									</template>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="space-y-4">
					<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
						<p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Create</p>
						<h2 class="text-xl font-semibold text-gray-900">Add a Department</h2>
						<p class="mt-1 text-sm text-gray-600">
							Saved directly to the courses table so students can pick it during enrollment.
						</p>

						<form class="mt-6 space-y-4" @submit.prevent="createCourse">
							<div class="space-y-2">
								<label class="text-sm font-medium text-gray-700">Department name</label>
								<input
									v-model="form.name"
									type="text"
									placeholder="e.g. Computer Science"
									class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-inner focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
								/>
								<p class="text-xs text-gray-500">Only the name is required for this table.</p>
							</div>

							<div v-if="form.error" class="flex items-start gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
								<AlertCircle class="mt-0.5 h-4 w-4" />
								<span>{{ form.error }}</span>
							</div>

							<div v-if="form.success" class="flex items-start gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
								<CheckCircle2 class="mt-0.5 h-4 w-4" />
								<span>{{ form.success }}</span>
							</div>

							<button
								type="submit"
								:disabled="saving"
								class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70"
							>
								<Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
								<Plus v-else class="h-4 w-4" />
								<span>{{ saving ? 'Saving...' : 'Save Department' }}</span>
							</button>
						</form>
					</div>

					<div class="rounded-2xl border border-indigo-100 bg-indigo-50 p-4 text-sm text-indigo-800 shadow-sm">
						<p class="font-semibold">Heads up</p>
						<p class="mt-1 leading-relaxed">
							Departments are stored in the courses table. Use concise, student-facing names. You can remove a department if it is no longer offered.
						</p>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import CourseCard from '@/pages/course/CourseCard.vue';
import SubjectManager from './CourseSchedule.vue';


type ApiCourse = { course_id: number; course_name: string };
type CardCourse = {
	id: number;
	name: string;
	code: string;
	status: string;
	teacher: string;
	students: number;
	subjects: number;
	gradient: string;
};

const showCourseModal = ref(false);
const courseModalTitle = ref('Create New Course');
const newCourseName = ref('');
const loading = ref(false);
const error = ref('');
const courses = ref<ApiCourse[]>([]);
const totalEnrollments = ref(0);
const totalSubjects = ref(0);
const unassignedTeachers = ref(0);
const editingCourseId = ref<number | null>(null);
const selectedCourseId = ref<number | 'all'>('all');

const gradients = [
	'from-blue-500 to-blue-600',
	'from-green-500 to-emerald-600',
	'from-amber-500 to-orange-600',
	'from-pink-500 to-rose-600',
	'from-cyan-500 to-blue-600',
	'from-violet-500 to-purple-600',
];

function toCardCourse(c: ApiCourse, index: number): CardCourse {
	return {
		id: c.course_id,
		name: c.course_name,
		code: '',
		status: '',
		teacher: '',
		students: 0,
		subjects: 0,
		gradient: gradients[index % gradients.length],
	};
}

const filteredCourses = computed(() => {
	if (selectedCourseId.value === 'all') return courses.value;
	return courses.value.filter((c) => c.course_id === selectedCourseId.value);
});

const cardCourses = computed(() => filteredCourses.value.map((c, i) => toCardCourse(c, i)));

function startEdit(id: number) {
	const c = courses.value.find((item) => item.course_id === id);
	if (!c) return;
	editingCourseId.value = id;
	newCourseName.value = c.course_name;
	openCourseModal('edit');
}

function openCourseModal(mode = 'create') {
	courseModalTitle.value = mode === 'create' ? 'Create New Course' : 'Edit Course';
	showCourseModal.value = true;
}

function closeCourseModal() {
	showCourseModal.value = false;
	newCourseName.value = '';
	error.value = '';
	editingCourseId.value = null;
}

async function loadCourses() {
	loading.value = true;
	error.value = '';
	try {
		const res = await fetch('/api/courses', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch courses');
		const data = await res.json();
		courses.value = (data?.courses ?? []) as ApiCourse[];
	} catch (e: any) {
		error.value = e?.message ?? 'Unable to load courses';
	} finally {
		loading.value = false;
	}
}

async function loadTotalEnrollments() {
	try {
		const res = await fetch('/api/enrollments/total', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch total enrollments');
		const data = await res.json();
		totalEnrollments.value = Number(data?.total_enrollments ?? 0);
	} catch (e) {
		// keep silent in UI, optional logging
	}
}

async function loadTotalSubjects() {
	try {
		const res = await fetch('/api/subjects/total', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch total subjects');
		const data = await res.json();
		totalSubjects.value = Number(data?.total_subjects ?? 0);
	} catch (e) {
		// silent
	}
}

async function loadUnassignedTeachers() {
	try {
		const res = await fetch('/api/teachers/unassigned-count', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch unassigned teachers');
		const data = await res.json();
		unassignedTeachers.value = Number(data?.unassigned_teachers ?? 0);
	} catch (e) {
		// silent
	}
}

async function createCourse() {
	error.value = '';
	if (!newCourseName.value.trim()) {
		error.value = 'Course name is required';
		return;
	}
	try {
		const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
		const isEdit = editingCourseId.value !== null;
		const url = isEdit ? `/api/courses/${editingCourseId.value}` : '/api/courses';
		const method = isEdit ? 'PUT' : 'POST';
		const res = await fetch(url, {
			method,
			headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
			body: JSON.stringify({ course_name: newCourseName.value.trim() }),
			credentials: 'same-origin',
		});
		if (!res.ok) {
			const err = await res.json().catch(() => ({}));
			throw new Error(err?.message ?? 'Failed to create course');
		}
		const data = await res.json();
		const created = data?.course as ApiCourse;
		if (created?.course_id) {
			if (isEdit) {
				const idx = courses.value.findIndex((c) => c.course_id === created.course_id);
				if (idx !== -1) courses.value[idx] = created;
			} else {
				courses.value.unshift(created);
			}
		}
		closeCourseModal();
	} catch (e: any) {
		error.value = e?.message ?? 'Unable to save course';
	}
}

onMounted(async () => {
	await Promise.all([
		loadCourses(),
		loadTotalEnrollments(),
		loadUnassignedTeachers(),
		loadTotalSubjects(),
	]);
});
</script>

<template>
	<AppLayout>
		<Head title="Courses - AttendEase" />
		
		<!-- Courses Management Page -->
		<div class="space-y-6 p-4 sm:p-6 lg:p-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-800">Courses Management</h1>
				<p class="text-gray-600 text-sm mt-2">Manage courses, subjects, and academic programs</p>
			</div>

			<!-- Course Overview Panel -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
				<!-- Total Courses -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Total Courses</p>
							<h3 class="text-3xl font-bold text-gray-800 mt-2">{{ courses.length }}</h3>
							<p class="text-xs text-gray-500 mt-2">Dynamic from database</p>
						</div>
						<div class="bg-blue-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"></path>
							</svg>
						</div>
					</div>
				</div>


				<!-- Total Enrollments -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Total Enrollments</p>
							<h3 class="text-3xl font-bold text-indigo-600 mt-2">{{ totalEnrollments }}</h3>
							<p class="text-xs text-gray-500 mt-2">Avg: {{ courses.length ? (Math.round((totalEnrollments / courses.length) * 100) / 100) : 0 }} per course</p>
						</div>
						<div class="bg-indigo-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.856-1.487M15 6h3a1 1 0 0 1 1 1v3h-4V7a1 1 0 0 1 0-1z"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Total Subjects -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Total Subjects</p>
							<h3 class="text-3xl font-bold text-purple-600 mt-2">{{ totalSubjects }}</h3>
							<p class="text-xs text-gray-500 mt-2">All subjects in system</p>
						</div>
						<div class="bg-purple-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-6-6h12"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Unassigned Teachers -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Unassigned Teachers</p>
							<h3 class="text-3xl font-bold text-amber-600 mt-2">{{ unassignedTeachers }}</h3>
							<p class="text-xs text-gray-500 mt-2">Available for scheduling</p>
						</div>
						<div class="bg-amber-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"></path>
							</svg>
						</div>
					</div>
				</div>
			</div>

		  <div class="flex h-full flex-1 flex-col gap-6 p-6 md:p-8">
            <div class="w-full">
                <div class="w-full"><SubjectManager /></div>
            </div>

			<!-- Courses Grid -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<CourseCard
					v-for="course in cardCourses"
					:key="course.id"
					:course="course"
					:onEdit="() => startEdit(course.id)"
					:onViewDetails="() => {}"
				/>
			</div>
		</div>
		</div>

		<!-- Create/Edit Course Modal -->
		<div id="courseModal" v-show="showCourseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
			<div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
				<!-- Modal Header -->
				<div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
					<h2 class="text-lg font-bold text-white" id="courseModalTitle">{{ courseModalTitle }}</h2>
					<button @click="closeCourseModal" class="text-white hover:text-gray-200 transition">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>

				<!-- Modal Content -->
				<form @submit.prevent="createCourse" class="p-6 space-y-6">
					<!-- Course Name -->
					<div>
						<h3 class="text-sm font-semibold text-gray-900 mb-4">Course Information</h3>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Course Name *</label>
							<input 
								type="text" 
								placeholder="Enter course name (e.g., Mathematics 101)" 
								class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
								v-model="newCourseName"
								required
							>
							<p class="text-xs text-gray-500 mt-1">This is the only field required in the database</p>
							<p v-if="error" class="text-xs text-red-600 mt-1">{{ error }}</p>
						</div>
					</div>

					<!-- Modal Actions -->
					<div class="flex gap-3 pt-4 border-t border-gray-200">
						<button type="button" @click="closeCourseModal" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">Cancel</button>
						<button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">Save Course</button>
					</div>
				</form>
			</div>
		</div>
	</AppLayout>
</template>

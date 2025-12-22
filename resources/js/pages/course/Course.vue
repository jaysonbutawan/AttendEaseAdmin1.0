<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import CourseCard from '@/pages/course/CourseCard.vue';

type ApiCourse = { id: number; course_name: string };
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
		id: c.id,
		name: c.course_name,
		code: '',
		status: '',
		teacher: '',
		students: 0,
		subjects: 0,
		gradient: gradients[index % gradients.length],
	};
}

const cardCourses = computed(() => courses.value.map((c, i) => toCardCourse(c, i)));

function openCourseModal(mode = 'create') {
	courseModalTitle.value = mode === 'create' ? 'Create New Course' : 'Edit Course';
	showCourseModal.value = true;
}

function closeCourseModal() {
	showCourseModal.value = false;
	newCourseName.value = '';
	error.value = '';
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

async function createCourse() {
	error.value = '';
	if (!newCourseName.value.trim()) {
		error.value = 'Course name is required';
		return;
	}
	try {
		const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
		const res = await fetch('/api/courses', {
			method: 'POST',
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
		if (created?.id) {
			courses.value.unshift(created);
		}
		closeCourseModal();
	} catch (e: any) {
		error.value = e?.message ?? 'Unable to create course';
	}
}

onMounted(loadCourses);
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
							<h3 class="text-3xl font-bold text-indigo-600 mt-2">1,230</h3>
							<p class="text-xs text-gray-500 mt-2">Avg: 27.95 per course</p>
						</div>
						<div class="bg-indigo-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.856-1.487M15 6h3a1 1 0 0 1 1 1v3h-4V7a1 1 0 0 1 0-1z"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Assigned Teachers -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Assigned Teachers</p>
							<h3 class="text-3xl font-bold text-amber-600 mt-2">42</h3>
							<p class="text-xs text-gray-500 mt-2">1.14 courses per teacher</p>
						</div>
						<div class="bg-amber-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"></path>
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Search and Filter Panel -->
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
				<div class="flex flex-col lg:flex-row gap-4">
					<!-- Search Bar -->
					<div class="flex-1">
						<div class="relative">
							<svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"></path>
							</svg>
							<input type="text" placeholder="Search by course name or code..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
						</div>
					</div>

					<!-- Filter by Status -->
					<select class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
						<option value="">All Status</option>
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
						<option value="archived">Archived</option>
					</select>

					<!-- Sort By -->
					<select class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
						<option value="">Sort By</option>
						<option value="name">Course Name (A-Z)</option>
						<option value="enrollment">Enrollment (High to Low)</option>
						<option value="recent">Recently Updated</option>
					</select>

					<!-- Add Course Button -->
					<button @click="openCourseModal('create')" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium flex items-center gap-2 whitespace-nowrap">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
						Add Course
					</button>
				</div>
			</div>

			<!-- Courses Grid -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<CourseCard
					v-for="course in cardCourses"
					:key="course.id"
					:course="course"
					:onEdit="() => openCourseModal('edit')"
					:onViewDetails="() => {}"
				/>
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

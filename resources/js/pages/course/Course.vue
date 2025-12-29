<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import CourseCard from '@/pages/course/CourseCard.vue';
import SubjectManager from './CourseSchedule.vue';
const totalEnrollments = ref(0);
const totalSubjects = ref(0);
const unassignedTeachers = ref(0);


async function loadTotalEnrollments() {
	try {
		const res = await fetch('/api/enrollments/total', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch total enrollments');
		const data = await res.json();
		totalEnrollments.value = Number(data?.total_enrollments ?? 0);
	} catch (e) {
		
		}
}

async function loadTotalSubjects() {
	try {
		const res = await fetch('/api/subjects/total', { credentials: 'same-origin' });
		if (!res.ok) throw new Error('Failed to fetch total subjects');
		const data = await res.json();
		totalSubjects.value = Number(data?.total_subjects ?? 0);
	} catch (e) {
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

onMounted(async () => {
	await Promise.all([
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
							<h3 class="text-3xl font-bold text-gray-800 mt-2"></h3>
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
							<p class="text-xs text-gray-500 mt-2">Avg:  per course</p>
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

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				<CourseCard
				/>
			</div>
		</div>
		</div>
	</AppLayout>
</template>

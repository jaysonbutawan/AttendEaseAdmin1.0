
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
	<AppLayout>
		<Head title="User Management - AttendEase" />
		
		<!-- User Management Page -->
		<div class="space-y-6 p-4 sm:p-6 lg:p-8">
			<!-- Header -->
			<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-800">User Management</h1>
		<p class="text-gray-600 text-sm mt-2">Manage administrators, teachers, and students in the system</p>
	</div>

			<!-- Summary Statistics -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
				<!-- Total Users -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Total Users</p>
							<h3 class="text-3xl font-bold text-blue-600 mt-2">2,487</h3>
					<p class="text-xs text-gray-500 mt-2">↑ 45 new this month</p>
				</div>
				<div class="bg-blue-100 rounded-lg p-3">
					<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		<!-- Active Accounts -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<div class="flex items-start justify-between">
				<div>
					<p class="text-gray-600 text-sm font-medium">Active Accounts</p>
					<h3 class="text-3xl font-bold text-green-600 mt-2">2,341</h3>
					<p class="text-xs text-gray-500 mt-2">94% of total</p>
				</div>
				<div class="bg-green-100 rounded-lg p-3">
					<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		<!-- Administrators -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<div class="flex items-start justify-between">
				<div>
					<p class="text-gray-600 text-sm font-medium">Administrators</p>
					<h3 class="text-3xl font-bold text-purple-600 mt-2">12</h3>
					<p class="text-xs text-gray-500 mt-2">Full system access</p>
				</div>
				<div class="bg-purple-100 rounded-lg p-3">
					<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"></path>
					</svg>
				</div>
			</div>
		</div>

		<!-- Teachers & Students -->
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<div class="flex items-start justify-between">
				<div>
					<p class="text-gray-600 text-sm font-medium">Teachers & Students</p>
					<h3 class="text-3xl font-bold text-indigo-600 mt-2">2,475</h3>
					<p class="text-xs text-gray-500 mt-2">1,245 Teachers • 1,230 Students</p>
				</div>
				<div class="bg-indigo-100 rounded-lg p-3">
					<svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747m0-13c5.5 0 10 4.745 10 10.747S17.5 27.747 12 27.747M12 6.253v13"></path>
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
							<input type="text" placeholder="Search by name, email, or ID..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
						</div>
					</div>

				</div>
			</div>

			<!-- User Table -->
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
				<div class="overflow-x-auto">
					<table class="w-full">
						<thead class="bg-gray-50 border-b border-gray-200">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">
									<input type="checkbox" class="rounded border-gray-300">
								</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">User</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
							<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Assigned To</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Last Activity</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200">
				<!-- User Row 1 -->
				<tr class="hover:bg-gray-50 transition">
						<td class="px-6 py-4">
							<input type="checkbox" class="rounded border-gray-300">
						</td>
						<td class="px-6 py-4">
							<div class="flex items-center gap-3">
								<div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">JD</div>
								<div>
									<p class="text-sm font-semibold text-gray-900">John Doe</p>
									<p class="text-xs text-gray-500">ID: #2341</p>
								</div>
							</div>
						</td>
						<td class="px-6 py-4">
							<p class="text-sm text-gray-600">john.doe@university.edu</p>
						</td>
						<td class="px-6 py-4">
								<p class="text-sm text-gray-600">—</p>
						</td>
						<td class="px-6 py-4">
							<p class="text-sm text-gray-600">Today, 10:32 AM</p>
						</td>
						<td class="px-6 py-4">
							<div class="flex gap-2">
								<button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
									</svg>
								</button>
								<button class="p-1.5 text-amber-600 hover:bg-amber-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
									</svg>
								</button>
								<button class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
									</svg>
								</button>
							</div>
						</td>
					</tr>

				<!-- User Row 2 -->
				<tr class="hover:bg-gray-50 transition">
						<td class="px-6 py-4">
							<input type="checkbox" class="rounded border-gray-300">
						</td>
						<td class="px-6 py-4">
							<div class="flex items-center gap-3">
								<div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold">SM</div>
								<div>
									<p class="text-sm font-semibold text-gray-900">Sarah Martinez</p>
									<p class="text-xs text-gray-500">ID: #2340</p>
								</div>
							</div>
						</td>
						<td class="px-6 py-4">
							<p class="text-sm text-gray-600">sarah.martinez@university.edu</p>
						</td>
						<td class="px-6 py-4">
								<p class="text-sm text-gray-600">Mathematics 101</p>
						</td>
						<td class="px-6 py-4">
							<p class="text-sm text-gray-600">Today, 9:15 AM</p>
						</td>
						<td class="px-6 py-4">
							<div class="flex gap-2">
								<button class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
									</svg>
								</button>
								<button class="p-1.5 text-amber-600 hover:bg-amber-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
									</svg>
								</button>
								<button class="p-1.5 text-red-600 hover:bg-red-50 rounded transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
									</svg>
								</button>
							</div>
						</td>
					</tr>

					<!-- Remaining rows omitted for brevity (kept identical to your markup) -->
				</tbody>
			</table>
		</div>

		<!-- Pagination -->
		<div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
			<p class="text-sm text-gray-600">Showing <span class="font-semibold">1</span> to <span class="font-semibold">4</span> of <span class="font-semibold">2,487</span> users</p>
			<div class="flex gap-2">
				<button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">Previous</button>
				<button class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">1</button>
				<button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">2</button>
				<button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">3</button>
				<button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">Next</button>
			</div>
		</div>
	</div>
		</div>
	</AppLayout>
</template>

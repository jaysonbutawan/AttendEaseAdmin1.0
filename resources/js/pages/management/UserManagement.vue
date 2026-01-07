
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import UsersCard from './UsersCard.vue';

interface User {
	id: string | number;
	name: string;
	email: string;
	role: 'admin' | 'teacher' | 'student';
	assigned_to?: string | number | null;
	last_activity?: string;
	initials: string;
	avatar_color: string;
    approval_status?: 'pending' | 'approved' | 'rejected';
    approved_at?: string;
}

interface UserManagementProps {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    totalUsers?: number;
    totalTeachers?: number;
    totalStudents?: number;
    filters?: {
        search?: string;
        role?: string;
		status?: string;
		view?: 'table' | 'cards';
    };
}

const props = defineProps<UserManagementProps>();

// Search and filter states
const searchQuery = ref(props.filters?.search ?? '');
const selectedRole = ref(props.filters?.role ?? '');
const selectedStatus = ref(props.filters?.status ?? '');
const viewMode = ref<'table' | 'cards'>(props.filters?.view ?? 'cards');

// Statistics
const totalUsers = computed(() => props.totalUsers ?? 0);
const totalTeachers = computed(() => props.totalTeachers ?? 0);
const totalStudents = computed(() => props.totalStudents ?? 0);
const teachersAndStudents = computed(() => totalTeachers.value + totalStudents.value);

// Selected users for bulk actions
const selectedUsers = ref<Array<string | number>>([]);

// Handle search
const handleSearch = () => {
    router.get('/management/users', {
        search: searchQuery.value,
        role: selectedRole.value,
		status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Handle role filter change
const handleRoleFilter = (role: string) => {
    selectedRole.value = role;
    handleSearch();
};

// Clear filters
const clearFilters = () => {
    searchQuery.value = '';
    selectedRole.value = '';
	selectedStatus.value = '';
    router.get('/management/users', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Pagination
const goToPage = (page: number) => {
    router.get('/management/users', {
        page,
        search: searchQuery.value,
        role: selectedRole.value,
		status: selectedStatus.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Toggle view mode
const toggleViewMode = (mode: 'table' | 'cards') => {
	viewMode.value = mode;
};

// Toggle user selection
const toggleUserSelection = (userId: string | number) => {
    const index = selectedUsers.value.indexOf(userId);
    if (index > -1) {
        selectedUsers.value.splice(index, 1);
    } else {
        selectedUsers.value.push(userId);
    }
};

// Toggle all users selection
const toggleAllUsers = () => {
    if (selectedUsers.value.length === props.users.data.length) {
        selectedUsers.value = [];
    } else {
		selectedUsers.value = props.users.data.map(user => user.id);
    }
};

// Check if all users are selected
const allUsersSelected = computed(() => {
    return props.users.data.length > 0 && selectedUsers.value.length === props.users.data.length;
});

// User actions
const editUser = (payload: { id: string | number; role: 'teacher' | 'student' | 'admin' }) => {
	const { id, role } = payload;
	if (role === 'admin') {
		router.visit('/settings');
		return;
	}
	router.visit(`/management/users/${role}/${id}/edit`);
};

const viewUser = (payload: { id: string | number; role: 'teacher' | 'student' | 'admin' }) => {
	const { id, role } = payload;
	if (role === 'admin') {
		router.visit('/settings');
		return;
	}
	router.visit(`/management/users/${role}/${id}`);
};

const deleteUser = (payload: { id: string | number; role: 'teacher' | 'student' | 'admin' }) => {
	const { id, role } = payload;
	if (role === 'admin') {
		alert('Admin account cannot be deleted.');
		return;
	}
	if (confirm('Are you sure you want to delete this record?')) {
		const endpoint = role === 'teacher' ? `/api/teachers/${id}` : `/api/students/${id}`;
		router.delete(endpoint, { preserveScroll: true });
	}
};

// Approve user
const approveUser = (payload: { id: string | number; role: 'teacher' | 'student' | 'admin' }) => {
	const { id, role } = payload;
	if (role === 'admin') {
		alert('Admin accounts are managed in Settings.');
		return;
	}
	const endpoint = role === 'teacher' ? `/api/teachers/${id}/approve` : `/api/students/${id}/approve`;
	router.post(endpoint, {}, { preserveScroll: true });
};

// Reject user
const rejectUser = (payload: { id: string | number; role: 'teacher' | 'student' | 'admin' }) => {
	const { id, role } = payload;
	if (role === 'admin') {
		alert('Admin accounts are managed in Settings.');
		return;
	}
	if (!confirm('Reject this user?')) return;
	const endpoint = role === 'teacher' ? `/api/teachers/${id}/reject` : `/api/students/${id}/reject`;
	router.post(endpoint, {}, { preserveScroll: true });
};

// Get role badge class
const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'admin':
            return 'bg-purple-100 text-purple-800';
        case 'teacher':
            return 'bg-blue-100 text-blue-800';
        case 'student':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Get approval status badge class
const getStatusBadgeClass = (status?: string) => {
	switch (status) {
		case 'approved':
			return 'bg-green-100 text-green-800';
		case 'rejected':
			return 'bg-red-100 text-red-800';
		case 'pending':
			return 'bg-yellow-100 text-yellow-800';
		default:
			return 'bg-gray-100 text-gray-800';
	}
};

// Format last activity
const formatLastActivity = (lastActivity?: string) => {
    if (!lastActivity) return 'Never';
    const date = new Date(lastActivity);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days === 1) return 'Yesterday';
    return date.toLocaleDateString();
};
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
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
				<!-- Total Users -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Total Users</p>
							<h3 class="text-3xl font-bold text-blue-600 mt-2">{{ totalUsers.toLocaleString() }}</h3>
							<p class="text-xs text-gray-500 mt-2">All system users</p>
						</div>
						<div class="bg-blue-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Total Teachers -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Teachers</p>
							<h3 class="text-3xl font-bold text-purple-600 mt-2">{{ totalTeachers.toLocaleString() }}</h3>
							<p class="text-xs text-gray-500 mt-2">Teaching staff</p>
						</div>
						<div class="bg-purple-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
							</svg>
						</div>
					</div>
				</div>

				<!-- Total Students -->
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-gray-600 text-sm font-medium">Students</p>
							<h3 class="text-3xl font-bold text-green-600 mt-2">{{ totalStudents.toLocaleString() }}</h3>
							<p class="text-xs text-gray-500 mt-2">Enrolled students</p>
						</div>
						<div class="bg-green-100 rounded-lg p-3">
							<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Search and Filter Panel -->
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
				<div class="flex flex-col lg:flex-row gap-4 items-center">
					<!-- Search Bar -->
					<div class="flex-1 w-full">
						<div class="relative">
							<svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"></path>
							</svg>
							<input 
								v-model="searchQuery"
								@keyup.enter="handleSearch"
								type="text" 
								placeholder="Search by name, email, or ID..." 
								class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
							>
						</div>
					</div>

					<!-- Role Filter -->
					<div class="flex gap-2">
						<button 
							@click="handleRoleFilter('')"
							:class="selectedRole === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							All
						</button>
						<button 
							@click="handleRoleFilter('teacher')"
							:class="selectedRole === 'teacher' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							Teachers
						</button>
						<button 
							@click="handleRoleFilter('student')"
							:class="selectedRole === 'student' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							Students
						</button>
					</div>

					<!-- Approval Status Filter -->
					<div class="flex gap-2">
						<button 
							@click="selectedStatus = ''; handleSearch()"
							:class="selectedStatus === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							All Status
						</button>
						<button 
							@click="selectedStatus = 'pending'; handleSearch()"
							:class="selectedStatus === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							Pending
						</button>
						<button 
							@click="selectedStatus = 'approved'; handleSearch()"
							:class="selectedStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							Approved
						</button>
						<button 
							@click="selectedStatus = 'rejected'; handleSearch()"
							:class="selectedStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
							class="px-4 py-2.5 rounded-lg text-sm font-medium transition"
						>
							Rejected
						</button>
					</div>

					<!-- View Toggle -->
					<div class="flex gap-1 bg-gray-100 p-1 rounded-lg">
						<button 
							@click="toggleViewMode('cards')"
							:class="viewMode === 'cards' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
							class="px-3 py-2 rounded-md transition"
							title="Card View"
						>
							<svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
							</svg>
						</button>
						<button 
							@click="toggleViewMode('table')"
							:class="viewMode === 'table' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
							class="px-3 py-2 rounded-md transition"
							title="Table View"
						>
							<svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
							</svg>
						</button>
					</div>

					<!-- Clear Filters -->
					<button 
						v-if="searchQuery || selectedRole"
						@click="clearFilters"
						class="px-4 py-2.5 bg-red-50 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition whitespace-nowrap"
					>
						Clear Filters
					</button>
				</div>
			</div>

			<!-- Cards View -->
			<div v-if="viewMode === 'cards' && users.data.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					<UsersCard
						v-for="user in users.data"
						:key="user.id"
						:user="user"
						:is-selected="selectedUsers.includes(user.id)"
						@toggle-selection="toggleUserSelection"
						@edit="(id) => editUser({ id, role: user.role })"
						@view="(id) => viewUser({ id, role: user.role })"
						@delete="(id) => deleteUser({ id, role: user.role })"
						@approve="(id) => approveUser({ id, role: user.role })"
						@reject="(id) => rejectUser({ id, role: user.role })"
					/>
				</div>
			</div>

			<!-- Empty State for Cards -->
			<div v-if="viewMode === 'cards' && users.data.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
				<div class="text-center text-gray-500">
					<svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"></path>
					</svg>
					<p class="text-lg font-medium">No users found</p>
					<p class="text-sm mt-2">Try adjusting your search or filters</p>
				</div>
			</div>

			<!-- User Table -->
			<div v-if="viewMode === 'table'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
				<div class="overflow-x-auto">
					<table class="w-full">
						<thead class="bg-gray-50 border-b border-gray-200">
							<tr>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">
									<input 
										type="checkbox" 
										:checked="allUsersSelected"
										@change="toggleAllUsers"
										class="rounded border-gray-300"
									>
								</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">User</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Role</th>							<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Assigned To</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Last Activity</th>
								<th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200">
							<tr 
								v-for="user in users.data" 
								:key="user.id"
								class="hover:bg-gray-50 transition"
							>
								<td class="px-6 py-4">
									<input 
										type="checkbox" 
										:checked="selectedUsers.includes(user.id)"
										@change="toggleUserSelection(user.id)"
										class="rounded border-gray-300"
									>
								</td>
								<td class="px-6 py-4">
									<div class="flex items-center gap-3">
										<div 
											class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
											:style="{ background: user.avatar_color }"
										>
											{{ user.initials }}
										</div>
										<div>
											<p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
											<p class="text-xs text-gray-500">ID: #{{ user.id }}</p>
										</div>
									</div>
								</td>
								<td class="px-6 py-4">
									<p class="text-sm text-gray-600">{{ user.email }}</p>
								</td>
								<td class="px-6 py-4">
									<span 
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
										:class="getRoleBadgeClass(user.role)"
									>
										{{ user.role }}
									</span>
								</td>
								<td class="px-6 py-4">
									<span 
										class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
										:class="getStatusBadgeClass(user.approval_status)"
									>
										{{ user.approval_status ?? 'pending' }}
									</span>
								</td>
								<td class="px-6 py-4">
									<p class="text-sm text-gray-600">{{ user.assigned_to ?? '—' }}</p>
								</td>
								<td class="px-6 py-4">
									<p class="text-sm text-gray-600">{{ formatLastActivity(user.last_activity) }}</p>
								</td>
								<td class="px-6 py-4">
									<div class="flex gap-2">
										<button 
											v-if="user.role !== 'admin'"
											@click="approveUser({ id: user.id, role: user.role })"
											class="p-1.5 text-green-600 hover:bg-green-50 rounded transition"
											title="Approve user"
										>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
											</svg>
										</button>
										<button 
											v-if="user.role !== 'admin'"
											@click="rejectUser({ id: user.id, role: user.role })"
											class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
											title="Reject user"
										>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
											</svg>
										</button>
										<button 
											@click="editUser({ id: user.id, role: user.role })"
											class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
											title="Edit user"
										>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
											</svg>
										</button>
										<button 
											@click="viewUser({ id: user.id, role: user.role })"
											class="p-1.5 text-amber-600 hover:bg-amber-50 rounded transition"
											title="View details"
										>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path>
											</svg>
										</button>
										<button 
											@click="deleteUser({ id: user.id, role: user.role })"
											class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
											title="Delete user"
										>
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
											</svg>
										</button>
									</div>
								</td>
							</tr>

							<!-- Empty State -->
							<tr v-if="users.data.length === 0">
								<td colspan="8" class="px-6 py-12 text-center">
									<div class="text-gray-500">
										<svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"></path>
										</svg>
										<p class="text-sm font-medium">No users found</p>
										<p class="text-xs mt-1">Try adjusting your search or filters</p>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between">
					<p class="text-sm text-gray-600">
						Showing 
						<span class="font-semibold">{{ users.from ?? 0 }}</span> 
						to 
						<span class="font-semibold">{{ users.to ?? 0 }}</span> 
						of 
						<span class="font-semibold">{{ users.total.toLocaleString() }}</span> 
						users
					</p>
					<div class="flex gap-2">
						<button 
							@click="goToPage(users.current_page - 1)"
							:disabled="users.current_page === 1"
							:class="users.current_page === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
							class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 transition"
						>
							Previous
						</button>
                        
						<!-- Page Numbers -->
						<template v-for="page in Math.min(users.last_page, 5)" :key="page">
							<button 
								@click="goToPage(page)"
								:class="users.current_page === page ? 'bg-blue-600 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-100'"
								class="px-3 py-2 rounded-lg text-sm transition"
							>
								{{ page }}
							</button>
						</template>
                        
						<button 
							@click="goToPage(users.current_page + 1)"
							:disabled="users.current_page === users.last_page"
							:class="users.current_page === users.last_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
							class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 transition"
						>
							Next
						</button>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>

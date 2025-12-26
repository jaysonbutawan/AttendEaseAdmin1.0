<script setup lang="ts">
interface SubjectCardProps {
	subject: {
		id: number;
		name: string;
		code?: string;
		course?: string;
		teacher?: string | null;
		status?: string;
		gradient?: string;
	};
	onEdit?: () => void;
	onViewDetails?: () => void;
}

const props = defineProps<SubjectCardProps>();
const gradient = props.subject.gradient || 'from-blue-500 to-indigo-600';
</script>

<template>
	<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
		<div :class="`h-2 bg-gradient-to-r ${gradient}`"></div>
		<div class="p-6">
			<!-- Header -->
			<div class="flex items-start justify-between mb-4">
				<div>
					<h3 class="text-lg font-bold text-gray-900">{{ subject.name }}</h3>
					<p v-if="subject.code" class="text-xs text-gray-500 font-mono mt-1">{{ subject.code }}</p>
				</div>
				<span
					v-if="subject.status"
					:class="[
						'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
						subject.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
					]"
				>
					{{ subject.status }}
				</span>
			</div>

			<!-- Meta -->
			<div class="space-y-3 mb-4">
				<div class="flex items-center gap-2">
					<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-6-6h12" />
					</svg>
					<p class="text-sm text-gray-600">{{ subject.course || 'Course not assigned' }}</p>
				</div>
				<div class="flex items-center gap-2">
					<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z" />
					</svg>
					<p class="text-sm text-gray-600">
						<span v-if="subject.teacher">{{ subject.teacher }}</span>
						<span v-else class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Unassigned</span>
					</p>
				</div>
				<div class="flex items-center gap-2">
					<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M3 12h18M3 19h18" />
					</svg>
					<p class="text-sm text-gray-600">Subject ID: {{ subject.id }}</p>
				</div>
			</div>

			<!-- Actions -->
			<div class="flex gap-2 pt-4 border-t border-gray-200">
				<button
					@click="onEdit?.()"
					class="flex-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
				>
					Edit
				</button>
				<button
					@click="onViewDetails?.()"
					class="flex-1 px-3 py-2 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm font-medium"
				>
					View Details
				</button>
			</div>
		</div>
	</div>
</template>

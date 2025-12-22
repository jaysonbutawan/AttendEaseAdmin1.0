<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type NavLink = {
    label: string;
    href: string;
    disabled?: boolean;
};

const page = usePage();
const currentUrl = computed(() => page.url);
const user = computed(() => page.props.auth?.user);

const navSections: { title: string; items: NavLink[] }[] = [
    {
        title: 'Management',
        items: [
            { label: 'Dashboard', href: '/dashboard' },
            { label: 'User Management', href: '/users', disabled: true },
            { label: 'Courses', href: '/courses' },
        ],
    },
    {
        title: 'Monitoring',
        items: [
            { label: 'Attendance Monitoring', href: '/attendance', disabled: true },
            { label: 'Rooms', href: '/rooms' },
            { label: 'Active Sessions', href: '/sessions', disabled: true },
        ],
    },
    {
        title: 'Academic',
        items: [
            { label: 'Subjects', href: '/subjects' },
            { label: 'Student Lists', href: '/students' },
            { label: 'Teachers', href: '/teachers' },
        ],
    },
    {
        title: 'Reports',
        items: [
            { label: 'Attendance Reports', href: '/reports/attendance', disabled: true },
            { label: 'Analytics', href: '/reports/analytics', disabled: true },
        ],
    },
];

const isActive = (href: string) =>
    currentUrl.value === href || currentUrl.value.startsWith(`${href}/`);

const linkClasses = (href: string, disabled = false) => {
    const base = 'flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all';

    if (disabled) {
        return `${base} text-gray-400 cursor-not-allowed bg-transparent`;
    }

    if (isActive(href)) {
        return `${base} text-white bg-blue-600 hover:bg-blue-700`;
    }

    return `${base} text-gray-700 hover:bg-gray-100`;
};

const userInitials = computed(() => {
    const name = user.value?.name || user.value?.email || 'User';
    const initials = name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((segment) => segment[0]?.toUpperCase())
        .join('')
        .slice(0, 2);

    return initials || 'JD';
});

const logout = () => router.post('/logout');
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="bg-white shadow-md border-r border-gray-200"
    >
        <SidebarHeader class="px-4 pt-6 pb-4">
            <Link :href="'/dashboard'" class="inline-flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center"
                >
                    <svg
                        class="w-6 h-6 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">AttendEase</p>
                    <p class="text-xs text-gray-500">Attendance System</p>
                </div>
            </Link>
        </SidebarHeader>

        <SidebarContent class="px-2 pb-6 space-y-6">
            <div v-for="section in navSections" :key="section.title" class="space-y-3">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    {{ section.title }}
                </h3>
                <ul class="space-y-2 px-2">
                    <li v-for="item in section.items" :key="item.label">
                        <Link
                            v-if="!item.disabled"
                            :href="item.href"
                            :class="linkClasses(item.href)"
                        >
                            <span>{{ item.label }}</span>
                        </Link>
                        <span
                            v-else
                            :class="linkClasses(item.href, true)"
                            aria-disabled="true"
                            role="link"
                        >
                            <span>{{ item.label }}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </SidebarContent>

        <SidebarFooter class="px-4 pb-6 pt-4 mt-auto border-t border-gray-200">
            <div class="px-3 py-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-900">
                            {{ user?.name ?? 'Administrator' }}
                        </p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="logout"
                    class="w-full px-2 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-all flex items-center justify-center gap-1"
                >
                    Logout
                </button>
            </div>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

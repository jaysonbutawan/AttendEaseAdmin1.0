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
    icon: string;
    disabled?: boolean;
};

const page = usePage();
const currentUrl = computed(() => page.url);
const user = computed(() => page.props.auth?.user);

const navSections: { title: string; items: NavLink[] }[] = [
    {
        title: 'Management',
        items: [
            { label: 'Dashboard', href: '/dashboard', icon: 'M3 12l2-4m0 0l7-4 7 4M5 8v10a1 1 0 001 1h12a1 1 0 001-1V8m-9 4h4' },
            { label: 'User Management', href: '/usermanagement', icon: 'M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z' },
            { label: 'Courses', href: '/courses', icon: 'M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.992 10-10.747 0-6.002-4.5-10.747-10-10.747z' },
        ],
    },
    {
        title: 'Monitoring',
        items: [
            { label: 'Attendance Monitoring', href: '/attendance', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', disabled: true },
            { label: 'Rooms', href: '/rooms', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9.11m3.409 0H15.5m-3.409 0H7m8-6.5h-5' },
            { label: 'Active Sessions', href: '/sessions', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
        ],
    },
    {
        title: 'Academic',
        items: [
            { label: 'Subjects', href: '/subjects', icon: 'M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.992 10-10.747 0-6.002-4.5-10.747-10-10.747z' },
            { label: 'Student Lists', href: '/students', icon: 'M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            { label: 'Teachers', href: '/teachers', icon: 'M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z' },
        ],
    },
    {
        title: 'Reports',
        items: [
            { label: 'Attendance Reports', href: '/reports/attendance', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', disabled: true },
            { label: 'Analytics', href: '/reports/analytics', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', disabled: true },
        ],
    },
];

const isActive = (href: string) =>
    currentUrl.value === href || currentUrl.value.startsWith(`${href}/`);

const linkClasses = (href: string, disabled = false) => {
    const base = 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap';

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
                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"
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
                <div class="hidden lg:block min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">AttendEase</p>
                    <p class="text-xs text-gray-500 truncate">Attendance System</p>
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
                            :title="item.label"
                        >
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                            <span class="hidden lg:inline">{{ item.label }}</span>
                        </Link>
                        <span
                            v-else
                            :class="linkClasses(item.href, true)"
                            aria-disabled="true"
                            role="link"
                            :title="item.label"
                        >
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                            <span class="hidden lg:inline">{{ item.label }}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </SidebarContent>

        <SidebarFooter class="px-4 pb-6 pt-4 mt-auto border-t border-gray-200">
            <div class="px-3 py-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center gap-2 mb-3 min-w-0">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="hidden lg:block min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">
                            {{ user?.name ?? 'Administrator' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">Administrator</p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="logout"
                    class="w-full px-2 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-all flex items-center justify-center gap-1"
                    title="Logout"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="hidden lg:inline">Logout</span>
                </button>
            </div>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

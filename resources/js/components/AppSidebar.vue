<script setup lang="ts">
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import NavUser from '@/components//NavUser.vue';

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
            { label: 'Attendance Monitoring', href: '/attendance', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
            { label: 'Rooms', href: '/rooms', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9.11m3.409 0H15.5m-3.409 0H7m8-6.5h-5' },
            { label: 'Active Sessions', href: '/sessions', icon: 'M13 10V3L4 14h7v7l9-11h-7z' },
        ],
    },
    {
        title: 'Academic',
        items: [
            { label: 'Departments', href: '/department', icon: 'M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1zm3 0v16m10-16v16M9 9h6m-6 4h6' },
            { label: 'Subjects', href: '/subjects', icon: 'M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.992 10-10.747 0-6.002-4.5-10.747-10-10.747z' },
            { label: 'Student Lists', href: '/students', icon: 'M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
            { label: 'Teachers', href: '/teachers', icon: 'M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z' },
        ],
    },
];

const isActive = (href: string) =>
    currentUrl.value === href || currentUrl.value.startsWith(`${href}/`);

const linkClasses = (href: string, disabled = false) => {
    const base = 'group relative flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-300 overflow-hidden';

    if (disabled) {
        return `${base} text-gray-400 cursor-not-allowed`;
    }

    if (isActive(href)) {
        return `${base} text-white bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/30`;
    }

    return `${base} text-gray-700 hover:text-indigo-700`;
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
            <Link :href="'/dashboard'" class="inline-flex items-center gap-3 group">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg"
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
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-gray-900 truncate">AttendEase</p>
                    <p class="text-xs text-gray-500 truncate">Attendance System</p>
                </div>
            </Link>
        </SidebarHeader>

        <SidebarContent class="px-3 pb-6 space-y-6">
            <div v-for="section in navSections" :key="section.title" class="space-y-2">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    {{ section.title }}
                </h3>
                <ul class="space-y-1">
                    <li v-for="item in section.items" :key="item.label">
                        <Link
                            v-if="!item.disabled"
                            :href="item.href"
                            :class="linkClasses(item.href)"
                            :title="item.label"
                        >
                            <!-- Animated Background on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                            
                            <!-- Shimmer Effect -->
                            <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                            
                            <!-- Icon -->
                            <svg 
                                class="w-5 h-5 flex-shrink-0 relative z-10 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                            
                            <!-- Label - Always visible, adjusts with screen size -->
                            <span class="relative z-10 truncate text-xs sm:text-sm font-medium">
                                {{ item.label }}
                            </span>

                            <!-- Active Indicator -->
                            <div 
                                v-if="isActive(item.href)"
                                class="absolute right-2 w-1.5 h-1.5 bg-white rounded-full animate-pulse"
                            ></div>
                        </Link>
                        
                        <span
                            v-else
                            :class="linkClasses(item.href, true)"
                            aria-disabled="true"
                            role="link"
                            :title="`${item.label} (Coming Soon)`"
                        >
                            <!-- Disabled Background -->
                            <div class="absolute inset-0 bg-gray-100/50 rounded-xl"></div>
                            
                            <!-- Icon -->
                            <svg 
                                class="w-5 h-5 flex-shrink-0 relative z-10 opacity-50" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                            </svg>
                            
                            <!-- Label -->
                            <span class="relative z-10 truncate text-xs sm:text-sm">
                                {{ item.label }}
                            </span>
                            
                            <!-- Coming Soon Badge -->
                            <span class="ml-auto text-[10px] bg-gray-200 text-gray-500 px-1.5 py-0.5 rounded relative z-10">
                                Soon
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

<style scoped>
/* Smooth animations */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Ensure smooth transitions */
* {
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for sidebar content */
:deep(.sidebar-content) {
  scrollbar-width: thin;
  scrollbar-color: rgba(99, 102, 241, 0.3) transparent;
}

:deep(.sidebar-content::-webkit-scrollbar) {
  width: 4px;
}

:deep(.sidebar-content::-webkit-scrollbar-track) {
  background: transparent;
}

:deep(.sidebar-content::-webkit-scrollbar-thumb) {
  background-color: rgba(99, 102, 241, 0.3);
  border-radius: 20px;
}

:deep(.sidebar-content::-webkit-scrollbar-thumb:hover) {
  background-color: rgba(99, 102, 241, 0.5);
}
</style>
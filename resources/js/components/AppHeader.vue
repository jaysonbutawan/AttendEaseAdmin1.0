<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import { dashboard, students, teachers } from '@/routes';
import type { BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : 'hover:text-blue-600',
);

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Students',
        href: students(),
    },
    {
        title: 'Teachers',
        href: teachers(),
    },
];
</script>

<template>
    <div>
        <div class="border-b border-sidebar-border/80">
            <div
                class="flex h-16 w-full items-center px-4 md:h-20 md:px-8 lg:h-24"
            >
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
                            >
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only"
                                >Navigation Menu</SheetTitle
                            >
                            <div
                                class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
                            >
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 font-medium hover:bg-accent"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        {{ item.title }}
                                        <span
                                            class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-indigo-600 opacity-0 transition-opacity duration-200"
                                            :class="{
                                                'opacity-100': isCurrentRoute(
                                                    item.href,
                                                ),
                                            }"
                                        ></span>
                                    </Link>
                                </nav>
                            </div>

                            <div class="mt-4 border-t pt-4">
                                <DropdownMenu>
                                    <DropdownMenuTrigger :as-child="true">
                                        <Button
                                            variant="ghost"
                                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 hover:bg-accent"
                                        >
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <Avatar
                                                    class="size-9 overflow-hidden rounded-full"
                                                >
                                                    <AvatarImage
                                                        v-if="auth.user.avatar"
                                                        :src="auth.user.avatar"
                                                        :alt="auth.user.name"
                                                    />
                                                    <AvatarFallback
                                                        class="rounded-full bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                                    >
                                                        {{
                                                            getInitials(
                                                                auth.user?.name,
                                                            )
                                                        }}
                                                    </AvatarFallback>
                                                </Avatar>

                                                <div
                                                    class="flex flex-col items-start"
                                                >
                                                    <span
                                                        class="text-sm font-semibold text-neutral-900 dark:text-neutral-50"
                                                    >
                                                        {{ auth.user?.name }}
                                                    </span>
                                                    <span
                                                        class="max-w-[180px] truncate text-xs text-neutral-500 dark:text-neutral-400"
                                                    >
                                                        {{ auth.user?.email }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- little chevron indicator (optional) -->
                                            <span
                                                class="text-xs text-neutral-500"
                                                >▼</span
                                            >
                                        </Button>
                                    </DropdownMenuTrigger>

                                    <DropdownMenuContent
                                        align="start"
                                        class="w-56"
                                    >
                                        <UserMenuContent :user="auth.user" />
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link :href="dashboard()" class="flex items-center gap-x-2">
                    <AppLogo
                        class="size-10 fill-current text-black dark:text-white"
                    />
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden flex-1 justify-end lg:flex">
                    <NavigationMenu class="flex h-full items-stretch">
                        <NavigationMenuList
                            class="flex h-full items-stretch space-x-2"
                        >
                            <NavigationMenuItem
                                v-for="(item, index) in mainNavItems"
                                :key="index"
                                class="relative flex h-full items-center"
                            >
                                <Link
                                    :href="item.href"
                                    :class="[
                                        navigationMenuTriggerStyle(),
                                        'nav-item !text-base text-gray-500', // use nav-item class
                                        activeItemStyles(item.href),
                                        { active: isCurrentRoute(item.href) }, // adds .active when current
                                    ]"
                                >
                                    {{ item.title }}
                                </Link>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>

                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar
                                    class="size-8 overflow-hidden rounded-full"
                                >
                                    <AvatarImage
                                        v-if="auth.user.avatar"
                                        :src="auth.user.avatar"
                                        :alt="auth.user.name"
                                    />
                                    <AvatarFallback
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>
    </div>

    <div
        v-if="props.breadcrumbs.length > 1"
        class="flex w-full border-b border-sidebar-border/70"
    >
        <div
            class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl"
        >
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>
    </div>
</template>

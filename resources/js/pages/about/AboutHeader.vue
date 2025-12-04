<script setup lang="ts">
import type { LandingPageController } from '@/controllers/LandingPageController';
import { login } from '@/routes';
import { Link } from '@inertiajs/vue3';

// import the same UI pieces you use in the app layout
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { Menu } from 'lucide-vue-next';

const props = defineProps<{
    controller: LandingPageController;
    canRegister: boolean;
}>();
</script>

<template>
    <div class="flex w-full items-center justify-between bg-transparent">
        <!-- Logo Section -->
        <div class="flex-shrink-0 text-left">
            <p class="text-sm text-gray-500">
                <span class="text-2xl font-bold text-black">Attend</span>
                <span class="text-2xl font-bold text-indigo-600">Ease</span>.
            </p>
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden flex-1 items-center justify-end space-x-6 lg:flex">
            <nav class="flex items-center space-x-20">
                <div
                    id="dynamic-content-container"
                    class="desktop-flex-grow flex h-full items-center justify-end"
                >
                    <nav
                        id="nav-links"
                        :class="{
                            hidden: props.controller.isLoginFormVisible.value,
                        }"
                        class="flex h-full items-center lg:mr-4 space-x-6 lg:space-x-8"
                    >
                        <a
                            href="#faq-section"
                            :class="[
                                'nav-item text-gray-500 hover:text-indigo-600',
                                {
                                    active: props.controller.isLinkActive(
                                        'faq-section',
                                    ),
                                },
                            ]"
                        >
                            FAQ
                        </a>

                        <a
                            href="#help-section"
                            :class="[
                                'nav-item text-gray-500 hover:text-indigo-600',
                                {
                                    active: props.controller.isLinkActive(
                                        'help-section',
                                    ),
                                },
                            ]"
                        >
                            Help & Downloads
                        </a>

                        <a
                            href="#contacts-section"
                            :class="[
                                'nav-item text-gray-500 hover:text-indigo-600',
                                {
                                    active: props.controller.isLinkActive(
                                        'contacts-section',
                                    ),
                                },
                            ]"
                        >
                            Contact Us
                        </a>

                        <a
                            href="#about-section"
                            :class="[
                                'nav-item text-gray-500 hover:text-indigo-600',
                                {
                                    active: props.controller.isLinkActive(
                                        'about-section',
                                    ),
                                },
                            ]"
                        >
                            About Us
                        </a>
                    </nav>
                </div>

                <Link
                    :href="login()"
                    class="transform rounded-xl border border-gray-300 bg-white px-8 py-3 text-lg font-semibold text-gray-700 shadow-md transition duration-300 hover:scale-105 hover:bg-gray-50"
                >
                    Log in
                </Link>
            </nav>
        </div>

        <!-- Mobile Menu (Sheet) -->
        <div class="lg:hidden">
            <Sheet>
                <SheetTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        <Menu class="h-5 w-5" />
                    </Button>
                </SheetTrigger>

                <SheetContent side="left" class="w-[280px] p-6">
                    <SheetTitle class="sr-only">
                        Navigation Menu
                    </SheetTitle>

                    <div
                        class="flex h-full flex-1 flex-col justify-between space-y-6 py-4"
                    >
                        <!-- Top: nav links -->
                        <nav class="-mx-3 space-y-1">
                            <a
                                href="#faq-section"
                                class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                :class="{
                                    'text-indigo-600 font-semibold':
                                        props.controller.isLinkActive(
                                            'faq-section',
                                        ),
                                }"
                            >
                                FAQ
                            </a>

                            <a
                                href="#help-section"
                                class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                :class="{
                                    'text-indigo-600 font-semibold':
                                        props.controller.isLinkActive(
                                            'help-section',
                                        ),
                                }"
                            >
                                Help & Downloads
                            </a>

                            <a
                                href="#contacts-section"
                                class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                :class="{
                                    'text-indigo-600 font-semibold':
                                        props.controller.isLinkActive(
                                            'contacts-section',
                                        ),
                                }"
                            >
                                Contact Us
                            </a>

                            <a
                                href="#about-section"
                                class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                :class="{
                                    'text-indigo-600 font-semibold':
                                        props.controller.isLinkActive(
                                            'about-section',
                                        ),
                                }"
                            >
                                About Us
                            </a>
                        </nav>

                        <!-- Bottom: auth actions -->
                        <div class="mt-4 border-t pt-4">
                            <Link
                                :href="login()"
                                class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Log in
                            </Link>
                        </div>
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </div>
</template>

<script setup lang="ts">
import { about, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>
<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="font-inter flex min-h-screen flex-col items-center bg-gray-50 p-6 text-gray-800 dark:bg-gray-900 dark:text-gray-200"
    >
        <header
            class="max-w-9xl flex w-full items-center justify-between px-4 py-4"
        >
            <div class="ml-30 grid flex-1 text-left text-xl">
                <p class="text-sm text-gray-500">
                    <span class="text-2xl font-bold text-black">Attend</span>
                    <span class="text-2xl font-bold text-indigo-600">Ease</span
                    >.
                </p>
            </div>

            <!-- Auth Links -->
            <nav class="flex items-center justify-end space-x-3">
                <!-- This would be an about Button -->
                <Link
                    :href="about()"
                    class="rounded-lg px-4 py-2 text-lg font-medium text-indigo-600 transition duration-150 hover:bg-indigo-50"
                >
                    About
                </Link>
            </nav>
        </header>

        <!-- MAIN HERO CONTENT (The center design) -->
        <main
            class="flex w-full flex-grow flex-col items-center justify-center text-center"
        >
            <h1
                class="text-6xl leading-tight font-extrabold tracking-tighter md:text-8xl"
            >
                Attendance <span class="text-indigo-600">Simplified</span>.
            </h1>
            <p class="mt-6 max-w-3xl text-xl text-gray-600 dark:text-gray-400">
                AttendEase is the smart platform for seamless student attendance
                tracking, teacher management, and subject organization.
            </p>

            <!-- CALL TO ACTION BUTTONS (Updated to Login and Register) -->
            <div
                class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row"
            >
                <Link
                    :href="login()"
                    class="transform rounded-xl bg-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg transition duration-300 hover:scale-105 hover:bg-indigo-700"
                >
                    Login
                </Link>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="transform rounded-xl border border-gray-300 bg-white px-8 py-3 text-lg font-semibold text-gray-700 shadow-md transition duration-300 hover:scale-105 hover:bg-gray-50"
                >
                    Register
                </Link>
            </div>
        </main>

       <footer
            class="w-full py-2 text-center text-lg text-gray-500 dark:border-gray-700"
        >
            <p class="text-sm text-gray-500">
                <span class="text-2xl font-bold text-black">Attend</span>
                <span class="text-2xl font-bold text-indigo-600">Ease</span>.
                All rights reserved. © {{ new Date().getFullYear() }}
            </p>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { about, dashboard, login, register } from '@/routes';
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
        class="flex min-h-screen flex-col items-center bg-gray-50 p-6 text-gray-800 dark:bg-gray-900 dark:text-gray-200 font-inter"
    >
        <!-- NAVIGATION BAR (Top Header) -->
        <header
            class="w-full max-w-7xl px-4 py-4 flex justify-between items-center"
        >
            <!-- Logo/App Name (Adjust this to match your logo component) -->
            <div class="flex items-center space-x-2 text-xl font-bold text-gray-800 dark:text-white">
                <!-- If using an icon component, uncomment and replace -->
                <!-- <AppLogoIcon class="w-8 h-8 fill-current text-indigo-600" /> -->
                AttendEase
            </div>

            <!-- Auth Links -->
            <nav class="flex items-center justify-end space-x-3">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-md hover:bg-indigo-700 transition duration-150"
                >
                    Go to Dashboard
                </Link>
                <template v-else>
                    <!-- This would be an about Button -->
                    <Link
                        :href="about()" 
                        class="rounded-lg px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 transition duration-150"
                    >
                        About
                    </Link>
                   
                </template>
            </nav>
        </header>

        <!-- MAIN HERO CONTENT (The center design) -->
        <main class="flex flex-col items-center justify-center flex-grow text-center max-w-4xl pt-20 pb-40">
            <h1 class="text-6xl md:text-8xl font-extrabold leading-tight tracking-tighter">
                Attendance <span class="text-indigo-600">Simplified</span>.
            </h1>
            <p class="mt-6 text-xl text-gray-600 dark:text-gray-400 max-w-3xl">
                AttendEase is the smart platform for seamless student attendance tracking, teacher management, and subject organization.
            </p>

            <!-- CALL TO ACTION BUTTONS (Updated to Login and Register) -->
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link
                    :href="login()"
                    class="rounded-xl bg-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-700 transition duration-300 transform hover:scale-105"
                >
                    Login
                </Link>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="rounded-xl border border-gray-300 bg-white px-8 py-3 text-lg font-semibold text-gray-700 shadow-md hover:bg-gray-50 transition duration-300 transform hover:scale-105"
                >
                    Register
                </Link>
            </div>
        </main>
        
        <!-- Placeholder for a simple footer or extra content if needed -->
        <footer class="w-full max-w-7xl py-4 text-center text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} AttendEase. All rights reserved.
        </footer>
    </div>
</template>

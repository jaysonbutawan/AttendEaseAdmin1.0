<script setup lang="ts">
import { dashboard, login, register } from '@/routes'; 
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const features = [
    {
        title: 'For Students',
        description: 'Effortlessly track your attendance, view schedules, and stay on top of your academic commitments.',
        imageUrl: 'https://scontent.fcgy1-2.fna.fbcdn.net/v/t39.30808-6/482223881_1717679305481311_6331821698277136086_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=669761&_nc_ohc=xCQsqdmqpFkQ7kNvwGfZ5ZW&_nc_oc=AdmNf4bn6S2qRLsozcEB4BUAK7YLrAaxakUGk9RYDwKZsKzsd6luB_m9jQMlkz4QQXY&_nc_zt=23&_nc_ht=scontent.fcgy1-2.fna&_nc_gid=OUC2vBFJzvqiANx22cN8Ww&oh=00_AfjdCXTgQqozR5XA6hbh5Pe_9uW6lVHECqNvKh8IFtSkIw&oe=6930342B',
    },
    {
        title: 'For Teachers',
        description: 'Streamline attendance taking, manage your classes, and generate reports with just a few clicks.',
        imageUrl: 'https://www.trackinghappiness.com/wp-content/uploads/2023/01/woman-sitting-at-table-with-notepad-laughing.jpg',
    },
    {
        title: 'For Institutions',
        description: 'Gain valuable insights into attendance trends and improve overall institutional efficiency and student success.',
        imageUrl: 'https://th.bing.com/th/id/OIP.2_QYmZEVZuCnh5yYo7z2pQHaE_?w=265&h=180&c=7&r=0&o=7&dpr=1.6&pid=1.7&rm=3',
    },
];
</script>

<template>
    <Head title="About Us">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200 font-inter p-6"
    >
        <!-- NAVIGATION BAR (Consistent with Welcome.vue) -->
        <header
            class="w-full max-w-7xl px-4 py-4 flex justify-between items-center sticky top-0 bg-gray-50 dark:bg-gray-900 z-10"
        >
            <!-- Logo/App Name -->
            <div class="flex items-center space-x-2 text-xl font-bold text-gray-800 dark:text-white">
                AttendEase
            </div>

            <!-- Nav Links -->
            <nav class="flex items-center justify-end space-x-3">
                    <!-- Login Button -->
                    <Link
                        :href="login()"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition duration-150 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Log in
                    </Link>
                    <!-- Register Button (Primary CTA) -->
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-md hover:bg-indigo-700 transition duration-150"
                    >
                        Register
                    </Link>
            </nav>
        </header>

        <!-- MAIN ABOUT CONTENT -->
        <main class="flex flex-col items-center flex-grow text-center max-w-6xl pt-16 pb-24 px-4 sm:px-6">
            
            <!-- Mission Section -->
            <section class="max-w-3xl">
                <h2 class="text-4xl md:text-8xl font-extrabold leading-tight tracking-tighter text-gray-900 dark:text-white">
                    Our Mission: 
                    <span class="text-indigo-600">Attendance Simplified.</span>
                </h2>
                <p class="mt-6 text-lg text-gray-600 dark:text-gray-400">
                    We are dedicated to creating the most seamless and intuitive platform for educational institutions to manage attendance, empowering teachers and administrators to focus on what truly matters: education.
                </p>
            </section>

            <!-- Target Audience / Feature Section -->
            <section class="mt-20 w-full">
                <h3 class="sr-only">Our Users</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-5xl mx-auto">
                    
                    <div v-for="feature in features" :key="feature.title" class="flex flex-col items-center text-center p-6">
                        <!-- Image placeholder matching the circular image style -->
                        <div class="w-40 h-40 rounded-full overflow-hidden shadow-xl ring-4 ring-indigo-50/50 dark:ring-gray-700/50">
                            <img :src="feature.imageUrl" :alt="feature.title" class="w-full h-full object-cover"/>
                        </div>
                        
                        <h4 class="mt-8 text-xl font-bold text-gray-900 dark:text-white">
                            {{ feature.title }}
                        </h4>
                        <p class="mt-3 text-base text-gray-600 dark:text-gray-400">
                            {{ feature.description }}
                        </p>
                    </div>

                </div>
            </section>

        </main>
        
        <!-- Footer (Consistent) -->
        <footer class="w-full max-w-7xl py-8 text-center text-sm text-gray-500 border-t border-gray-200 dark:border-gray-700">
            &copy; {{ new Date().getFullYear() }} AttendEase. All rights reserved.
        </footer>
    </div>
</template>
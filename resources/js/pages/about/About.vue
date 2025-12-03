<script setup lang="ts">
import { login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

import { onMounted, onUnmounted, ref } from 'vue';
import AttendEaseHeader from '@/pages/about/AboutHeader.vue';
import {
  LandingPageController,
  Slide,
  Feature,
} from '@/controllers/LandingPageController';



// controller instance


// Define the carousel slides data
const slides: Slide[] = [
    {
        src: 'https://placehold.co/800x600/5C6BC0/FFFFFF?text=AttendEase+Screen+1',
        alt: 'Attendance Summary Screen',
    },
    {
        src: 'https://placehold.co/800x600/5C6BC0/FFFFFF?text=AttendEase+Screen+2',
        alt: 'Check-in QR Code Scanner',
    },
    {
        src: 'https://placehold.co/800x600/5C6BC0/FFFFFF?text=AttendEase+Screen+3',
        alt: 'Student Records List',
    },
];


const currentSlideIndex = ref(0);
const activeSection = ref('faq-section'); // Default active section
const loginMessage = ref(''); // For showing authentication messages

const handleContactSubmit = (event: Event) => {
    event.preventDefault();
    loginMessage.value = 'Inquiry submitted successfully!';
    setTimeout(() => {
        loginMessage.value = '';
        (event.target as HTMLFormElement).reset();
    }, 3000);
};

// --- Carousel Logic ---

let autoSlideTimer: number | undefined;

const nextSlide = () => {
    currentSlideIndex.value = (currentSlideIndex.value + 1) % slides.length;
};

const prevSlide = () => {
    currentSlideIndex.value =
        (currentSlideIndex.value - 1 + slides.length) % slides.length;
};

const goToSlide = (index: number) => {
    currentSlideIndex.value = index;
};

const startAutoSlide = () => {
    if (typeof window !== 'undefined') {
        autoSlideTimer = setInterval(nextSlide, 3000);
    }
};

const stopAutoSlide = () => {
    if (typeof autoSlideTimer !== 'undefined') {
        clearInterval(autoSlideTimer);
    }
};

// --- Scroll/Active Link Logic (Replacing `active` class in static HTML) ---

const sectionIds = [
    'faq-section',
    'help-section',
    'contacts-section',
    'about-section',
];

const updateActiveSection = () => {
    let currentActive: string = 'faq-section'; // Default to first section
    const windowHeight = window.innerHeight;

    for (const id of sectionIds) {
        const section = document.getElementById(id);
        if (section) {
            const rect = section.getBoundingClientRect();
            // Check if the section's top edge is visible and relatively close to the top of the viewport
            // Using 30% of the viewport height as the activation threshold
            if (
                rect.top <= windowHeight * 0.3 &&
                rect.bottom >= windowHeight * 0.3
            ) {
                currentActive = id;
            }
        }
    }
    activeSection.value = currentActive;
};

onMounted(() => {
    startAutoSlide();
    window.addEventListener('resize', updateActiveSection);
    window.addEventListener('scroll', updateActiveSection);
    updateActiveSection(); // Initial check on load
});

onUnmounted(() => {
    stopAutoSlide();
    window.removeEventListener('resize', updateActiveSection);
    window.removeEventListener('scroll', updateActiveSection);
});



const features: Feature[] = [
    {
        title: 'For Students',
        description:
            'Effortlessly track your attendance, view schedules, and stay on top of your academic commitments.',
        imageUrl:
            'https://scontent.fcgy1-2.fna.fbcdn.net/v/t39.30808-6/482223881_1717679305481311_6331821698277136086_n.jpg?_nc_cat=108&ccb=1-7&_nc_sid=669761&_nc_ohc=xCQsqdmqpFkQ7kNvwGfZ5ZW&_nc_oc=AdmNf4bn6S2qRLsozcEB4BUAK7YLrAaxakUGk9RYDwKZsKzsd6luB_m9jQMlkz4QQXY&_nc_zt=23&_nc_ht=scontent.fcgy1-2.fna&_nc_gid=OUC2vBFJzvqiANx22cN8Ww&oh=00_AfjdCXTgQqozR5XA6hbh5Pe_9uW6lVHECqNvKh8IFtSkIw&oe=6930342B',
    },
    {
        title: 'For Teachers',
        description:
            'Streamline attendance taking, manage your classes, and generate reports with just a few clicks.',
        imageUrl:
            'https://www.trackinghappiness.com/wp-content/uploads/2023/01/woman-sitting-at-table-with-notepad-laughing.jpg',
    },
    {
        title: 'For Institutions',
        description:
            'Gain valuable insights into attendance trends and improve overall institutional efficiency and student success.',
        imageUrl:
            'https://th.bing.com/th/id/OIP.2_QYmZEVZuCnh5yYo7z2pQHaE_?w=265&h=180&c=7&r=0&o=7&dpr=1.6&pid=1.7&rm=3',
    },
];
const controller = new LandingPageController(slides, features);

const props = withDefaults(
  defineProps<{
    canRegister: boolean;
  }>(),
  { canRegister: true },
);
</script>

<template>
    <Head title="About Us">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
     <div
        class="font-inter flex min-h-screen flex-col items-center bg-gray-50 p-6 text-gray-800 dark:bg-gray-900 dark:text-gray-200"
    >
        <header
            class="sticky top-0 z-10 flex w-full items-center justify-between bg-gray-50 px-30 py-4 dark:bg-gray-900"
        >
    
    <AttendEaseHeader
      :controller="controller"
      :can-register="props.canRegister"
    />
        </header>
         <div
            class="font-inter flex min-h-screen flex-col items-center bg-gray-50 p-6 text-gray-800 dark:bg-gray-900 dark:text-gray-200"
        >
      
            <div class="flex min-h-screen flex-col antialiased">
                <main
                    class="mx-auto max-w-7xl flex-grow px-4 py-20 sm:px-6 lg:px-8"
                >
                    <div class="text-center">
                        <h1
                            class="mb-6 text-6xl font-extrabold tracking-tight text-gray-900 sm:text-7xl lg:text-8xl"
                        >
                            Attendance
                            <span class="primary-text">Simplified</span>.
                        </h1>
                        <p
                            class="mx-auto mt-3 max-w-2xl text-xl text-gray-500 sm:mt-5 sm:text-2xl"
                        >
                            AttendEase is the smart platform for seamless
                            student attendance tracking, teacher management, and
                            subject organization.
                        </p>

                        <div class="mt-10 flex justify-center space-x-4">
                            <a
                                href="#help-section"
                                class="inline-flex items-center justify-center rounded-xl border border-indigo-600 bg-indigo-600 
                                px-8 py-3 text-lg font-medium text-white shadow-md transition duration-300 hover:scale-105 hover:bg-indigo-700"
                            >
                                Download Our App
                            </a>
                            <a
                                href="#faq-section"
                                class="transform inline-flex items-center justify-center rounded-xl border border-gray-300
                                 bg-white px-8 py-3 text-lg font-medium text-gray-700 shadow-md transition duration-300 hover:scale-105 hover:bg-gray-50"
                            >
                                Find Quick Answers
                            </a>
                        </div>
                    </div>
                </main>

                <!-- FAQ Section -->
                <section
                    id="faq-section"
                    class="mx-auto mt-8 max-w-7xl rounded-xl bg-white px-4 py-16 shadow-lg sm:px-6 lg:px-8"
                >
                    <div class="preview-header">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">
                            Frequently Asked Questions
                        </h2>
                        <p class="text-gray-500">
                            Your quick guide to common questions about accounts,
                            setup, and features.
                        </p>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                        <!-- FAQ 1 -->
                        <div
                            class="faq-card rounded-xl border border-gray-200 p-6 shadow-md"
                        >
                            <h3 class="primary-text mb-3 text-xl font-bold">
                                How do I reset my password?
                            </h3>
                            <div class="flex items-start gap-6">
                                <p
                                    class="flex-1 text-sm leading-relaxed text-gray-600"
                                >
                                    Navigate to the sign-in page, click "Forgot
                                    Password," and enter your registered email.
                                    We will send a secure link to reset your
                                    credentials instantly.
                                </p>
                                <img
                                    :src="'https://placehold.co/128x128/6366F1/FFFFFF?text=Reset'"
                                    alt="Reset Password Flow"
                                    class="h-32 w-32 rounded-lg object-cover shadow-md"
                                />
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div
                            class="faq-card rounded-xl border border-gray-200 p-6 shadow-md"
                        >
                            <h3 class="primary-text mb-3 text-xl font-bold">
                                What devices are supported?
                            </h3>
                            <div class="flex items-start gap-6">
                                <p
                                    class="flex-1 text-sm leading-relaxed text-gray-600"
                                >
                                    AttendEase supports all major browsers on
                                    desktop and mobile apps Android only.
                                </p>
                                <img
                                    :src="'https://placehold.co/128x128/6366F1/FFFFFF?text=Android'"
                                    alt="Supported Devices"
                                    class="h-32 w-32 rounded-lg object-cover shadow-md"
                                />
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div
                            class="faq-card rounded-xl border border-gray-200 p-6 shadow-md"
                        >
                            <h3 class="primary-text mb-3 text-xl font-bold">
                                Is there a free trial period?
                            </h3>
                            <div class="flex items-start gap-6">
                                <p
                                    class="flex-1 text-sm leading-relaxed text-gray-600"
                                >
                                    Yes! We offer a 30-day free trial for all
                                    new school accounts. No credit card
                                    required.
                                </p>
                                <img
                                    :src="'https://placehold.co/128x128/6366F1/FFFFFF?text=Trial'"
                                    alt="Free Trial Information"
                                    class="h-32 w-32 rounded-lg object-cover shadow-md"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Help & Downloads Section -->
                <section
                    id="help-section"
                    class="mx-auto mt-8 max-w-7xl px-4 py-16 sm:px-6 lg:px-8"
                >
                    <div class="preview-header">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">
                            Help & Application Downloads
                        </h2>
                        <p class="text-gray-500">
                            Download our mobile apps for easy on-the-go
                            attendance management.
                        </p>
                    </div>

                    <div
                        class="flex flex-col items-center space-y-8 lg:flex-row lg:space-y-0 lg:space-x-12"
                    >
                        <!-- App Carousel -->
                        <div class="flex justify-center p-4 lg:w-1/2">
                            <div
                                id="app-carousel"
                                class="relative w-full max-w-sm overflow-hidden rounded-xl border border-gray-200 shadow-2xl"
                            >
                                <!-- Slides -->
                                <div
                                    class="carousel-inner flex transition-transform duration-500 ease-in-out"
                                    :style="{
                                        transform: `translateX(-${currentSlideIndex * 100}%)`,
                                    }"
                                >
                                    <img
                                        v-for="(slide, index) in slides"
                                        :key="index"
                                        :src="slide.src"
                                        :alt="slide.alt"
                                        class="max-h-64 w-full flex-shrink-0 object-contain p-2"
                                    />
                                </div>

                                <!-- Left Arrow -->
                                <button
                                    id="prev-slide"
                                    @click="prevSlide"
                                    class="bg-opacity-50 hover:bg-opacity-75 absolute top-1/2 left-2 -translate-y-1/2 transform rounded-full bg-gray-800 p-2 text-white transition"
                                >
                                    &#10094;
                                </button>

                                <!-- Right Arrow -->
                                <button
                                    id="next-slide"
                                    @click="nextSlide"
                                    class="bg-opacity-50 hover:bg-opacity-75 absolute top-1/2 right-2 -translate-y-1/2 transform rounded-full bg-gray-800 p-2 text-white transition"
                                >
                                    &#10095;
                                </button>

                                <!-- Dots -->
                                <div
                                    class="absolute bottom-3 left-1/2 flex -translate-x-1/2 transform space-x-2"
                                >
                                    <span
                                        v-for="(slide, index) in slides"
                                        :key="index"
                                        @click="goToSlide(index)"
                                        class="dot h-3 w-3 cursor-pointer rounded-full hover:bg-gray-700"
                                        :class="{
                                            'bg-gray-700':
                                                currentSlideIndex === index,
                                            'bg-gray-400':
                                                currentSlideIndex !== index,
                                        }"
                                    >
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links & Support -->
                        <div class="p-4 lg:w-1/2">
                            <h3
                                class="primary-text mb-4 text-2xl font-semibold"
                            >
                                Quick Links & Support
                            </h3>
                            <p class="mb-6 text-gray-600">
                                Access our detailed knowledge base or download
                                the dedicated mobile apps for fast, reliable
                                attendance logging in the classroom.
                            </p>

                            <div class="mb-8 flex flex-wrap gap-4">
                                <a
                                    href="https://github.com/jaysonbutawan/AttendEase"
                                    class="inline-flex items-center space-x-3 rounded-lg bg-gray-900 p-3 text-white transition duration-150 hover:bg-gray-700"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm2.25 14.5L12 12.25l-2.25 4.25H7.5l4.5-8.5 4.5 8.5h-2.25z"
                                        />
                                    </svg>
                                    <span class="font-medium"
                                        >Download Android App</span
                                    >
                                </a>
                            </div>

                            <a
                                href="#"
                                class="primary-text inline-block text-sm font-medium hover:underline"
                            >
                                View Comprehensive User Guides &rarr;
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Contact Us Section -->
                <section
                    id="contacts-section"
                    class="mx-auto mt-8 max-w-7xl rounded-xl bg-white px-4 py-16 shadow-lg sm:px-6 lg:px-8"
                >
                    <div class="preview-header">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">
                            Get In Touch
                        </h2>
                        <p class="text-gray-500">
                            Reach out directly to our contact personnel or send
                            us a message.
                        </p>
                    </div>

                    <div
                        class="mx-auto flex max-w-5xl flex-col gap-12 md:flex-row"
                    >
                        <!-- Left Column: Contact Personnel -->
                        <div class="flex flex-col gap-6 md:w-1/2">
                            <!-- Contact 1 -->
                            <div
                                class="flex w-full items-center space-x-4 rounded-lg border border-gray-200 p-4 shadow-sm transition hover:shadow-md"
                            >
                                <svg
                                    class="h-8 w-8 flex-shrink-0 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v6m-8-6v6m4-6v6"
                                    />
                                </svg>
                                <div class="flex flex-col text-gray-800">
                                    <p class="flex items-center font-semibold">
                                        <svg
                                            class="mr-1 h-5 w-5 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                            />
                                        </svg>
                                        RODGIE A. FEDERIO
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M3 5.25C3 4.01 4.01 3 5.25 3h13.5C19.99 3 21 4.01 21 5.25v13.5c0 1.24-1.01 2.25-2.25 2.25H5.25C4.01 21 3 19.99 3 18.75V5.25zM12 12l8-5V18H4V7l8 5z"
                                            />
                                        </svg>
                                        09811453289
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18a2 2 0 002 2h16a2 2 0 002-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                            />
                                        </svg>
                                        rod101004@gmail.com
                                    </p>
                                </div>
                            </div>

                            <!-- Contact 2 -->
                            <div
                                class="flex w-full items-center space-x-4 rounded-lg border border-gray-200 p-4 shadow-sm transition hover:shadow-md"
                            >
                                <svg
                                    class="h-8 w-8 flex-shrink-0 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v6m-8-6v6m4-6v6"
                                    />
                                </svg>
                                <div class="flex flex-col text-gray-800">
                                    <p class="flex items-center font-semibold">
                                        <svg
                                            class="mr-1 h-5 w-5 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                            />
                                        </svg>
                                        JAYSON D. BUTAWAN
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M3 5.25C3 4.01 4.01 3 5.25 3h13.5C19.99 3 21 4.01 21 5.25v13.5c0 1.24-1.01 2.25-2.25 2.25H5.25C4.01 21 3 19.99 3 18.75V5.25zM12 12l8-5V18H4V7l8 5z"
                                            />
                                        </svg>
                                        09764001459
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18a2 2 0 002 2h16a2 2 0 002-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                            />
                                        </svg>
                                        jaysonbutawan2@gmail.com
                                    </p>
                                </div>
                            </div>

                            <!-- Contact 3: You -->
                            <div
                                class="flex w-full items-center space-x-4 rounded-lg border border-gray-200 p-4 shadow-sm transition hover:shadow-md"
                            >
                                <svg
                                    class="h-8 w-8 flex-shrink-0 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v6m-8-6v6m4-6v6"
                                    />
                                </svg>
                                <div class="flex flex-col text-gray-800">
                                    <p class="flex items-center font-semibold">
                                        <svg
                                            class="mr-1 h-5 w-5 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                            />
                                        </svg>
                                        JOBERT P. NOYAD
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M3 5.25C3 4.01 4.01 3 5.25 3h13.5C19.99 3 21 4.01 21 5.25v13.5c0 1.24-1.01 2.25-2.25 2.25H5.25C4.01 21 3 19.99 3 18.75V5.25zM12 12l8-5V18H4V7l8 5z"
                                            />
                                        </svg>
                                        09911543308
                                    </p>
                                    <p class="flex items-center text-sm">
                                        <svg
                                            class="mr-1 h-4 w-4 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18a2 2 0 002 2h16a2 2 0 002-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                            />
                                        </svg>
                                        jobertnoyad93@gmail.com
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Send a Message Form -->
                        <div class="md:w-1/2">
                            <div class="rounded-xl bg-indigo-50 p-6 shadow-lg">
                                <h3
                                    class="mb-4 text-xl font-semibold text-gray-800"
                                >
                                    Send Us a Message
                                </h3>
                                <form
                                    @submit="handleContactSubmit"
                                    class="space-y-4"
                                >
                                    <input
                                        type="text"
                                        placeholder="Your Name"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    />
                                    <input
                                        type="email"
                                        placeholder="Your Email"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    />
                                    <textarea
                                        placeholder="Your Message"
                                        rows="4"
                                        required
                                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                    ></textarea>
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-semibold text-white shadow-lg transition duration-150 hover:bg-indigo-700"
                                    >
                                        Submit Inquiry
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- About Us Section -->
                <section
                    id="about-section"
                    class="mx-auto mt-8 max-w-7xl px-4 py-16 sm:px-6 lg:px-8"
                >
                    <div class="preview-header">
                        <h2 class="mb-2 text-3xl font-bold text-gray-900">
                            About AttendEase
                        </h2>
                        <p class="text-gray-500">
                            Learn about our mission to revolutionize school
                            administration and attendance tracking.
                        </p>
                    </div>

                    <div
                        class="mx-auto flex max-w-6xl flex-col gap-12 rounded-xl bg-white p-8 shadow-lg lg:flex-row-reverse"
                    >
                        <div class="flex justify-center p-4 lg:w-1/2">
                            <img
                                :src="'https://placehold.co/800x600/6366F1/FFFFFF?text=AttendEase+Team'"
                                alt="Photo of the AttendEase core development team"
                                class="h-auto w-full rounded-xl border border-gray-200 shadow-2xl"
                            />
                        </div>

                        <div class="space-y-6 p-4 lg:w-1/2">
                            <h3
                                class="primary-text mb-4 text-2xl font-semibold"
                            >
                                System Overview
                            </h3>
                            <p class="text-gray-600">
                                The AttendEase System is an Android mobile
                                application developed using Kotlin, designed for
                                both teachers and students to streamline
                                attendance management. It integrates Firebase
                                Authentication for secure user login, Firebase
                                Realtime Database for storing schedules,
                                attendance, and reports, and ZXing for QR code
                                generation and scanning. Classroom locations are
                                validated using FusedLocationProviderClient,
                                which compares student GPS coordinates against
                                pre-defined classroom geofenced boundaries.
                            </p>

                            <h3
                                class="primary-text mb-4 border-t border-gray-100 pt-4 text-2xl font-semibold"
                            >
                                Acknowledgements
                            </h3>
                            <p class="text-gray-600">
                                A big thank you goes to our co-researcher for
                                their hard work, valuable help, and dedication.
                                Without their contribution, this project
                                wouldn’t have been the same. We’re also thankful
                                to our friends and family for always supporting
                                us, encouraging us, and understanding us. Their
                                help kept us going through the tough times.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>

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

<style scoped>
/* ------------------ Custom Styles from Original HTML ------------------ */
:root {
    /* Define CSS variables based on Tailwind/original colors for easier use in custom CSS */
    --primary-color-main: rgb(79 70 229); /* Indigo 600 */
    --primary-color-hover: rgb(67 56 202); /* Indigo 700 */
    --background-color: #f7f9fb;
}

body {
    background-color: var(--background-color);
}

.primary-color {
    background-color: var(--primary-color-main);
}
.primary-text {
    color: var(--primary-color-main);
}

/* ------------------ Navigation & Animation Effects ------------------ */

header {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* Subtle shadow for depth */
}

/* Main Nav Link Hover/Active Effect */
.nav-item {
    position: relative;
    padding: 8px 16px;
    transition:
        color 0.2s ease-in-out,
        background-color 0.2s ease-in-out,
        transform 0.15s;
    height: 100%;
    display: flex;
    align-items: center;
    border-radius: 8px;
}

/* Pressed/Active Click Effect */
.nav-item:active {
    transform: scale(0.98);
    background-color: rgba(79, 70, 229, 0.1);
}

/* Underline and Scale effect on hover */
.nav-item:hover {
    color: var(--primary-color-main);
    transform: scale(1.03);
}

.nav-item::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 0;
    width: 0;
    height: 2px;
    background-color: var(--primary-color-main);
    transition:
        width 0.3s ease-in-out,
        left 0.3s ease-in-out;
}
.nav-item:hover::after {
    width: 100%;
    left: 0;
}

/* Active link styling */
.nav-item.active {
    font-weight: 600; /* Semi-bold */
    color: var(--primary-color-main);
}
.nav-item.active::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 2px;
    background-color: var(--primary-color-main);
}

/* Button Hover Effect */
.primary-button {
    transition: all 0.2s ease-in-out;
}
.primary-button:hover {
    background-color: var(--primary-color-hover); 
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -2px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.inline-form-field {
    width: 150px; 
}
.inline-signin-button {
    height: 40px; 
}

.desktop-flex-grow {
    flex-grow: 1;
}

.preview-header {
    margin-bottom: 2rem;
    text-align: center;
}

.faq-card {
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.faq-card:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.faq-card:active {
    transform: scale(0.97);
}

#app-carousel img {
    padding: 0.5rem;
}
.carousel-inner {
    display: flex;
    width: 100%;
}
</style>
